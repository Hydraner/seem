<?php

/**
 * @file
 * Contains \Drupal\seem\Plugin\SeemDisplay\SeemDisplayBase.
 */

namespace Drupal\seem\Plugin\SeemDisplay;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Url;
use Drupal\layout_plugin\Layout;

/**
 * Provides a base class for Layout plugins.
 *
 * @todo: Adjust documentation since this is all geklaut.
 */
abstract class SeemDisplayBase extends PluginBase implements SeemDisplayInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * The layout configuration.
   *
   * @var array
   */
  protected $configuration = [];
  protected $processedRegions;
  protected $mainContent;
  protected $layout;

  /**
   * Gets the human-readable name.
   *
   * @return \Drupal\Core\Annotation\Translation|NULL
   *   The human-readable name.
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * Gets the optional description for advanced layouts.
   *
   * @return \Drupal\Core\Annotation\Translation|NULL
   *   The layout description.
   */
  public function getDescription() {
    return isset($this->pluginDefinition['description']) ? $this->pluginDefinition['description'] : NULL;
  }

  public function getContext() {
    return $this->pluginDefinition['context'];
  }

  public function getLayout() {
    return $this->layout;
  }
  public function setLayout($layout) {
    $this->layout = $layout;
    return $this;
  }

  /**
   * Gets information on regions keyed by machine name.
   *
   * @return array
   *   An array of information on regions keyed by machine name.
   */
  public function getRegionDefinitions() {
    return $this->pluginDefinition['regions'];
  }

  public function setMainContent($main_content) {
    $this->mainContent = $main_content;
  }

  public function getMainContent() {
    return $this->mainContent;
  }

  // @todo: Move this to seemDisplayBase
  public function getProcessedRegions() {
    $regions = [];
    foreach ($this->getRegionDefinitions() as $region_key => $region_definition) {
      $regions[$region_key] = $this->processRegion($region_definition, $region_key);
//      $build[$region_key]['#theme_wrappers'][] = 'region';
//      $build[$region_key]['#region'] = $region_key;
//      $build[$region_key]['#sorted'] = TRUE;
    }
    return $regions;
  }

  // @todo: Move this to seemDisplayBase
  public function processRegion($region_definition, $region_key) {
    $region = [];
    $debug = 1;
    foreach ($region_definition as $content) {
      if (\Drupal::getContainer()
        ->get('plugin.manager.seem_renderable.processor')
        ->hasDefinition($content['type'])
      ) {
        $seem_renderable = \Drupal::getContainer()
          ->get('plugin.manager.seem_renderable.processor')
          ->createInstance($content['type'], ['region_key' => $region_key]);
        $renderable = $seem_renderable->doRenderable($content, $this);
        $region[] = $renderable;
      }
    }

    return $region;
  }

  /**
   * {@inheritdoc}
   *
   */
  public function build() {
    // @todo: make this more pretty :) But it works!
    $regions = $this->getProcessedRegions();
    if (isset($this->pluginDefinition['layout'])) {
      $layout_plugin_manager = Layout::layoutPluginManager();
      if ($layout_plugin_manager->hasDefinition($this->pluginDefinition['layout'])) {
        $layout = $layout_plugin_manager->createInstance($this->pluginDefinition['layout']);
        $build = $layout->build($regions);
      }
    }
    else {
      $build = $regions;
    }


    // If no region was set, we will render the main content.
    if (empty($build)) {
      $build['content'][] = $this->getMainContent();
    }
    $build['#display'] = $this->getPluginDefinition();
    $build['#settings'] = $this->getConfiguration();
//    if ($theme = $this->getThemeHook()) {
//      $build['#theme'] = $theme;
//    }
//    if ($library = $this->getLibrary()) {
//      $build['#attached']['library'][] = $library;
//    }

    $seem_displayable = \Drupal::getContainer()
      ->get('plugin.manager.seem_displayable.processor')
      ->createInstance($build['#display']['seem_displayable']);


//    $seem_displayable_base_path = $seem_displayable->getBasePath($this->getContext());

    // @todo: Add configuration link with context @see https://www.previousnext.com.au/blog/understanding-drupal-8s-modal-api-and-dialog-controller

// Build link for dialog.
//    $link_url = Url::fromRoute('entity.seem_display.config_form', array(

    // @todo: Get config entity for context. Use parameters and display for context.
//    $seem_display_entity = entity_load();
//    $query = \Drupal::entityQuery('seem_display');
    // @todo: Context per plugin. this just works for entities.
    $parameters = \Drupal::routeMatch()->getRawParameters()->all();
    $display = $build['#display'];
    $query = \Drupal::entityQuery('seem_display');
    foreach ($parameters as $key => $value) {
      $query->condition("parameters.$key", $value);
    }
    if (empty($parameters)) {
      $query->condition("parameters", NULL, 'IS NULL');
    }
    foreach ($display['context'] as $key => $value) {
      $query->condition("context.$key", $value);
    }

    $seem_display_id = $query->execute();
    $entity_id = !empty($seem_display_id) ? key($query->execute()) : '';
    if (!empty($entity_id)) {
      $type = 'edit_form';
    }
    else {
      $type = 'add_form';
    }

    $link_url = Url::fromRoute('entity.seem_display.' . $type, array(
        'seem_display' => $entity_id,
        'parameters' => Json::encode($parameters),
        'display' => Json::encode(['display' => $display]),
      )
    );
    $link_url->setOptions(array(
        'attributes' => array(
          'class' => array('use-ajax'),
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode(array(
            'width' => 700,
          )),
        ))
    );
    // Magic link.
    // @todo: We need better UX for that.
    $link_add_unit_display_name = \Drupal::l('Create unit display name', $link_url);

    $build['content'][] = [
      '#markup' => $link_add_unit_display_name
    ];

    return $build;
  }

  // This only needs to be done in a page/existing_page context.
  public function pageBuild($build) {
    // @todo: Reuse processRegion.
    foreach ($this->getExistingRegions() as $region_key => $region_definition) {
      foreach ($region_definition as $content) {
        if (\Drupal::getContainer()
          ->get('plugin.manager.seem_renderable.processor')
          ->hasDefinition($content['type'])
        ) {
          $seem_renderable = \Drupal::getContainer()
            ->get('plugin.manager.seem_renderable.processor')
            ->createInstance($content['type'], ['region_key' => $region_key]);
          $renderable = $seem_renderable->doRenderable($content, $this);
          $build[$region_key][] = $renderable;
          $seem_renderable->doExtraTasks($build);
        }
      }
    }
    
    return $build;
  }

  public function getExistingRegions() {
    if (isset($this->pluginDefinition['existing_regions'])) {
      return $this->pluginDefinition['existing_regions'];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   *
   * @todo: Add the possiblitly to deactivate the display.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Hirarchy: SeemDisplayBaseDefault -> SeemDisplayableBaseDefault -> SemmDisplayCustomDisplay


    // @todo: layout integration.
//    $form['layout_plugin'] = [
//
//    ];

    // @todo: get default config forms form displayables.
//    foreach ($this->seemDisplayablePluginManager->getDefinitions() as $definition) {
//      $form += $definition->getConfigurationFormDefault();
//    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
    // @todo: Add the possibilty to add defaultConfiguration().
    return array_merge($this->defaultConfiguration(), $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   *
   * @todo: Think about how to usefully use this.
   */
  public function calculateDependencies() {
    // TODO: Implement calculateDependencies() method.
    return [];
  }

}

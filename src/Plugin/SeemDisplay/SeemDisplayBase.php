<?php

/**
 * @file
 * Contains \Drupal\seem\Plugin\SeemDisplay\SeemDisplayBase.
 */

namespace Drupal\seem\Plugin\SeemDisplay;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;

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

//  public function setProcessedRegions($regions) {
//    $this->processedRegions = $regions;
//  }

//  public function getProcessedRegions() {
//    return $this->processedRegions;
//  }

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
//  public function hasContext($context) {
//    $debug = 1;
//  }


  /**
   * {@inheritdoc}
   *
   * @todo: This MUST be cached.
   */
  public function build() {
    $build = $this->getProcessedRegions();
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
    return array_merge($this->defaultConfiguration(), $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

  public function calculateDependencies() {
    // TODO: Implement calculateDependencies() method.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

}

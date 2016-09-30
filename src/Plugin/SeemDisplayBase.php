<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for Layout plugins.
 */
abstract class SeemDisplayBase extends PluginBase implements SeemDisplayInterface, ConfigurablePluginInterface, PluginFormInterface, ContainerFactoryPluginInterface {

  /**
   * The display configuration.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * The layout if available and selected.
   *
   * @var bool|string
   */
  protected $layout = FALSE;

  /**
   * Optional settings for the layout.
   *
   * @var array
   */
  protected $layoutSettings = [];

  /**
   * The context's render array.
   *
   * @var array
   */
  protected $mainContent;

  /**
   * The seem renderable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemRenderableManager
   */
  protected $seemRenderableManager;

  /**
   * The layout manager.
   *
   * @var \Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface
   */
  protected $layoutManager;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\seem\Plugin\SeemRenderableManager $seem_renderable_manager
   *   The seem renderable plugin manager.
   * @param \Drupal\layout_plugin\Plugin\Layout\LayoutPluginManagerInterface $layout_manager
   *   Layout manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SeemRenderableManager $seem_renderable_manager, LayoutPluginManagerInterface $layout_manager) {
    $this->seemRenderableManager = $seem_renderable_manager;
    $this->layoutManager = $layout_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->layout = isset($this->pluginDefinition['layout']) ? $this->pluginDefinition['layout'] : $this->layout;
    $this->layoutSettings = isset($this->pluginDefinition['layout_settings']) ? $this->pluginDefinition['layout_settings'] : $this->layoutSettings;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.seem_renderable.processor'),
      $container->get('plugin.manager.layout_plugin')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return isset($this->pluginDefinition['description']) ? $this->pluginDefinition['description'] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setMainContent($main_content) {
    $this->mainContent = $main_content;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMainContent() {
    return $this->mainContent;
  }

  /**
   * {@inheritdoc}
   */
  public function setLayout($layout) {
    $this->layout = $layout;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * {@inheritdoc}
   */
  public function setLayoutSetting($key, $value) {
    $this->layoutSettings[$key] = $value;
    return $this->layoutSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function setLayoutSettings($layout_settings) {
    foreach ($layout_settings as $key => $value) {
      $this->setLayoutSetting($key, $value);
    }
    return $this->layoutSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayoutSettings() {
    return $this->layoutSettings;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegionDefinitions() {
    return isset($this->pluginDefinition['regions']) ? $this->pluginDefinition['regions'] : [];
  }

  /**
   * {@inheritdoc}
   */
  public function setRegionDefinitions($regions) {
    $this->pluginDefinition['regions'] = $regions;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function processRegion($region_definition, $region_key, &$build = []) {
    $region = [];
    foreach ($region_definition as $content) {
      // Check if their is a matching seem renderable.
      if ($this->seemRenderableManager->hasDefinition($content['type'])) {
        // Create an seemRenderable instance for the given region.
        $seem_renderable = $this->seemRenderableManager->createInstance($content['type'], ['region_key' => $region_key]);
        $region[] = $seem_renderable->doRenderable($content, $this);
        if (!empty($build)) {
          $seem_renderable->doExtraTasks($build);
        }
      }
      else {
        // @todo: Return exception that the renderable type does not exists.
      }
    }

    return $region;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessedRegions() {
    $regions = [];
    foreach ($this->getRegionDefinitions() as $region_key => $region_definition) {
      $regions[$region_key] = $this->processRegion($region_definition, $region_key);
    }

    return $regions;
  }

  /**
   * {@inheritdoc}
   */
  public function processLayout($regions, $layout, $configuration = []) {
    if ($this->layoutManager->hasDefinition($layout)) {
      $layout = $this->layoutManager->createInstance($layout, []);

      // Add support for default settings in layouts.
      $default_settings = isset($layout->pluginDefinition['settings']) ? $layout->pluginDefinition['settings'] : [];
      $layout->setConfiguration(array_merge($default_settings, $configuration));

      return $layout->build($regions);
    }
    else {
      // @todo: Return exception information if no definition exists.
      return $regions;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $regions = $this->getProcessedRegions();

    // Process layout if available.
    if ($this->layout) {
      $build = $this->processLayout($regions, $this->layout, $this->layoutSettings);
    }
    else {
      $build = $regions;
    }

    // If no region was set, we will fallback to the main content.
    if (empty($build)) {
      $build['content'][] = $this->getMainContent();
    }

    // Add the display information to the build array for later usage if needed.
    $build['#display'] = $this->getPluginDefinition();

    // Add display specific configuration link.
    $this->addContextualLinks($build);

    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * This only needs to be done in a page/existing_page context.
   */
  public function pageBuild($build) {
    foreach ($this->getExistingRegionsDefinition() as $region_key => $region_definition) {
      $build[$region_key] = $this->processRegion($region_definition, $region_key, $build);
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getExistingRegionsDefinition() {
    if (isset($this->pluginDefinition['existing_regions'])) {
      return $this->pluginDefinition['existing_regions'];
    }
    return [];
  }

  /**
   * Adds the configure display link to the build array.
   *
   * @param array $build
   *   A build array.
   */
  public function addContextualLinks(&$build) {

    if (isset($this->pluginDefinition['context'])) {
      $build['#attributes']['class'][] = 'seem-region';
      // @todo: Get config entity for context. Use parameters and display for context.
      // @todo: Context per plugin. this just works for entities.
      $parameters = \Drupal::routeMatch()->getRawParameters()->all();
      $display = $this->getPluginDefinition();
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
        $route_name = 'entity.seem_display.edit_form';
      }
      else {
        $route_name = 'entity.seem_display.add_form';
      }

      $build['#contextual_links'] = array(
        'seem' => array(
          'route_parameters' => ['seem_display' => $entity_id],
          'metadata' => [
            'parameters' => $parameters,
            'display' => $display,
            'route' => $route_name
          ]
        ),
      );

      $layout = $this->layoutManager->createInstance('seem_wrapper', []);
      $build = $layout->build($build);
    }
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

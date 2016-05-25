<?php

namespace Drupal\seem;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\seem\Plugin\Discovery\SuggestionYamlDiscovery;

/**
 * Provides the default seem_display manager.
 */
class SeemDisplayManager extends DefaultPluginManager implements SeemDisplayManagerInterface {

  /**
   * Provides default values for all seem_display plugins.
   *
   * @var array
   */
  protected $defaults = array(
    // Add required and optional plugin properties.
    'id' => '',
    'label' => '',
    'path' => '',
    'regions' => array(),
  );

  /**
   * Constructs a SeemDisplayManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, CacheBackendInterface $cache_backend) {
    // Add more services as required.
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;
    $this->setCacheBackend($cache_backend, 'seem_display', array('seem_display'));
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $directories = array_merge($this->moduleHandler->getModuleDirectories(), $this->themeHandler->getThemeDirectories());

//      $plugin_manager = \Drupal::service('plugin.manager.layoutable.processor');
//      $suggestions = array();
//      foreach ($plugin_manager->getDefinitions() as $plugin_id => $definition) {
//        $suggestions += $plugin_manager->createInstance($plugin_id)->getSuggestions();
//      }
      $debug = 1;
      // @todo: Build own Discovery to discover plugins by theme_hook_suggestion.
      $this->discovery = new SuggestionYamlDiscovery($directories, 'seem.display.plugin');

//      $this->discovery->addTranslatableProperty('label', 'label_context');
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // You can add validation of the plugin definition here.
    if (empty($definition['id'])) {
      throw new PluginException(sprintf('Example plugin property (%s) definition "is" is required.', $plugin_id));
    }
  }

  // Add other methods here as defined in the SeemDisplayManagerInterface.

}

<?php

namespace Drupal\seem;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDirectoryDiscovery;
use Drupal\seem\Plugin\SeemLayoutableManager;

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
    'id' => FALSE,
    'label' => '',
    'path' => FALSE,
    'regions' => array(),
  );

  /**
   * The SeemLayoutable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemLayoutableManager
   */
  protected $seemLayoutablePluginManager;

  /**
   * The theme handler to invoke the alter hook.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * Constructs a SeemDisplayManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\seem\Plugin\SeemLayoutableManager $seem_layoutable_plugin_manager
   *   The seem layoutable plugin manager.
   */
  public function __construct(ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, CacheBackendInterface $cache_backend, SeemLayoutableManager $seem_layoutable_plugin_manager) {
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;
    $this->seemLayoutablePluginManager = $seem_layoutable_plugin_manager;
    $this->setCacheBackend($cache_backend, 'seem_display', array('seem_display'));
  }


  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $directories = array_merge($this->moduleHandler->getModuleDirectories(), $this->themeHandler->getThemeDirectories());

      // Make the discovery search in /layout directory.
      foreach ($directories as &$directory) {
        $directory = $directory . '/display';
      }

      // Since we extract the id from the file_name, we use the 'label' as $key.
      $this->discovery = new YamlDirectoryDiscovery($directories, 'seem.display.plugin', 'label');
      $this->discovery->addTranslatableProperty('label', 'label_context');
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }

    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    $basename = basename($definition['_discovered_file_path']);
    $basename_fragments = explode('.', $basename);
    $definition['id'] = $basename_fragments[0];
    $definition['seem_layoutable'] = $basename_fragments[1];

    // @todo: Add the possibility to validate the id per seem_layoutable.
    if (!$this->seemLayoutablePluginManager->hasDefinition($definition['seem_layoutable'])) {
      throw new PluginException(sprintf('Seem layoutable "%s" does not exist (defined in %s).', $definition['seem_layoutable'], $definition['_discovered_file_path']));
    }
  }
}

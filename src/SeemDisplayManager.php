<?php

namespace Drupal\seem;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\Cache;
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
   * Cached definitions array, keyed by seem_layoutbale.
   *
   * @var array
   */
  protected $definitionsBySeemLayoutable;

  /**
   * An alternative cache key which we use to cache Definitions, keyed by
   * seem_layoutable.
   *
   * @var string
   */
  protected $cacheKeyBySeemLayoutable;

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
    $this->setCacheBackend($cache_backend, 'seem_display', array('seem_display'), 'seem_display:by_seem_layoutable');
  }

  /**
   * {@inheritdoc}
   *
   * @param string
   *   Custom cache_key for plugin definitions keyed by seem_layoutable.
   */
  public function setCacheBackend(CacheBackendInterface $cache_backend, $cache_key, array $cache_tags = array(), $cache_key_seem_layoutable = NULL) {
    parent::setCacheBackend($cache_backend, $cache_key, $cache_tags);
    $this->cacheKeyBySeemLayoutable = $cache_key_seem_layoutable;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      // Define themes as additional plugin source.
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
   * Determines if the provider of a definition exists. Since we support themes
   * and modules as a provider, we need to check both sources.
   *
   * @return bool
   *   TRUE if provider exists, FALSE otherwise.
   */
  protected function providerExists($provider) {
    return $this->moduleHandler->moduleExists($provider) || $this->themeHandler->themeExists($provider);
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

  /**
   * Returns plugin definitions of the decorated discovery class, grouped by a
   * given seem_layoutable plugin_id.
   *
   * @param $seem_layoutable_plugin_id
   *   The seem_layoutable plugin id.
   * @return array|null
   *   An array of plugin definitions.
   */
  public function getDefinitionsBySeemLayoutable($seem_layoutable_plugin_id) {
    $definitions_by_seem_layoutable = $this->getCachedDefinitionsBySeemLayoutable($seem_layoutable_plugin_id);
    if (!isset($definitions_by_seem_layoutable)) {
      $definitions = $this->getDefinitions();
      $definitions_by_seem_layoutable = [];
      foreach ($definitions as $plugin_id => $plugin_definition) {
        $definitions_by_seem_layoutable[$plugin_definition['seem_layoutable']][$plugin_definition['id']] = $plugin_definition;
      }
      $this->setCachedDefinitionsBySeemLayoutable($definitions_by_seem_layoutable);
    }
    return $definitions_by_seem_layoutable;
  }

  /**
   * Returns the cached plugin definitions of the decorated discovery class,
   * grouped by seem_layoutable plugin_id.
   *
   * @param $seem_layoutable_plugin_id
   *   The seem_layoutable plugin id.
   * @return array|null
   *   On success this will return an array of plugin definitions.
   */
  protected function getCachedDefinitionsBySeemLayoutable($seem_layoutable_plugin_id) {
    if (!isset($this->definitionsBySeemLayoutable) && $cache = $this->cacheGet($this->cacheKeyBySeemLayoutable)) {
      $this->definitionsBySeemLayoutable = $cache->data;
    }
    return $this->definitionsBySeemLayoutable[$seem_layoutable_plugin_id];
  }

  /**
   * Sets a cache of plugin definitions for the decorated discovery class,
   * grouped by seem_layoutable plugin_id.
   *
   * @param array $definitions_by_seem_layoutable
   *   List of definitions to store in cache.
   */
  protected function setCachedDefinitionsBySeemLayoutable($definitions_by_seem_layoutable) {
    $this->cacheSet($this->cacheKeyBySeemLayoutable, $definitions_by_seem_layoutable, Cache::PERMANENT, $this->cacheTags);
    $this->definitionsBySeemLayoutable = $definitions_by_seem_layoutable;
  }

}

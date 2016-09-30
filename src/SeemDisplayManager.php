<?php

namespace Drupal\seem;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\seem\Plugin\Discovery\YamlDirectoryDiscoveryDecorator;
use Drupal\seem\Plugin\SeemDisplayableInterface;
use Drupal\seem\Plugin\SeemDisplayableManager;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

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
   * The SeemDisplayable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemDisplayableManager
   */
  protected $seemDisplayablePluginManager;

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
  protected $definitionsBySeemDisplayable;

  /**
   * An alternative cache key.
   *
   * Which we use to cache Definitions, keyed by seem_displayable.
   *
   * @var string
   */
  protected $cacheKeyBySeemDisplayable;

  /**
   * Constructs a SeemDisplayManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\seem\Plugin\SeemDisplayableManager $seem_displayable_plugin_manager
   *   The seem Displayable plugin manager.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, ThemeHandlerInterface $theme_handler, SeemDisplayableManager $seem_displayable_plugin_manager) {
    $plugin_interface = 'Drupal\seem\Plugin\SeemDisplayInterface';
    $plugin_definition_annotation_name = 'Drupal\seem\Annotation\SeemDisplay';
    parent::__construct("Plugin/SeemDisplay", $namespaces, $module_handler, $plugin_interface, $plugin_definition_annotation_name);
    $this->seemDisplayablePluginManager = $seem_displayable_plugin_manager;
    $this->moduleHandler = $module_handler;
    $this->themeHandler = $theme_handler;

    $discovery = new AnnotatedClassDiscovery($this->subdir, $this->namespaces, $this->pluginDefinitionAnnotationName, $this->additionalAnnotationNamespaces);
    $this->discovery = new ContainerDerivativeDiscoveryDecorator($discovery);

    $this->discovery = new YamlDirectoryDiscoveryDecorator($this->discovery, $this->getDiscoveryDirectories(), 'seem.display.plugin', 'label');

    $this->defaults += array(
      'type' => 'page',
      // Used for plugins defined in layouts.yml that do not specify a class
      // themselves.
      'class' => 'Drupal\seem\Plugin\SeemDisplayDefault',
    );

    $this->setCacheBackend($cache_backend, 'seem_display');
    $this->alterInfo('seem_display');
  }

  /**
   * {@inheritdoc}
   *
   * @param CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param string $cache_key
   *   Custom cache_key for plugin definitions keyed by seem_displayable.
   * @param array $cache_tags
   *   An array of cache tags.
   * @param \Drupal\seem\Plugin\SeemDisplayableInterface $cache_key_seem_displayable
   *   The displayable.
   */
  public function setCacheBackend(CacheBackendInterface $cache_backend, $cache_key, array $cache_tags = array(), SeemDisplayableInterface $cache_key_seem_displayable = NULL) {
    parent::setCacheBackend($cache_backend, $cache_key, $cache_tags);
    $this->cacheKeyBySeemDisplayable = $cache_key_seem_displayable;
  }

  /**
   * Build the directory index.
   *
   * The yaml discovery will use to find the yaml plugin definitions.
   */
  protected function getDiscoveryDirectories() {
    // Define themes as additional plugin source.
    $directories = array_merge($this->moduleHandler->getModuleDirectories(), $this->themeHandler->getThemeDirectories());

    // Make the discovery search in /seem_display directory.
    // @todo: Make this search recusively.
    foreach ($directories as &$directory) {
      $directory = $directory . '/seem_display';
    }

    return $directories;
  }

  /**
   * Determines if the provider of a definition exists.
   *
   * Since we support themes and modules as a provider, we need to check both
   * sources.
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
  protected function alterDefinitions(&$definitions) {
    $new_definitions = [];
    // Adjust the array_key to match the new plugin id.
    foreach ($definitions as $old_plugin_id => $definition) {
      $new_definitions[$definition['id']] = $definition;
    }
    $definitions = $new_definitions;

    parent::alterDefinitions($definitions);
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // @todo: Find a better way to do this. Maybe the seem_displayable should know this.
    if (isset($definition['_discovered_file_path'])) {
      $basename = basename($definition['_discovered_file_path']);
      $basename_fragments = explode('.seem_display.yml', $basename);
      $basename_fragments = explode('.', $basename_fragments[0]);
      $count = count($basename_fragments);
      $definition['seem_displayable'] = $basename_fragments[$count - 1];
      $basename_fragments = explode('.' . $basename_fragments[$count - 1] . '.seem_display.yml', $basename);
      $definition['id'] = $basename_fragments[0];
    }

    // Generate a route name if we have a path given and store the path to
    // generate the route in the route subscriber. We only want to use the route
    // as a context.
    if (isset($definition['context']['path']) && !isset($definition['context']['route'])) {
      $definition['path'] = $definition['context']['path'];
      unset($definition['context']['path']);
      $definition['context']['route'] = "seem.display_" . $definition['id'];
    }

    // @todo: Get context dependencies from seem_displayable.
    // @todo: Add the possibility to validate the id per seem_displayable.
    if (!$this->seemDisplayablePluginManager->hasDefinition($definition['seem_displayable'])) {
      throw new PluginException(sprintf('Seem Displayable "%s" does not exist (defined in %s).', $definition['seem_displayable'], $definition['_discovered_file_path']));
    }
  }

  /**
   * Get the definition by context.
   *
   * @param mixed $context
   *   The context.
   * @param string $seem_displayable_plugin_id
   *   The plugin id.
   *
   * @return bool|mixed
   *   A definition or FALSE.
   */
  public function getDefinitionByContext($context, $seem_displayable_plugin_id = NULL) {
    if ($seem_displayable_plugin_id) {
      $definitions = $this->getDefinitionsBySeemDisplayable($seem_displayable_plugin_id);
    }
    else {
      $definitions = $this->getDefinitions();
    }

    foreach ($definitions as $definition) {
      if ($definition['context'] == $context) {
        return $definition;
      }
    }
    return FALSE;
  }

  /**
   * Recursive find.
   *
   * @param array $array
   *   An array of found items.
   * @param string $needle
   *   A string to search for.
   *
   * @return array
   *   An array of the items found.
   */
  public function recursiveFind(array $array, $needle) {
    $iterator  = new RecursiveArrayIterator($array);
    $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
    $items = array();
    foreach ($recursive as $key => $value) {
      if ($key === $needle) {
        array_push($items, $value);
      }
    }
    return $items;
  }

  /**
   * Returns plugin definitions of the decorated discovery class.
   *
   * Grouped by a given seem_displayable plugin_id.
   *
   * @param string $seem_displayable_plugin_id
   *   The seem_displayable plugin id.
   *
   * @return array|null
   *   An array of plugin definitions.
   */
  public function getDefinitionsBySeemDisplayable($seem_displayable_plugin_id) {
    $definitions_by_seem_displayable = $this->getCachedDefinitionsBySeemDisplayable($seem_displayable_plugin_id);
    if (!isset($definitions_by_seem_displayable)) {
      $definitions = $this->getDefinitions();
      $definitions_by_seem_displayable = [];
      foreach ($definitions as $plugin_id => $plugin_definition) {
        // @todo: THIS MUST BE CACHED FOR getDefinitions TOO!
        $plugin_definition['original_plugin_id'] = $plugin_id;
        $definitions_by_seem_displayable[$plugin_definition['seem_displayable']][$plugin_definition['id']] = $plugin_definition;
      }
      $this->setCachedDefinitionsBySeemDisplayable($definitions_by_seem_displayable);
      return isset($definitions_by_seem_displayable[$seem_displayable_plugin_id]) ? $definitions_by_seem_displayable[$seem_displayable_plugin_id] : [];
    }
    return $definitions_by_seem_displayable;
  }

  /**
   * Returns the cached plugin definitions of the decorated discovery class.
   *
   * Grouped by seem_displayable plugin_id.
   *
   * @param string $seem_displayable_plugin_id
   *   The seem_displayable plugin id.
   *
   * @return array|null
   *   On success this will return an array of plugin definitions.
   */
  protected function getCachedDefinitionsBySeemDisplayable($seem_displayable_plugin_id) {
    if (!isset($this->definitionsBySeemDisplayable) && $cache = $this->cacheGet($this->cacheKeyBySeemDisplayable)) {
      $this->definitionsBySeemDisplayable = $cache->data;
    }
    return isset($this->definitionsBySeemDisplayable[$seem_displayable_plugin_id]) ? $this->definitionsBySeemDisplayable[$seem_displayable_plugin_id] : NULL;
  }

  /**
   * Sets a cache of plugin definitions for the decorated discovery class.
   *
   * Grouped by seem_displayable plugin_id.
   *
   * @param array $definitions_by_seem_displayable
   *   List of definitions to store in cache.
   */
  protected function setCachedDefinitionsBySeemDisplayable($definitions_by_seem_displayable) {
    $this->cacheSet($this->cacheKeyBySeemDisplayable, $definitions_by_seem_displayable, Cache::PERMANENT, $this->cacheTags);
    $this->definitionsBySeemDisplayable = $definitions_by_seem_displayable;
  }

//  function searchArray($array, $key, $value) {
//    $results = array();
//
//    if (is_array($array)) {
//      if (isset($array[$key]) && $array[$key] == $value) {
//        $results[] = $array;
//      }
//
//      foreach ($array as $subarray) {
//        $results = array_merge($results, $this->searchArray($subarray, $key, $value));
//      }
//    }
//
//    return $results;
//  }
//
//  public function getRenderableDisplayDefinitions() {
//    $definitions = $this->searchArray($this->getDefinitions(), 'type', 'display');
//
//    $debug = 1;
////    return $this->getDefinitions();
//    return $definitions;
//  }

}

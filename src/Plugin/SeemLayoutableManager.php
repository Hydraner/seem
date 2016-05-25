<?php

namespace Drupal\seem\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Seem element type plugin plugin manager.
 */
class SeemLayoutableManager extends DefaultPluginManager {

  /**
   * Constructor for SeemLayoutableManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/SeemLayoutable', $namespaces, $module_handler, 'Drupal\seem\Plugin\SeemLayoutableInterface', 'Drupal\seem\Annotation\SeemLayoutable');

    $this->alterInfo('seem_layoutable_info');
    $this->setCacheBackend($cache_backend, 'seem_layoutable_plugins');
  }

}

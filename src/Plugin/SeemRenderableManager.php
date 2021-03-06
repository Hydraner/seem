<?php

namespace Drupal\seem\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Seem renderable plugin manager.
 */
class SeemRenderableManager extends DefaultPluginManager {

  /**
   * Constructor for SeemRenderableManager objects.
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
    parent::__construct('Plugin/SeemRenderable', $namespaces, $module_handler, 'Drupal\seem\Plugin\SeemRenderableInterface', 'Drupal\seem\Annotation\SeemRenderable');

    $this->alterInfo('seem_seem_renderable_info');
    $this->setCacheBackend($cache_backend, 'seem_seem_renderable_plugins');
  }

}

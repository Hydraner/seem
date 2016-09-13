<?php
/**
 * @file
 * Contains Drupal\seem\SeemServiceProvider
 */

namespace Drupal\seem;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the language manager service.
 */
class SeemServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides menu.link_tree class to add seem support.
    $definition = $container->getDefinition('menu.link_tree');
    $definition->setClass('Drupal\seem\Menu\SeemMenuLinkTree');
  }
}

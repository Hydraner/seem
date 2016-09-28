<?php

namespace Drupal\seem\Menu;

use Drupal\Core\Menu\MenuLinkTree;

/**
 * Implements the loading, transforming and rendering of menu link trees.
 */
class SeemMenuLinkTree extends MenuLinkTree {

  /**
   * {@inheritdoc}
   */
  public function build(array $tree) {
    $build = parent::build($tree);

    // Wrap each menu tree with a seem render element, in order to be able to
    // add a display per menu.
    if (isset($build['#menu_name'])) {
      return [
        '#type' => 'seem',
        '#displayable' => 'menu',
        '#menu_name' => $build['#menu_name'],
        '#main_content' => $build,
      ];
    }
    return $build;
  }

}

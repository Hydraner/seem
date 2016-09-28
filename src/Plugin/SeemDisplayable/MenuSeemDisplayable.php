<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * The seem_displayable plugin type for menus.
 *
 * @SeemDisplayable(
 *   id = "menu",
 *   label = @Translation("Menu")
 * )
 */
class MenuSeemDisplayable extends SeemDisplayableBase {

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    return ['menu_name' => $element['#menu_name']];
  }

}

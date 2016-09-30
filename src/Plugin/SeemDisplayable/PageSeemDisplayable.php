<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * The seem_displayable plugin type for pages.
 *
 * @SeemDisplayable(
 *   id = "page",
 *   label = @Translation("Page")
 * )
 */
class PageSeemDisplayable extends SeemDisplayableBase {

  /**
   * Get config context.
   *
   * @param array $display_context
   *   The current display's context.
   *
   * @return array
   *   The context on which basis the config will be stored and loaded..
   *
   * @todo: Make this work. does it?
   */
  public function getConfigContext($display_context) {
    $route_name = \Drupal::routeMatch()->getRouteName();

    return $display_context + [
      'route_name' => $route_name
    ];
  }

}

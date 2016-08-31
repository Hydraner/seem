<?php

namespace Drupal\seem\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class PageController.
 *
 * @package Drupal\seem\Controller
 */
class PageController extends ControllerBase {

  /**
   * Since the display is managing the content of landing page, we don't need
   * to do here anything, since we have no main_content. An empty array will
   * just be fine for the SeemVariant to take action.
   *
   * @return array
   *    An empty array.
   */
  public function view() {
    return [];
  }

  /**
   * For now we don't restrict access on landing_pages. It might be a nice
   * feature in the future the call a custom callback or something here.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access() {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::allowed();
  }

  public function viewSeemDisplayConfig() {
    return ['#markup' => 'ES GEHT! zumindest für entities'];
  }

}

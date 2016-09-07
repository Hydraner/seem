<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * @todo A better description here.
 *
 * @todo: Add the existing page features to this displayable (@see
 *        RouteSubscriber).
 *
 * @SeemDisplayable(
 *   id = "existing_page",
 *   label = @Translation("Existing page")
 * )
 */
class ExistingPageSeemDisplayable extends SeemDisplayableBase {

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $debug = 1;
    return [];
  }
  
}

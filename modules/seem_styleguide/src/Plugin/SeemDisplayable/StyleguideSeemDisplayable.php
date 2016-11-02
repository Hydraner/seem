<?php

namespace Drupal\seem_styleguide\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * The seem_displayable plugin type for styleguide elements.
 *
 * @SeemDisplayable(
 *   id = "styleguide",
 *   label = @Translation("Styleguide")
 * )
 */
class StyleguideSeemDisplayable extends SeemDisplayableBase {

  /**
   * {@inheritdoc}
   *
   * @todo: Use better context.
   */
  public function getContext($element) {
    return ['display_id' => $element['#display_id']];
  }

}

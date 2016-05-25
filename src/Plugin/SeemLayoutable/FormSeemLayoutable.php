<?php

namespace Drupal\seem\Plugin\SeemLayoutable;

use Drupal\seem\Plugin\SeemLayoutableBase;

/**
 * @todo A better description here.
 *
 * @SeemLayoutable(
 *   id = "form",
 *   label = @Translation("Form")
 * )
 */
class FormSeemLayoutable extends SeemLayoutableBase {

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    return $element['#form_id'];
  }
}

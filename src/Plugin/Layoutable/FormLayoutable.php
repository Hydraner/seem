<?php

namespace Drupal\seem\Plugin\Layoutable;

use Drupal\seem\LayoutableBase;

/**
 * @todo A better description here.
 *
 * @Layoutable(
 *   id = "form",
 *   label = @Translation("Form")
 * )
 */
class FormLayoutable extends LayoutableBase {

  function getPattern($element) {
    return $element['#form_id'];
  }
}

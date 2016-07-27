<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * @todo A better description here.
 *
 * @SeemDisplayable(
 *   id = "form",
 *   label = @Translation("Form")
 * )
 */
class FormSeemDisplayable extends SeemDisplayableBase {

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    return ['form_id' => $element['#form_id']];
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    return $element['#form_id'];
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveDisplayable($definitions) {
    return isset($definitions[$this->getPattern($this->configuration['element'])]) ? $definitions[$this->getPattern($this->configuration['element'])] : NULL;
  }
  
}

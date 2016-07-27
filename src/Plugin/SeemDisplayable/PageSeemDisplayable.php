<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * @todo A better description here.
 *
 * @SeemDisplayable(
 *   id = "page",
 *   label = @Translation("Page")
 * )
 */
class PageSeemDisplayable extends SeemDisplayableBase {

  /**
   * {@inheritdoc}
   */
  public function determineActiveDisplayable($definitions) {
    return isset($definitions[$this->configuration['suggestion']]) ? $definitions[$this->configuration['suggestion']] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $debug = 1;
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    $debug = 1;
    return [];
  }
  
}

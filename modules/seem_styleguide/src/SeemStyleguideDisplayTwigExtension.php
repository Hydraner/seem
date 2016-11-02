<?php

namespace Drupal\seem_styleguide;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class DefaultService.
 */
class SeemStyleguideDisplayTwigExtension extends Twig_Extension {

  /**
   * Safe if the display has already been rendered.
   */
  protected $rendered = FALSE;

  /**
   * {@inheritdoc}
   *
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'seem_display';
  }

  /**
   * In this function we can declare the extension function
   */
  public function getFunctions() {
    return array(
      new Twig_SimpleFunction('display_seem',
        array($this, 'display_seem'),
        array(
          'is_safe' => array('html')
        )
      )
    );
  }

  /**
   * The php function to load a given block
   */
  public function display_seem($seem_display_id) {
    if (!$this->rendered) {
      $this->rendered = TRUE;
      return [
        '#type' => 'seem',
        '#displayable' => 'styleguide',
        '#display_id' => $seem_display_id,
        '#main_content' => []
      ];
    }
  }

}

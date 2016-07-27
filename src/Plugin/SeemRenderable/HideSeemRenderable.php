<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Provides renderable, which will actually hide something.
 *
 * @SeemRenderable(
 *   id = "hide",
 *   label = @Translation("Hide")
 * )
 */
class HideSeemRenderable extends SeemRenderableBase {

  /**
   * The key of the render Element which should be hided.
   *
   * @var $element_key
   *   The elements key.
   */
  protected $element_key;

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    // Just save some context, since we don't actually add a new render element.
    $this->element_key = $content['key'];

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function doExtraTasks(&$build) {
    unset($build[$this->region_key][$this->element_key]);
  }

}

<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Provides the main content of the current display variant.
 *
 * @SeemRenderable(
 *   id = "main_content",
 *   label = @Translation("Main content")
 * )
 */
class MainContentSeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    return $seem_display->getMainContent();
  }
}

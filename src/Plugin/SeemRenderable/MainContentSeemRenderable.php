<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
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
  public function doRenderable($content, SeemVariant $seem_variant) {
    return $seem_variant->getMainContent();
  }
}

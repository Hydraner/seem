<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Provides a piece of renderable markup, defined by the layout.
 *
 * @SeemRenderable(
 *   id = "markup",
 *   label = @Translation("Markup")
 * )
 */
class MarkupSeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemVariant $seem_variant) {
    return ['#markup' => $content['markup']];
  }
}

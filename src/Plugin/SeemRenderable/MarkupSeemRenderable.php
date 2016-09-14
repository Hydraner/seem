<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
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
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $markup = new TranslatableMarkup($content['markup']);
    return ['#markup' => $markup];
  }
}

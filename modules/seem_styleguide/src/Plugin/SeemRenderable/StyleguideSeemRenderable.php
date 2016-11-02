<?php

namespace Drupal\seem_styleguide\Plugin\SeemRenderable;

use Drupal\seem\Plugin\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Renders a sassdoc stylguide.
 *
 * @SeemRenderable(
 *   id = "styleguide",
 *   label = @Translation("Styleguide")
 * )
 */
class StyleguideSeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    return ['#theme' => 'generated_styleguide'];
  }

}

<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Renders a seem display in a region.
 *
 * @SeemRenderable(
 *   id = "display",
 *   label = @Translation("Markup")
 * )
 */
class DisplaySeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    /** @var \Drupal\seem\Plugin\SeemDisplay\SeemDisplayBase $seem_display */
    $regions = [];
    foreach ($content['regions'] as $region_key => $region_definition) {
      $regions[$region_key] = $seem_display->processRegion($region_definition, $region_key);
    }

    // @todo: Use Build function instead! But this has to be rewritten first :)
    return $seem_display->processLayout($regions, $content['layout']);
  }
  
}

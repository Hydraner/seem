<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\block_content\Entity\BlockContent;
use Drupal\seem\Annotation\SeemDisplay;
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;

/**
 * Renders a content block.
 *
 * @SeemRenderable(
 *   id = "content_block",
 *   label = @Translation("Markup")
 * )
 */
class ContentBlockSeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $bid = $content['bid'];
    $block = BlockContent::load($bid);
    $render = \Drupal::entityManager()->getViewBuilder('block_content')->view($block);
    return $render;
  }

}

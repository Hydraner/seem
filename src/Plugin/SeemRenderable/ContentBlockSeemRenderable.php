<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\block_content\Entity\BlockContent;
use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
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
  public function doRenderable($content, SeemVariant $seem_variant) {
    $bid = $content['bid'];
    $block = BlockContent::load($bid);
    $render = \Drupal::entityManager()->getViewBuilder('block_content')->view($block);
    return $render;
  }
}

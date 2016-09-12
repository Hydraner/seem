<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityInterface;
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
    if ($block instanceof EntityInterface) {
      // @todo: Inject entityTypeManager.
      $render = \Drupal::entityManager()
        ->getViewBuilder('block_content')
        ->view($block);
      return $render;
    }
    // @todo: Add useful error information.
    return [];
  }

}

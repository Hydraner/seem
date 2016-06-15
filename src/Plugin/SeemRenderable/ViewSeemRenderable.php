<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
use Drupal\seem\Plugin\SeemRenderableBase;
use Drupal\views\Views;

/**
 * Provides renderable view, defined by the display.
 *
 * @SeemRenderable(
 *   id = "view",
 *   label = @Translation("View")
 * )
 */
class ViewSeemRenderable extends SeemRenderableBase {

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemVariant $seem_variant) {
    $view = Views::getView($content['view']);
    if (!$view || !$view->access($content['display_id'])) {
      return;
    }

    return [
      // Force rendering the title, since this is what we expect it to do.
      // @see seem_preprocess_views_view()
      '#show_title' => TRUE,
      '#type' => 'view',
      '#name' => $content['name'],
      '#display_id' => $content['display_id'],
      '#arguments' => isset($content['arguments']) ? $content['arguments'] : [],
    ];
  }
}

<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
use Drupal\seem\Plugin\SeemRenderableBase;
use Drupal\views\Views;

/**
 * Provides renderable views, defined by the layout.
 *
 * @todo:
 *   Add functionality to render the title of the view too.
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
        //return views_embed_view($content['view'], $content['display_id']);
        $args = func_get_args();
        $view = Views::getView($content['view']);
        if (!$view || !$view->access($content['display_id'])) {
            return;
        }
        $view->buildTitle();
        //kint($view);

        return[
            '#title' => $view->getTitle(),
            '#type' => 'view',
            '#name' => $content['view'],
            '#display_id' => $content['display_id'],
            '#arguments' => $args,
        ];

    }
}
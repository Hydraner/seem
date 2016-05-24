<?php

namespace Drupal\seem\EventSubscriber;

use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Selects the block page display variant.
 *
 * @see \Drupal\block\Plugin\DisplayVariant\BlockPageVariant
 */
class SeemDisplayVariantSubscriber implements EventSubscriberInterface {

  /**
   * Selects the block page display variant.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
//    $route_match = $event->getRouteMatch();
//    $route_match->getRouteObject()->setOption('display_variant_plugin_id', $event->getPluginId());
    $configuration = $event->getPluginConfiguration();
    $configuration['original_display_variant_plugin_id'] = $event->getPluginId();
    $event->setPluginConfiguration($configuration);
    $event->setPluginId('seem_variant');
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = array('onSelectPageDisplayVariant');
    return $events;
  }

}

<?php

namespace Drupal\seem\EventSubscriber;

use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Selects the seem display variant for pages.
 *
 * @see \Drupal\block\Plugin\DisplayVariant\BlockPageVariant
 */
class SeemDisplayVariantSubscriber implements EventSubscriberInterface {

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
   */
  protected $seemDisplayPluginManager;

  /**
   * Constructs a new SeemDisplayVariantSubscriber object.
   *
   * @param \Drupal\seem\SeemDisplayManager $seem_display_plugin_manager
   *   The seem display manager
   */
  public function __construct(SeemDisplayManager $seem_display_plugin_manager) {
    $this->seemDisplayPluginManager = $seem_display_plugin_manager;
  }

  /**
   * Selects the seem display variant.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    // Inject the original display variant plugin id to the seem variant
    // display. Since we only want to take control over the main_content part
    // of the page, we will inject the matching seem layout into the original
    // display variant.
    $configuration = array_merge($event->getPluginConfiguration(), ['original_display_variant_plugin_id' => $event->getPluginId()]);
    $event->setPluginConfiguration($configuration);
    /** 
     * @todo: Think about using diffrent variants for diffrent usecases 
     *        (http response for instance). This would mean that we already need 
     *        to make decisions at this point and inject the configuration
     *        instead of doing this in the build() method.
     */
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
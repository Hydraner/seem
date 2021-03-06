<?php

namespace Drupal\seem\EventSubscriber;

use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\seem\Plugin\SeemDisplayableManager;
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
   * @var \Drupal\seem\SeemDisplayManagerInterface
   */
  protected $seemDisplayPluginManager;

  /**
   * The seem displayable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemDisplayableManager
   */
  protected $seemDisplayableManager;

  /**
   * Constructs a new SeemDisplayVariantSubscriber object.
   *
   * @param \Drupal\seem\SeemDisplayManager $seem_display_plugin_manager
   *   The seem display manager.
   * @param \Drupal\seem\Plugin\SeemDisplayableManager $seem_displayable_manager
   *   The seem displayable plugin manager.
   */
  public function __construct(SeemDisplayManager $seem_display_plugin_manager, SeemDisplayableManager $seem_displayable_manager) {
    $this->seemDisplayPluginManager = $seem_display_plugin_manager;
    $this->seemDisplayableManager = $seem_displayable_manager;
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

    // If we have a seem_display on the route, we can be sure it's cause we
    // registered the route ourselves.
    if (NULL !== $event->getRouteMatch()->getParameter('seem_display') && is_array($event->getRouteMatch()->getParameter('seem_display'))) {
      // @todo: Replace suggestion with file_name or ID?
      $configuration['suggestion'] = $event->getRouteMatch()->getParameter('plugin_id');
      $configuration['context']['route'] = $event->getRouteMatch()->getRouteName();
      // @todo: Use seemDisplayable to determine context! (like the seem render element does)
      $seem_displayable = $this->seemDisplayableManager->createInstance('page');
      $configuration['seem_displayable'] = $seem_displayable;
      $configuration['seem_display'] = $event->getRouteMatch()->getParameter('seem_display');

      $event->setPluginConfiguration($configuration);
      $event->setPluginId('seem_variant');
    }
    else {
      $context = ['route' => $event->getRouteMatch()->getRouteName()];
      $seem_display = $this->seemDisplayPluginManager->getDefinitionByContext($context, 'existing_page');

      if ($seem_display) {
        // @todo: Replace suggestion with file_name or ID?
        $configuration['suggestion'] = $event->getRouteMatch()->getRouteName();
        $configuration['context']['route'] = $event->getRouteMatch()->getRouteName();
        $seem_displayable = $this->seemDisplayableManager->createInstance('existing_page');
        $configuration['seem_displayable'] = $seem_displayable;
        $configuration['seem_display'] = $seem_display;

        $event->setPluginConfiguration($configuration);
        $event->setPluginId('seem_variant');
      }
      else {
        // We can assume that their is no existing page context. So what about
        // the other contexts.
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = array('onSelectPageDisplayVariant');
    return $events;
  }

}

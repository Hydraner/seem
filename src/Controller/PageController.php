<?php

namespace Drupal\seem\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\seem\Annotation\SeemDisplayable;
use Drupal\seem\Plugin\SeemDisplayableManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageController.
 *
 * @package Drupal\seem\Controller
 */
class PageController extends ControllerBase {


  public function __construct(CurrentRouteMatch $current_route_match, SeemDisplayableManager $seem_displayable) {
    $this->currentRouteMatch = $current_route_match;

    $this->seemDisplayable = $seem_displayable;
  }


  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('plugin.manager.seem_displayable.processor')
    );
  }

  /**
   * Since the display is managing the content of landing page, we don't need
   * to do here anything, since we have no main_content. An empty array will
   * just be fine for the SeemVariant to take action.
   *
   * @return array
   *    An empty array.
   */
  public function view() {
    return [];
  }

  /**
   * For now we don't restrict access on landing_pages. It might be a nice
   * feature in the future the call a custom callback or something here.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function access() {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::allowed();
  }

  public function viewSeemDisplayConfig() {
    $debug = 1;
    $parameters = $this->currentRouteMatch->getParameters();
    $seem_displayable_definition = $parameters->get('seem_displayable_definition');

    $displayable = $this->seemDisplayable->createInstance($seem_displayable_definition['id']);


    $label = $parameters->get('seem_displayable_definition')['label']->render();
    return ['#markup' => "Dies ist ein $label"];
  }

}

<?php

namespace Drupal\seem\Routing;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Routing\RouteCompiler;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines a route subscriber, listening.
 */
class RouteSubscriber extends RouteSubscriberBase {
  protected $pluginManager;

  /**
   * RouteSubscriber constructor.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   */
  public function __construct(PluginManagerInterface $plugin_manager) {
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    $debug = 1;
    // @todo: Extend plugin manager to return definitions by SeemRenderable (in
    // this case we need "page").
    foreach ($this->pluginManager->getDefinitions() as $definition) {
      $path = $definition['path'];
      if ($route_name = $this->findRouteName($path, $collection)) {
      }
      else {
        // We need to create the route.
        $debug = 1;
        $route_name = "seem.layout_" . $definition['id'];
        $path = $definition['path'];
        $requirements = array();
        $parameters = array();
        $requirements['_custom_access'] = '\Drupal\seem\Controller\PageController::access';

        $route = new Route(
          $path,
          [
            '_controller' => '\Drupal\seem\Controller\PageController::content',
            '_title' => $definition['label'],
//          'page_manager_page_variant' => $variant_id,
//          'page_manager_page' => $page_id,
//          'page_manager_page_variant_weight' => $variant->getWeight(),
            // When adding multiple variants, the variant ID is added to the
            // route name. In order to convey the base route name for this set
            // of variants, add it as a parameter.
            'base_route_name' => $route_name,
          ],
          $requirements,
          [
            'parameters' => $parameters,
//          '_admin_route' => $entity->usesAdminTheme(),
          ]
        );
        $collection->add($route_name, $route);
      }

    }
  }

  protected function findRouteName($path, RouteCollection $collection) {
    // Loop through all existing routes to see if this is overriding a route.
    foreach ($collection->all() as $name => $collection_route) {
      // Find all paths which match the path of the current display.
      $route_path = $collection_route->getPath();
      $route_path_outline = RouteCompiler::getPatternOutline($route_path);

      // Match either the path or the outline, e.g., '/foo/{foo}' or '/foo/%'.
      if ($path === $route_path || $path === $route_path_outline) {
        // Return the overridden route name.
        return $name;
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Run after EntityRouteAlterSubscriber.
    $events[RoutingEvents::ALTER][] = ['onAlterRoutes', -230];
    return $events;
  }

}

<?php

namespace Drupal\seem\Routing;

use Drupal\Core\Routing\RouteCompiler;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines a route subscriber, listening.
 */
class RouteSubscriber extends RouteSubscriberBase {

  use StringTranslationTrait;

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
   */
  protected $seemDisplayManager;

  /**
   * RouteSubscriber constructor.
   *
   * @param \Drupal\seem\SeemDisplayManager $seem_display_manager
   *   The seem display plugin manager.
   */
  public function __construct(SeemDisplayManager $seem_display_manager) {
    $this->seemDisplayManager = $seem_display_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    $this->getSeemPageRoutes($collection);
    $this->getSeemExistingPageRoutes($collection);
  }

  /**
   * Collect and create routes from the seem displays for the displayable type
   * 'page'.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The route collection.
   */
  protected function getSeemPageRoutes(RouteCollection $collection) {
    foreach ($this->seemDisplayManager->getDefinitionsBySeemDisplayable('page') as $definition) {
      $path = $definition['path'];
      if (!$this->findRouteName($path, $collection)) {
        // We need to create the route.
        $requirements = array();
        $parameters = array();
        $requirements['_custom_access'] = '\Drupal\seem\Controller\PageController::access';

        $route = new Route(
          $path,
          [
            '_controller' => '\Drupal\seem\Controller\PageController::view',
            '_title' => (string) $definition['label'],

            'base_route_name' => $definition['context']['route'],
            'seem_display' => $definition
          ],
          $requirements,
          [
            'parameters' => $parameters,
            // @todo: Make admin_route configurable.
            // '_admin_route' => $entity->usesAdminTheme(),
          ]
        );
        $collection->add($definition['context']['route'], $route);
      }
    }
  }

  /**
   * Collect route overides for the seem display type 'page' and add them to the
   * existing routes.
   *
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The route collection.
   */
  protected function getSeemExistingPageRoutes(RouteCollection $collection) {
    foreach ($this->seemDisplayManager->getDefinitionsBySeemDisplayable('existing_page') as $definition) {
      // Special Route overrides for 404, 403 and 401 response definitions..
      if (isset($definition['response']) && $route = $collection->get($definition['context']['route'])) {
        if ($definition['response'] == 404) {
          $route->setDefaults([
            '_controller' => '\Drupal\system\Controller\Http4xxController:on404',
            '_title' => 'Page not found',
          ]);
          $route->setRequirement('_access', 'TRUE');
        }
        else if ($definition['response'] == 403) {
          $route->setDefaults([
            '_controller' => '\Drupal\system\Controller\Http4xxController:on403',
            '_title' => 'Access denied',
          ]);
          $route->setRequirement('_access', 'TRUE');
        }
        else if ($definition['response'] == 401) {
          $route->setDefaults([
            '_controller' => '\Drupal\system\Controller\Http4xxController:on401',
            '_title' => 'Unauthorized',
          ]);
          $route->setRequirement('_access', 'TRUE');
        }
      }

      // Route parameter override.
      // Seem supports overriding route parameters through the display plugin
      // for existing pages. just add a parameter "route" => [].
      // @todo: We probably need a good example for that in the example module.
      if (isset($definition['route']) && $route_overrides = $definition['route']) {
        if (isset($definition['context']['route']) && $route = $collection->get($definition['context']['route'])) {
          foreach ($route_overrides as $key => $values) {
            $method_key = ucfirst($key);
            if (method_exists($route, "get$method_key")) {
              $route->{"add$method_key"}($values);
            }
          }
        }
      }
    }
  }

  /**
   * Finds an existing route name for a given path.
   *
   * @param string $path
   *   A path string.
   * @param \Symfony\Component\Routing\RouteCollection $collection
   *   The collection holding all the routes we are searching in for the path.
   * @return string $route_name
   *   The route name if it exists.
   */
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

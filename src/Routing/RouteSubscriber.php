<?php

namespace Drupal\seem\Routing;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Routing\RouteCompiler;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines a route subscriber, listening.
 */
class RouteSubscriber extends RouteSubscriberBase {

  use StringTranslationTrait;

  protected $pluginManager;
  protected $seemDisplayable;

  /**
   * RouteSubscriber constructor.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $plugin_manager
   */
  public function __construct(PluginManagerInterface $plugin_manager, $seem_displayable) {
    $this->pluginManager = $plugin_manager;
    $this->seemDisplayable = $seem_displayable;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    foreach ($this->pluginManager->getDefinitionsBySeemDisplayable('page') as $definition) {
      $path = $definition['path'];
      if (!$this->findRouteName($path, $collection)) {
        // We need to create the route.
        $debug = 1;
//        $route_name = "seem.layout_" . $definition['id'];
        $requirements = array();
        $parameters = array();
        $requirements['_custom_access'] = '\Drupal\seem\Controller\PageController::access';

        $route = new Route(
          $path,
          [
            '_controller' => '\Drupal\seem\Controller\PageController::view',
            '_title' => (string) $definition['label'],
//          'page_manager_page_variant' => $variant_id,
//          'page_manager_page' => $page_id,
//          'page_manager_page_variant_weight' => $variant->getWeight(),
            // When adding multiple variants, the variant ID is added to the
            // route name. In order to convey the base route name for this set
            // of variants, add it as a parameter.
            'base_route_name' => $definition['context']['route'],
            // @todo: I think we don't need this anymore since we use the route as context.
//            'plugin_id' => $definition['id'],
            'seem_display' => $definition
          ],
          $requirements,
          [
            'parameters' => $parameters,
//          '_admin_route' => $entity->usesAdminTheme(),
          ]
        );
        $collection->add($definition['context']['route'], $route);
      }

    }


    // Route overrides.
    foreach ($this->pluginManager->getDefinitionsBySeemDisplayable('existing_page') as $definition) {
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

      if (isset($definition['route']) && $route_overrides = $definition['route']) {
        if (isset($definition['context']['route']) && $route = $collection->get($definition['context']['route'])) {
          foreach ($route_overrides as $key => $values) {
            $method_key = ucfirst($key);
            if (method_exists($route, "get$method_key")) {
//              if (!empty($route->{"get$method_key"}())) {
              $route->{"add$method_key"}($values);
//              }
//              else {
//                $route->{"set$method_key"}($values);
//              }
//              $debug = 1;
            }
          }
        }
//        $route->set
      }
    }

    // @todo: Build routes for displayable configuration forms.
    // @todo: change this admin/structure/seem/seem_displayable_configuration
    foreach ($this->seemDisplayable->getDefinitions() as $definition) {
      $displayable = $this->seemDisplayable->createInstance($definition['id']);
      $paths = $displayable->getBasePaths();
      // @todo: Generate the route for the Paths.
      $count = 0;
      foreach ($paths as $delta => $path) {
        $configuration_path = $path . '/seem';

        // @todo: Create access for seem display config pages.
        $requirements = [];
        $requirements['_custom_access'] = '\Drupal\seem\Controller\PageController::access';

        $route = new Route(
          $configuration_path,
          [
            // @todo: Move this to seemDispalyable.
            '_controller' => '\Drupal\seem\Controller\PageController::viewSeemDisplayConfig',
            '_title' => (string) $definition['label'],
            'base_route_name' => 'seem.displayable_config_' . $definition['id'],
//            'seem_displayable' => $displayable,
            'seem_displayable_definition' => $definition,
          ],
          $requirements,
          [
            'parameters' => [],
            '_admin_route' => TRUE,
          ]
        );
        $collection->add('seem.displayable_config_' . $definition['id'] . '_' . $count, $route);
        $count++;
      }
      $debug = 1;
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

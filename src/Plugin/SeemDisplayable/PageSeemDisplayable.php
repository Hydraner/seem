<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\seem\Plugin\SeemDisplayableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The seem_displayable plugin type for pages.
 *
 * @SeemDisplayable(
 *   id = "page",
 *   label = @Translation("Page")
 * )
 */
class PageSeemDisplayable extends SeemDisplayableBase implements ContainerFactoryPluginInterface {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Creates a LocalTasksBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigContext($display_context) {
    $path = $this->routeMatch->getRouteObject()->getPath();

    return $display_context + ['path' => $path];
  }

}

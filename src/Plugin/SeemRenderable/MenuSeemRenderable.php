<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a given menu.
 *
 * @SeemRenderable(
 *   id = "menu",
 *   label = @Translation("Menu")
 * )
 */
class MenuSeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

  /**
   * The menu link tree interface.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   *   The menu tree.
   */
  protected $menuLinkTree;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_link_tree
   *   The menu tree.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MenuLinkTreeInterface $menu_link_tree) {
    $this->menuLinkTree = $menu_link_tree;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu.link_tree')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $menu_name = $content['menu_name'];

    // Build the typical default set of menu tree parameters.
    $parameters = $this->menuLinkTree->getCurrentRouteMenuTreeParameters($menu_name);

    // Load the tree based on this set of parameters.
    $tree = $this->menuLinkTree->load($menu_name, $parameters);

    // Transform the tree.
    $manipulators = array(
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $this->menuLinkTree->transform($tree, $manipulators);

    // Get renderable array from the transformed tree.
    return $this->menuLinkTree->build($tree);
  }

}

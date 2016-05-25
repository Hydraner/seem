<?php

namespace Drupal\seem\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageController.
 *
 * @package Drupal\seem\Controller
 */
class PageController extends ControllerBase {


  /**
   * Drupal\seem\SeemLayoutPluginManager definition.
   *
   * @var \Drupal\seem\SeemDisplayManager
   */
  protected $plugin_manager_seem_layout_plugin;
  /**
   * {@inheritdoc}
   */
  public function __construct(SeemDisplayManager $plugin_manager_seem_layout_plugin) {
    $this->plugin_manager_seem_layout_plugin = $plugin_manager_seem_layout_plugin;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function content() {
    $debug = 1;
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
    ];
  }
  public function view() {
//    $debug = 1;
//    $page_builder = new SeemVariant([], '', [], \Drupal::service('plugin.manager.block'));
//
//    $page_builder->appendRenderArray('main', ['#markup' => 'muh']);
//    $page_builder->appendRenderArray('main', ['#markup' => 'meh']);
//    $page_builder->appendRenderArray('main_two', ['#markup' => 'buuuh!']);
//
////    $page_builder->appendBlock('main_two', 'views_block:news-block_1');
//
//    return $page_builder->build();
    return [];
//    return [
//      '#type' => 'markup',
//      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
//    ];
  }

  public function access() {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.
    return AccessResult::allowed();
  }
}

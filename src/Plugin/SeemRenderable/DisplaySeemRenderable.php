<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\seem\Plugin\SeemDisplayableManager;
use Drupal\seem\Plugin\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a seem display in a region.
 *
 * @SeemRenderable(
 *   id = "display",
 *   label = @Translation("Display")
 * )
 */
class DisplaySeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface
   */
  protected $seemDisplayManager;

  /**
   * The seem displayable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemDisplayableManager
   */
  protected $seemDisplayableManager;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_manager
   *   The seem display manager.
   * @param \Drupal\seem\Plugin\SeemDisplayableManager $seem_displayable_manager
   *   The seem displayable plugin manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    SeemDisplayManager $seem_display_manager,
    SeemDisplayableManager $seem_displayable_manager
  ) {
    $this->seemDisplayManager = $seem_display_manager;
    $this->seemDisplayableManager = $seem_displayable_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.seem_display'),
      $container->get('plugin.manager.seem_displayable.processor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $seem_displayable = $this->seemDisplayableManager->createInstance('display');
    /** @var \Drupal\seem\Plugin\SeemDisplayInterface $renderable_seem_display */
    $renderable_seem_display = $this->seemDisplayManager->createInstance('renderable_display');
    $renderable_seem_display->setMainContent($seem_display->getMainContent());
    $renderable_seem_display->setSeemDisplayable($seem_displayable);
    if (isset($content['layout'])) {
      $renderable_seem_display->setLayout($content['layout']);
    }
    if (isset($content['layout_settings'])) {
      $renderable_seem_display->setLayoutSettings($content['layout_settings']);
    }
    if (isset($content['regions'])) {
      $renderable_seem_display->setRegionDefinitions($content['regions']);
    }

    return $renderable_seem_display->build();
  }

}

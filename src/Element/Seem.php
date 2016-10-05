<?php

namespace Drupal\seem\Element;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\seem\Plugin\SeemDisplayableManager;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a render element to layout a given render array.
 *
 * @RenderElement("seem")
 */
class Seem extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The SeemDisplayable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemDisplayableManager
   */
  protected $seemDisplayablePluginManager;

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface
   */
  protected $seemDisplayPluginManager;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\seem\Plugin\SeemDisplayableManager $seem_displayable_plugin_manager
   *   The seem_displayable plugin manager.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_plugin_manager
   *   The seem display manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SeemDisplayableManager $seem_displayable_plugin_manager, SeemDisplayManager $seem_display_plugin_manager) {
    $this->seemDisplayablePluginManager = $seem_displayable_plugin_manager;
    $this->seemDisplayPluginManager = $seem_display_plugin_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.seem_displayable.processor'),
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return array(
      '#pre_render' => array(
        array($this, 'preRenderSeemElement'),
      ),
    );
  }

  /**
   * Implements preRenderSeemElement().
   */
  public function preRenderSeemElement($element) {
    // Check whether the content has already been rendered by seem and if there
    // is displayable which knows how it has to be rendered.
    if (!isset($element['#main_content']['#rendered']) && $this->seemDisplayablePluginManager->hasDefinition($element['#displayable'])) {
      /** @var \Drupal\seem\Plugin\SeemDisplayableInterface $seem_displayable */
      $seem_displayable = $this->seemDisplayablePluginManager->createInstance($element['#displayable']);
      $seem_displayable_plugin_id = $seem_displayable->getPluginId();
      $context = $seem_displayable->getContext($element);

      // Load a seem display, based on the context, pulled out from the element
      // by the seem displayable.
      if ($seem_display_definition = $this->seemDisplayPluginManager->getDefinitionByContext($context, $seem_displayable_plugin_id)) {
        // Make sure the content won't get rendered recursive.
        $element['#main_content']['#rendered'] = TRUE;

        /** @var \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface $seem_display */
        $seem_display = $this->seemDisplayPluginManager->createInstance($seem_display_definition['id']);
        $seem_display->setSeemDisplayable($seem_displayable);
        $seem_display->setMainContent($element['#main_content']);

        return $seem_display->build();
      }
    }

    // If their is no seem display responsible, we just render the #main_content
    // like nothing happened.
    return $element['#main_content'];
  }

}

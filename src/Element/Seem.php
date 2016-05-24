<?php

/**
 * @file
 * Contains the Seem render element implementation.
 */

namespace Drupal\seem\Element;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a render element to layout a given render array.
 *
 * @RenderElement("seem")
 */
class Seem extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The Layoutable manager.
   *
   * @var \Drupal\seem\LayoutableManager
   */
  protected $layoutableManager;

  /**
   * The variant manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $variantManager;

  /**
   * The Plugin Block Manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface.
   */
  protected $blockManager;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $layoutable_manager
   *   The layoutable plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $variant_manager
   *   The variant manager.Variant $display_variant_manager.
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The Plugin Block Manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PluginManagerInterface $layoutable_manager, PluginManagerInterface $variant_manager, BlockManagerInterface $block_manager) {
    $this->layoutableManager = $layoutable_manager;
    $this->variantManager = $variant_manager;
    $this->blockManager = $block_manager;

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
      $container->get('plugin.manager.layoutable.processor'),
      $container->get('plugin.manager.display_variant'),
      $container->get('plugin.manager.block')
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
    if ($this->layoutableManager->hasDefinition($element['#layoutable'])) {
      // Make sure the content won't get rendered recursive.
      $element['#main_content']['#rendered'] = TRUE;

      /** @var \Drupal\seem\LayoutableInterface $element_type */
      $element_type = $this->layoutableManager->createInstance($element['#layoutable']);
      $configuration['suggestion'] = $element_type->getPattern($element);

      /** @var \Drupal\seem\Plugin\DisplayVariant\SeemVariant $seem_variant */
      $seem_variant = new SeemVariant($configuration, 'seem', $this->variantManager->getDefinition('seem_variant'), $this->blockManager, $this->variantManager);

      // Call Drupal\Core\Display\PageVariantInterface methods.
      $seem_variant->setMainContent($element['#main_content']);
      $seem_variant->setTitle('');

      return $seem_variant->build();
    }

    return $element['#main_content'];
  }
}

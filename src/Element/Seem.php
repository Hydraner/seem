<?php

namespace Drupal\seem\Element;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Display\VariantManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\seem\Plugin\DisplayVariant\SeemVariant;
use Drupal\seem\Plugin\SeemLayoutableManager;
use Drupal\seem\Plugin\SeemRenderableManager;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a render element to layout a given render array.
 *
 * @RenderElement("seem")
 */
class Seem extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The SeemLayoutable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemLayoutableManager
   */
  protected $seemLayoutablePluginManager;

  /**
   * The seem_renderable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemRenderableManager
   */
  protected $seemRenderablePluginManager;

  /**
   * The variant plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $variantPluginManager;

  /**
   * The block plugin manager.
   *
   * @var \Drupal\Core\Block\BlockManager.
   */
  protected $blockPluginManager;

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
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
   * @param \Drupal\seem\Plugin\SeemLayoutableManager $seem_layoutable_plugin_manager
   *   The seem_layoutable plugin manager.
   * @param \Drupal\seem\Plugin\SeemRenderableManager $seem_renderable_plugin_manager
   *   The seem seem_renderable plugin manager.
   * @param \Drupal\Core\Display\VariantManager $variant_plugin_manager
   *   The variant manager.Variant $display_variant_manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $block_plugin_manager
   *   The Plugin Block Manager.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_plugin_manager
   *   The seem display manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SeemLayoutableManager $seem_layoutable_plugin_manager, SeemRenderableManager $seem_renderable_plugin_manager, VariantManager $variant_plugin_manager, PluginManagerInterface $block_plugin_manager, SeemDisplayManager $seem_display_plugin_manager) {
    $this->seemLayoutablePluginManager = $seem_layoutable_plugin_manager;
    $this->seemRenderablePluginManager = $seem_renderable_plugin_manager;
    $this->variantPluginManager = $variant_plugin_manager;
    $this->blockPluginManager = $block_plugin_manager;
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
      $container->get('plugin.manager.seem_layoutable.processor'),
      $container->get('plugin.manager.seem_renderable.processor'),
      $container->get('plugin.manager.display_variant'),
      $container->get('plugin.manager.block'),
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
    // it is layoutable which knows how.
    if (!isset($element['#main_content']['#rendered']) && $this->seemLayoutablePluginManager->hasDefinition($element['#layoutable'])) {
      // Make sure the content won't get rendered recursive.
      $element['#main_content']['#rendered'] = TRUE;

      /** @var \Drupal\seem\Plugin\SeemLayoutableInterface $element_type */
      $element_type = $this->seemLayoutablePluginManager->createInstance($element['#layoutable']);
      $configuration['suggestion'] = $element_type->getPattern($element);

      /** @var \Drupal\seem\Plugin\DisplayVariant\SeemVariant $seem_variant */
      $seem_variant = new SeemVariant($configuration, 'seem', $this->variantPluginManager->getDefinition('seem_variant'), $this->blockPluginManager, $this->variantPluginManager, $this->seemRenderablePluginManager, $this->seemDisplayPluginManager);

      // Call Drupal\Core\Display\PageVariantInterface methods.
      $seem_variant->setMainContent($element['#main_content']);
      $seem_variant->setTitle('');

      return $seem_variant->build();
    }

    return $element['#main_content'];
  }
}

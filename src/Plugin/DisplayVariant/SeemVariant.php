<?php

namespace Drupal\seem\Plugin\DisplayVariant;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Display\PageVariantInterface;
use Drupal\Core\Display\VariantBase;
use Drupal\Core\Display\VariantManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatch;
use Drupal\seem\Plugin\SeemRenderableManager;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a variant plugin, which will build a display based on a seem display
 * configuration.
 *
 * @todo: Add some more descriptive documentation about what this variant does,
 *        since it can also inject itself into $pageVariant.
 *
 * @DisplayVariant(
 *   id = "seem_variant",
 *   admin_label = @Translation("Seem"),
 *   no_ui = TRUE,
 * )
 */
class SeemVariant extends VariantBase implements PageVariantInterface, ContainerFactoryPluginInterface {

  /**
   * The block plugin manager.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $blockPluginManager;

  /**
   * The variant plugin manager.
   *
   * \Drupal\Core\Display\VariantManager
   */
  protected $displayVariantPluginManager;

  /**
   * The seem_renderable plugin manager.
   *
   * @var \Drupal\seem\Plugin\SeemRenderableManager
   */
  protected $seemRenderablePluginManager;

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
   */
  protected $seemDisplayPluginManager;

  /**
   * The render array representing the main page content.
   *
   * @var array
   */
  protected $mainContent = [];

  /**
   * The page title: a string (plain title) or a render array (formatted title).
   *
   * @var string|array
   */
  protected $title = '';

  /**
   * A variant instance.
   *
   * @var \Drupal\Core\Display\VariantManager
   */
  protected $pageVariant;


  protected $seemLayoutable;

  /**
   * Constructs a new SeemVariant object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $block_plugin_manager
   *   The block plugin manager.
   * @param \Drupal\Core\Display\VariantManager $display_variant_plugin_manager
   *   The variant plugin manager.
   * @param \Drupal\seem\Plugin\SeemRenderableManager $seem_renderable_plugin_manager
   *   The seem renderable plugin manager.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_plugin_manager
   *   The seem display plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PluginManagerInterface $block_plugin_manager, VariantManager $display_variant_plugin_manager, SeemRenderableManager $seem_renderable_plugin_manager, SeemDisplayManager $seem_display_plugin_manager) {
    $this->blockPluginManager = $block_plugin_manager;
    $this->displayVariantPluginManager = $display_variant_plugin_manager;
    $this->seemRenderablePluginManager = $seem_renderable_plugin_manager;
    $this->seemDisplayPluginManager = $seem_display_plugin_manager;

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
      $container->get('plugin.manager.block'),
      $container->get('plugin.manager.display_variant'),
      $container->get('plugin.manager.seem_renderable.processor'),
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected $regions = [];

  /**
   * {@inheritdoc}
   */
//  public function build() {
//    // Set default page cache keys that include the display.
//    $build['#cache']['keys'] = [
//      'dodo_make_this_better',
//      $this->id(),
//    ];
//    $build['#pre_render'][] = [$this, 'buildRegions'];
//    return $build;
//  }

  /**
   * #pre_render callback for building the regions.
   */
  public function build() {
    // Iterate through all available layouts.
    // @todo: Identify the layout by it's context.
    // @todo: Use the context as id for layouts.
    $test = $this->seemDisplayPluginManager->getDefinitionsBySeemLayoutable('entity');
    $test2 = $this->seemDisplayPluginManager->getDefinitionsBySeemLayoutable('form');
    $debug = 1;
    foreach ($this->seemDisplayPluginManager->getDefinitions() as $key => $definition) {
      // If the layout matches our suggestion, extract the regions.
      if (isset($this->configuration['suggestion'])) {
        if ($definition['id'] == $this->configuration['suggestion']) {
          foreach ($definition['regions'] as $region => $region_content) {
            foreach ($region_content as $content) {
              if ($this->seemRenderablePluginManager->hasDefinition($content['type'])) {
                $seem_renderable = $this->seemRenderablePluginManager->createInstance($content['type']);
                $renderable = $seem_renderable->doRenderable($content, $this);
                $this->addToRegion($region, $renderable);
              }
            }
          }
        }
      }
    }

    // If original_display_variant_plugin_id exists, it means that we are
    // currently on an existing_landing_page context. Inject the layout inside
    // of the original display_variant since we only want to take over control
    // of the main content area.
    if (isset($this->configuration['original_display_variant_plugin_id'])) {
      $original_display_variant_plugin_id = $this->configuration['original_display_variant_plugin_id'];

      // Get an instance of the original display variant.
      $instance = $this->displayVariantPluginManager->createInstance($original_display_variant_plugin_id);
      $this->pageVariant = $instance;
      // If we have custom defined regions from a plugin, inject them. Otherwise
      // Inject the mainContent since we don't have to do anything.
      if (!empty($this->regions)) {
        $this->pageVariant->setMainContent($this->regions);
      }
      else {
        $this->pageVariant->setMainContent($this->mainContent);
      }
      // This must be called till it's part of the interface.
      $this->pageVariant->setTitle($this->title);

      // Use the parents page_variant build.
      return $this->pageVariant->build();
    }

    // If we don't have regions, just
    if (empty($this->regions)) {
      return $this->mainContent;
    }

    // @todo: Add cachable metadata.

    // Render regions.
    // @todo: Render real regions.
    return $this->regions;

  }

  /**
   * Appends a render array to a region.
   *
   * @todo: Right now we only collect render arrays. They are currently not
   *        rendered through theme_region.
   * @todo: Do we need a weight parameter?
   *
   * @param string $region
   *   The region key.
   * @param array $build
   *   The render array representing some content which will be rendered into
   *   the given region.
   *
   * @return $this
   */
  public function addToRegion($region, array $build) {
    $this->regions[$region][] = $build;
    return $this;
  }

  /**
   * Sets the main content for the page being rendered.
   *
   * @param array $main_content
   *   The render array representing the main content.
   *
   * @return $this
   */
  public function setMainContent(array $main_content) {
    $this->mainContent = $main_content;
    return $this;
  }

  /**
   * Returns the main content of the page being rendered.
   *
   * @return array
   *    The render array representing the main content.
   */
  public function getMainContent() {
    return $this->mainContent;
  }

  /**
   * Sets the title for the page being rendered.
   *
   * @param string|array $title
   *   The page title: either a string for plain titles or a render array for
   *   formatted titles.
   *
   * @return $this
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }
  public function setSeemLayoutable($seem_layoutable) {
    $this->seemLayoutable = $seem_layoutable;
    return $this;
  }
}

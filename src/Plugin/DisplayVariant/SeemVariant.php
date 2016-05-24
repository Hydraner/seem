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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo A better description here.
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
   * The display_variant plugin manager.
   *
   */
  protected $displayVariantPluginManager;

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
  protected $pageVariant;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PluginManagerInterface $blockPluginManager, VariantManager $display_variant) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->blockPluginManager = $blockPluginManager;
    $this->displayVariantPluginManager = $display_variant;
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
      $container->get('plugin.manager.display_variant')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected $regions = [];

  /**
   * {@inheritdoc}
   */
  public function build() {
    $plugin_manager = \Drupal::service('plugin.manager.seem_layout_plugin');
    // Iterate through all available layouts.
    // @todo: Identify the layout by it's context.
    // @todo: Use the context as id for layouts.
    foreach ($plugin_manager->getDefinitions() as $key => $definition) {
      // If the layout matches our suggestion, extract the regions.
      if ($definition['context'] == $this->configuration['suggestion']) {
        foreach ($definition['regions'] as $region => $region_content) {
          foreach ($region_content as $content) {
            // @todo: introduce SeemRenderType plugin.
            if ($content['type'] == 'context') {
              $this->appendRenderArray($region, $this->mainContent);
            }
            else {
              if ($content['type'] == 'markup') {
                $this->appendRenderArray($region, ['#markup' => $content['markup']]);
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
   * @param string $region
   *   The region.
   * @param array $build
   *   The render array.
   *
   * @return $this
   */
  public function appendRenderArray($region, array $build) {
    // @todo: Render "real" regions.
//    if (!isset($this->regions[$region])) {
//      // @todo: Keep in mind that this will be deprecated.
//      $this->regions[$region]['#theme_wrappers'] = 'region';
//      $this->regions[$region]['#region'] = $region;
//    }
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
}

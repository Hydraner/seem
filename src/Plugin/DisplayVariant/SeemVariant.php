<?php

namespace Drupal\seem\Plugin\DisplayVariant;

use Drupal\Core\Display\PageVariantInterface;
use Drupal\Core\Display\VariantBase;
use Drupal\Core\Display\VariantManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Display variant.
 *
 * Provides a variant plugin, which will build a display based on a seem display
 * configuration.
 *
 * @DisplayVariant(
 *   id = "seem_variant",
 *   admin_label = @Translation("Seem"),
 *   no_ui = TRUE,
 * )
 */
class SeemVariant extends VariantBase implements PageVariantInterface, ContainerFactoryPluginInterface {

  /**
   * The variant plugin manager.
   *
   * @var \Drupal\Core\Display\VariantManager
   */
  protected $displayVariantManager;

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface
   */
  protected $seemDisplayManager;

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
   * Constructs a new SeemVariant object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Display\VariantManager $display_variant_manager
   *   The variant plugin manager.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_manager
   *   The seem display plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, VariantManager $display_variant_manager, SeemDisplayManager $seem_display_manager) {
    $this->displayVariantManager = $display_variant_manager;
    $this->seemDisplayManager = $seem_display_manager;

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
      $container->get('plugin.manager.display_variant'),
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * Builds a page variant.
   *
   * Load an instance of the original display variant plugin, since seem only
   * takes control over the content part, we will inject our seem display as
   * main_content into the responsible display_variant.
   */
  public function build() {
    /** @var \Drupal\seem\Plugin\SeemDisplayBase $seem_display */
    $seem_display = $this->seemDisplayManager->createInstance($this->configuration['seem_display']['id']);
    // Use the display_variant's main content to be able to place it in a custom
    // region via a custom seem display.
    $seem_display->setMainContent($this->getMainContent());
    $seem_display->setSeemDisplayable($this->getSeemDisplayable());

    // Get an instance of the original display variant.
    /** @var \Drupal\Core\Display\PageVariantInterface $page_variant */
    $page_variant = $this->displayVariantManager->createInstance($this->configuration['original_display_variant_plugin_id']);

    // Use the seem_displays's build as new main content.
    $page_variant->setMainContent($seem_display->build());

    // This must be called till it's part of the interface.
    $page_variant->setTitle($this->title);

    // Use the parents page_variant build.
    $build = $page_variant->build();

    // Let the seem display have a last look at the build, since it also can
    // manipulate the outer seem regions.
    return $seem_display->pageBuild($build);
  }

  /**
   * {@inheritdoc}
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
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSeemDisplayable() {
    return $this->configuration['seem_displayable'];
  }

}

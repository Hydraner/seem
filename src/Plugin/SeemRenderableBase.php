<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for Seem renderable plugins.
 */
abstract class SeemRenderableBase extends PluginBase implements SeemRenderableInterface {

  /**
   * A region key.
   *
   * @var string
   *   The region key of the current region.
   */
  protected $region_key;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->region_key = $configuration['region_key'];
  }

  /**
   * Adds the possibility to do some extra tasks on the finished build.
   *
   * @param array $build
   *   A build array.
   */
  public function doExtraTasks(&$build) {}

  /**
   * Define default configuration to have the possibility to create
   * soft-dependency settings.
   */
  public function defaultConfiguration() {
    return [];
  }

}

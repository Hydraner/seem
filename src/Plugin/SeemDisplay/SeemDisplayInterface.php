<?php

/**
 * @file
 * Contains \Drupal\seem\Plugin\SeemDisplayPluginInterface.
 */

namespace Drupal\seem\Plugin\SeemDisplay;

use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Provides an interface for SeemDisplay plugins.
 */
interface SeemDisplayInterface extends PluginInspectionInterface, DerivativeInspectionInterface {

  /**
   * Build a render array for layout with regions.
   *
   * @param array $regions
   *   An associative array keyed by region name, containing render arrays
   *   representing the content that should be placed in each region.
   *
   * @return array
   *   Render array for the layout with regions.
   */
//  public function build(array $regions);
  public function build();

}

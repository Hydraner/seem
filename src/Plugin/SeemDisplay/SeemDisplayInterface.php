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
   * Build a render array for a display.
   *
   * @return array
   *   Render array for the layout with regions.
   */
  public function build();

  /**
   * Gets the human-readable name.
   *
   * @return \Drupal\Core\Annotation\Translation|NULL
   *   The human-readable name.
   */
  public function getLabel();

  /**
   * Gets the optional description for advanced layouts.
   *
   * @return \Drupal\Core\Annotation\Translation|NULL
   *   The layout description.
   */
  public function getDescription();

  /**
   * Pass the context from the displayable element to the display, to be able to
   * render it later in the display.
   *
   * @param $main_content
   *   The context's render array.
   * @return \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface $this
   *   The seemDisplay.
   */
  public function setMainContent($main_content);

  /**
   * Get the context's render array.
   *
   * @return array
   *    The context's render array.
   */
  public function getMainContent();

  /**
   * Set the layout for this display.
   *
   * @param string $layout
   *   The layout id.
   * @return \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface $this
   *   The seemDisplay.
   */
  public function setLayout($layout);

  /**
   * Set the layout for this display.
   *
   * @return bool|string
   *   The display's layout.
   */
  public function getLayout();

  /**
   * Set a layout setting for this display.
   *
   * @param string $key
   *   The settings key.
   * @param string $value
   *   The settings value.
   * @return $settings
   *   The settings.
   */
  public function setLayoutSetting($key, $value);

  /**
   * Set a bunch of layout settings at once..
   *
   * @param string $layout_settings
   *   An array containing the layout settings.
   * @return $settings
   *   The settings.
   */
  public function setLayoutSettings($layout_settings);

  /**
   * Overrides the region definitions provided by the display plugin.
   *
   * @param string $regions
   *   An array containing the region definitions..
   * @return $this
   *   The seem_display object.
   */
  public function setRegionDefinitions($regions);

  /**
   * Get the layout settings.
   *
   * @return array|bool
   *   The display's layout settings.
   */
  public function getLayoutSettings();

  /**
   * Gets information on regions keyed by machine name.
   *
   * @return array
   *   An array of information on regions keyed by machine name.
   */
  public function getRegionDefinitions();

  /**
   * Processes a region definitions by calling the related seem_renderable
   * and let him do the magic.
   *
   * @param $region_definition
   *   The region definition.
   * @param $region_key
   *   The region key.
   * @return array $region
   *   A renderable array which representates the region definition from as
   *   defined in the display.
   */
  public function processRegion($region_definition, $region_key);

  /**
   * Processes all regions defined by the display.
   *
   * @return array $regions
   *   A render array containing all processed regions.
   */
  public function getProcessedRegions();

  /**
   * Processes the defined layout.
   *
   * @param $regions
   *   A render array with processed regions. @see $this->getProcessedRegions().
   * @param $layout
   *   A layout id.
   * @param array $configuration
   *   Additional configuration for the layout plugin.
   * @return mixed
   *   A render array with a processed layout.
   */
  public function processLayout($regions, $layout, $configuration = []);
}

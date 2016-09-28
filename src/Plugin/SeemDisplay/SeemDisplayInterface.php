<?php

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
   * Add extra render arrays to the complete displayVariant.
   *
   * This only makes sense in the context of a page, where the blockPageVariant
   * or a pageManagerDisplayVariant may wraps our display.
   *
   * @param array $build
   *   A render array given by a displayVariant.
   *
   * @return array
   *   Manipulated render array from displayVariant.
   */
  public function pageBuild($build);

  /**
   * Gets the human-readable name.
   *
   * @return \Drupal\Core\Annotation\Translation|null
   *   The human-readable name.
   */
  public function getLabel();

  /**
   * Gets the optional description for advanced layouts.
   *
   * @return \Drupal\Core\Annotation\Translation|null
   *   The layout description.
   */
  public function getDescription();

  /**
   * Pass the context from the displayable element to the display.
   *
   * To be able to render it later in the display.
   *
   * @param array $main_content
   *   The context's render array.
   *
   * @return \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface
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
   *
   * @return \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface
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
   *
   * @return array
   *   The settings.
   */
  public function setLayoutSetting($key, $value);

  /**
   * Set a bunch of layout settings at once..
   *
   * @param string $layout_settings
   *   An array containing the layout settings.
   *
   * @return array
   *   The settings.
   */
  public function setLayoutSettings($layout_settings);

  /**
   * Overrides the region definitions provided by the display plugin.
   *
   * @param string $regions
   *   An array containing the region definitions..
   *
   * @return array
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
   * Processes a region definitions by calling the related seem_renderable.
   *
   * @param array $region_definition
   *   The region definition.
   * @param string $region_key
   *   The region key.
   * @param array $build
   *   An optional build array to be able to perform extra tasks on the build
   *   array like hiding existing stuff.
   *
   * @return array
   *   A renderable array which represents the region definition from as defined
   *   in the display.
   */
  public function processRegion($region_definition, $region_key, &$build = []);

  /**
   * Processes all regions defined by the display.
   *
   * @return array
   *   A render array containing all processed regions.
   */
  public function getProcessedRegions();

  /**
   * Processes the defined layout.
   *
   * @param array $regions
   *   A render array with processed regions. @see $this->getProcessedRegions().
   * @param string $layout
   *   A layout id.
   * @param array $configuration
   *   Additional configuration for the layout plugin.
   *
   * @return mixed
   *   A render array with a processed layout.
   */
  public function processLayout($regions, $layout, $configuration = []);

  /**
   * Get information on existing regions.
   *
   * In the context of a page keyed by machine name.
   *
   * @return array
   *   An array of information on regions keyed by machine name.
   */
  public function getExistingRegionsDefinition();

}

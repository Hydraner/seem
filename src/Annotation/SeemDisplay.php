<?php

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a SeemDisplay annotation object.
 *
 * SeemDisplay's are used to define a list of region and it's content.
 *
 * Plugin namespace: Plugin\SeemDisplay
 *
 * @see \Drupal\seem\Plugin\SeemDisplayInterface
 * @see \Drupal\seem\Plugin\SeemDisplayBase
 * @see \Drupal\seem\Plugin\SeemDisplayPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class SeemDisplay extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * An optional description for advanced displays.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description = "";

  /**
   * The context defines the selection rule for a display.
   *
   * The required data is defined by the seem displayable, which will use the
   * context data to select the display for the requested context. For instance
   * for an existing_page displayable, the rout must be provided by the context.
   *
   * @var array
   */
  public $context;

  /**
   * An optional layout from layout_plugins module.
   *
   * It is recommended to use a layout with a display to have some nice markup.
   * When not using a layout, the regions will be rendered without markup.
   *
   * @var string
   */
  public $layout = "none";

  /**
   * Optional settings for the layout.
   *
   * Defined settings will directly be passed to the layout.
   *
   * @var array
   */
  public $layout_settings = [];

  /**
   * An associative array of regions.
   *
   * The key of the array is the machine name of the region, and the value is
   * an associative array with the seem renderable definitions.
   *
   * If a layout is selected, the regions will be mapped directly on the regions
   * defined in the layout.
   *
   * @var array
   */
  public $regions = array();

}

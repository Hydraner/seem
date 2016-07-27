<?php

/**
 * @file
 * Contains Drupal\seem\Annotation\SeemDisplay.
 */

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a SeemDisplay annotation object.
 *
 * SeemDisplay's are used to define a list of region and it's content.
 *
 * @todo: Ajust documentation since this is borrowed from Layout.php.
 *
 * Plugin namespace: Plugin\SeemDisplay
 *
 * @see \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface
 * @see \Drupal\seem\Plugin\SeemDisplay\SeemDisplayBase
 * @see \Drupal\seem\Plugin\SeemDisplay\SeemDisplayPluginManager
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
   * The layout type.
   *
   * Available options:
   *  - full: Layout for the whole page.
   *  - page: Layout for the main page response.
   *  - partial: A partial layout that is typically used for sub-regions.
   *
   * @var string
   */
  public $type = 'page';

  /**
   * The human-readable name.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * An optional description for advanced layouts.
   *
   * Sometimes layouts are so complex that the name is insufficient to describe
   * a layout such that a visually impaired administrator could layout a page
   * for a non-visually impaired audience. If specified, it will provide a
   * description that is used for accessibility purposes.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description = "";
  public $layout = "none";

  public $seem_displayable;
  public $context;

  /**
   * An associative array of regions in this layout.
   *
   * The key of the array is the machine name of the region, and the value is
   * an associative array with the following keys:
   * - label: (string) The human-readable name of the region.
   *
   * Any remaining keys may have special meaning for the given layout plugin,
   * but are undefined here.
   *
   * @var array
   */
  public $regions = array();

}

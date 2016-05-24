<?php

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Seem element type plugin item annotation object.
 *
 * @see \Drupal\seem\Plugin\SeemElementTypePluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class SeemElementTypePlugin extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}

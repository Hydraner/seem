<?php

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for seem's Displayable plugins.
 *
 * @see \Drupal\seem\Plugin\SeemDisplayableManager
 *
 * @Annotation
 */
class SeemDisplayable extends Plugin {

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

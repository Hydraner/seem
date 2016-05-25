<?php

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for seen's layoutable plugins.
 *
 * @see \Drupal\seem\Plugin\SeemLayoutableManager
 *
 * @Annotation
 */
class SeemLayoutable extends Plugin {

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

<?php

namespace Drupal\seem\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Seem renderable item annotation object.
 *
 * @see \Drupal\seem\Plugin\SeemRenderableManager
 * @see plugin_api
 *
 * @Annotation
 */
class SeemRenderable extends Plugin {


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

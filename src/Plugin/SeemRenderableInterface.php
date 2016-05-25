<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\seem\Plugin\DisplayVariant\SeemVariant;

/**
 * Defines an interface for Seem renderable plugins.
 */
interface SeemRenderableInterface extends PluginInspectionInterface {

  /**
   * Returns a render array.
   *
   * @return mixed[]
   *   A render array.
   */
  public function doRenderable($content, SeemVariant $seem_variant);
}

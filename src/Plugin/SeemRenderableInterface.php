<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;

/**
 * Defines an interface for Seem renderable plugins.
 */
interface SeemRenderableInterface extends PluginInspectionInterface {

  /**
   * Returns a render array.
   *
   * @param $content
   *   The content which needs to be rendered.
   * @param \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface $seem_display
   *   The seem_display definition.
   * @return array
   *   A render array.
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display);

}

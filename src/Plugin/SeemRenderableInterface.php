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
   * @param \Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface $seem_display
   * @param $region_key
   * @return \mixed[] A render array.
   * A render array.
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display);
}

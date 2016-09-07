<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem Displayable plugins.
 */
interface SeemDisplayableInterface extends PluginInspectionInterface {

  public function getPattern($element);
}

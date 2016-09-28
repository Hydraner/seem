<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem Displayable plugins.
 */
interface SeemDisplayableInterface extends PluginInspectionInterface {

  /**
   * Determines which parameters are context for the selected displayable.
   *
   * @param array $element
   *   A render element.
   *
   * @return mixed
   *   A context.
   */
  public function getContext($element);

}

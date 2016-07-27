<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem Displayable plugins.
 */
interface SeemDisplayableInterface extends PluginInspectionInterface {


  // Add get/set methods for your plugin type here.
//  function getSuggestions();
  public function getPattern($element);
}

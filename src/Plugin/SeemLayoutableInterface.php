<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem layoutable plugins.
 */
interface SeemLayoutableInterface extends PluginInspectionInterface {


  // Add get/set methods for your plugin type here.
//  function getSuggestions();
  public function getPattern($element);
}

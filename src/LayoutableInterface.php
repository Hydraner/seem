<?php

namespace Drupal\seem;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem element type plugin plugins.
 */
interface LayoutableInterface extends PluginInspectionInterface {


  // Add get/set methods for your plugin type here.
//  function getSuggestions();
  function getPattern($element);
}

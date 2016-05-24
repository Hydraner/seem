<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Seem element type plugin plugins.
 */
interface SeemElementTypePluginInterface extends PluginInspectionInterface {


  // Add get/set methods for your plugin type here.
  function getSuggestions();
}

<?php

namespace Drupal\seem\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for Seem element type plugin plugins.
 */
abstract class SeemDisplayableBase extends PluginBase implements SeemDisplayableInterface {


  // Add common methods and abstract methods for your plugin type here.

  public function getSuggestion() {
    return isset($this->configuration['suggestion']) ? $this->configuration['suggestion'] : NULL;
  }
}

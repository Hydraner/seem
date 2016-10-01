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

  /**
   * Return the seem display config context.
   *
   * The config context describes when a config entity will be loaded for a
   * displayable. The context can be different from the displayable context,
   * since it might be useful to have a configuration on an entity_id basis or
   * based on a path (additionally to the display_context for instance).
   *
   * @param array $display_context
   *   The current display's context.
   *
   * @return array
   *   The context on which basis the config will be stored and loaded.
   */
  public function getConfigContext($display_context);

}

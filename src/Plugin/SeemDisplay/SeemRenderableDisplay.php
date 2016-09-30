<?php

namespace Drupal\seem\Plugin\SeemDisplay;

/**
 * Provides a 'Display' seem_renderable plugin.
 *
 * This is used by the 'display' seem renderable plugin to create fake display
 * instances when nesting displays. So also if this seems to be not in use,
 * make sure not to delete this, since it is required for the display
 * seem_renderable to work.
 *
 * @see \Drupal\seem\Plugin\SeemRenderable\DisplaySeemRenderable::doRenderable
 *
 * @SeemDisplay(
 *   id = "renderarble_display",
 *   admin_label = @Translation("Renderable display"),
 *   label = @Translation("Renderable display"),
 *   seem_displayable = "display",
 * )
 */
class SeemRenderableDisplay extends SeemDisplayDefault {

}

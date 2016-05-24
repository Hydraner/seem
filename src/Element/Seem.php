<?php

namespace Drupal\seem\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\seem\Plugin\DisplayVariant\SeemVariant;

/**
 * Provides a render element for an entire HTML page: <html> plus its children.
 *
 * @RenderElement("seem")
 */
class Seem extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#pre_render' => array(
        array($class, 'preRenderSeemElement'),
      ),
    );
  }

  public function preRenderSeemElement($element) {
    $configuration = [];
    $seem_element_type_plugin = \Drupal::service('plugin.manager.seem_element_type_plugin.processor');
    if ($seem_element_type_plugin->hasDefinition($element['#element_type'])) {
      $element_type = $seem_element_type_plugin->createInstance($element['#element_type']);
      $configuration['suggestion'] = $element_type->getPattern($element);

      $plugin_manager = \Drupal::service('plugin.manager.display_variant');
      $seem = new SeemVariant($configuration, 'seem', $plugin_manager->getDefinition('seem'), \Drupal::service('plugin.manager.block'), $plugin_manager);
      $element['#context']['#rendered'] = TRUE;
      $seem->setMainContent($element['#context']);
      $seem->setTitle('');
      $element['seem'] = $seem->build();
      return $element['seem'];
    }

    return $element['#context'];
  }

}

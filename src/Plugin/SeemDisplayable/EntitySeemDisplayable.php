<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * The seem_displayable plugin type for entities.
 *
 * @SeemDisplayable(
 *   id = "entity",
 *   label = @Translation("Entity")
 * )
 */
class EntitySeemDisplayable extends SeemDisplayableBase {

  /**
   * The context.
   *
   * @var mixed
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $this->context = [
      'entity_type' => $element['#entity_type'],
      'bundle' => $element['#bundle'],
      'view_mode' => $element['#view_mode'],
    ];
    return $this->context;
  }

  /**
   * Get config context.
   *
   * @param array $element
   *   The render element.
   *
   * @return array|mixed
   *   The config context.
   *
   * @todo: Make this work. does it?
   */
  public function getConfigContext($element) {
    $this->context = [
      'entity_type' => $element['#entity_type'],
      'bundle' => $element['#bundle'],
      'view_mode' => $element['#view_mode'],
      'entity_id' => $element['#entity_id'],
    ];
    return $this->context;
  }

}

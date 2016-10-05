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
   * The entities id.
   *
   * @var int
   */
  protected $entityId;

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $entity = $element['#main_content']['#' . $element['#entity_type']];
    $this->entityId = $entity->id();

    return [
      'entity_type' => $element['#entity_type'],
      'bundle' => $element['#bundle'],
      'view_mode' => $element['#view_mode'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigContext($display_context) {
    $debug = 1;
    return $display_context + [
      'entity_id' => $this->entityId
    ];
  }

}

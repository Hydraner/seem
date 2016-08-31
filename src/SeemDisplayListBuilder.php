<?php

namespace Drupal\seem;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Seem display entities.
 */
class SeemDisplayListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Seem display');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['action_header']['#markup'] = '<h3>' . t('Available actions:') . '</h3>';
    $build['action_table'] = parent::render();
//    if (!$this->hasConfigurableActions) {
//      unset($build['action_table']['#header']['operations']);
//    }
    $build['action_admin_manage_form'] = \Drupal::formBuilder()->getForm('Drupal\seem\Form\SeemDisplayAdminManageForm');
    return $build;
  }

}

<?php

namespace Drupal\seem\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SeemDisplayForm.
 *
 * @package Drupal\seem\Form
 */
class SeemDisplayForm extends SeemDisplayFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $display = Json::decode($_GET['display']);
    $display = $display['display'];
    $parameters = Json::decode($_GET['parameters']);


//    $seem_displayable = \Drupal::getContainer()
//      ->get('plugin.manager.seem_displayable.processor')
//      ->createInstance($display['seem_displayable']);


    $form = parent::form($form, $form_state);

//    $seem_display = $this->entity;
//    $form['label'] = array(
//      '#type' => 'textfield',
//      '#title' => $this->t('Label'),
//      '#maxlength' => 255,
//      '#default_value' => $seem_display->label(),
//      '#description' => $this->t("Label for the Seem display."),
//      '#required' => TRUE,
//    );
//
//    $form['id'] = array(
//      '#type' => 'machine_name',
//      '#default_value' => $seem_display->id(),
//      '#machine_name' => array(
//        'exists' => '\Drupal\seem\Entity\SeemDisplay::load',
//      ),
//      '#disabled' => !$seem_display->isNew(),
//    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }


}

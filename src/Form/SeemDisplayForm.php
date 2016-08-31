<?php

namespace Drupal\seem\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SeemDisplayForm.
 *
 * @package Drupal\seem\Form
 */
class SeemDisplayForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $seem_display = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $seem_display->label(),
      '#description' => $this->t("Label for the Seem display."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $seem_display->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\seem\Entity\SeemDisplay::load',
      ),
      '#disabled' => !$seem_display->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $seem_display = $this->entity;
    $status = $seem_display->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Seem display.', [
          '%label' => $seem_display->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Seem display.', [
          '%label' => $seem_display->label(),
        ]));
    }
    $form_state->setRedirectUrl($seem_display->urlInfo('collection'));
  }

}

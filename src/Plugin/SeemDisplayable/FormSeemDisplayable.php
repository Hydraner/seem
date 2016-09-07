<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * @todo A better description here.
 *
 * @SeemDisplayable(
 *   id = "form",
 *   label = @Translation("Form")
 * )
 */
class FormSeemDisplayable extends SeemDisplayableBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    return ['form_id' => $element['#form_id']];
  }

  // @todo: make this work.
  public function getConfigContext($element) {
    return [
      'form_id' => $element['#form_id'],
      'route' => ''
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    return $element['#form_id'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    parent::buildConfigurationForm($form, $form_state);
    // TODO: Implement buildConfigurationForm() method.
    $form['config']['new_submit'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Submit value'),
      // The default value will be set by tge seenDisplayBase.
//      '#default_value' => !$this->entity->isNew() ? $this->entity->get('new_submit') : '',
      '#maxlength' => '255',
      '#description' => $this->t('A unique label for this advanced action. This label will be displayed in the interface of modules that integrate with actions.'),
    );

    return $form;
  }

}

<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\seem\Plugin\SeemDisplayableBase;

/**
 * The seem_displayable plugin type for formula's.
 *
 * @SeemDisplayable(
 *   id = "form",
 *   label = @Translation("Form")
 * )
 */
class FormSeemDisplayable extends SeemDisplayableBase {

  use StringTranslationTrait;

  /**
   * The forms id.
   *
   * @var string
   */
  protected $formId;

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $this->formId = $element['#form_id'];
    return ['form_id' => $element['#form_id']];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigContext($display_context) {
    return $display_context + [
      'form_id' => $this->formId,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    parent::buildConfigurationForm($form, $form_state);
    $form['config']['new_submit'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Submit value'),
      '#maxlength' => '255',
      '#description' => $this->t('A unique label for this advanced action. This label will be displayed in the interface of modules that integrate with actions.'),
    );
    return $form;
  }

}

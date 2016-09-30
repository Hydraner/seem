<?php

/**
 * @file
 * Contains \Drupal\seem_example\Plugin\SeemDisplay\SeemDisplayExampleTest.
 */

namespace Drupal\seem_example\Plugin\SeemDisplay;

use Drupal\Core\Form\FormStateInterface;
use Drupal\seem\Plugin\SeemDisplayBase;

/**
 * @todo: Description
 *
 * @SeemDisplay(
 *   id = "seem_display_example_test",
 *   label = @Translation("Test Display (with settings)"),
 *   seem_displayable = "entity",
 *   context = {
 *      "entity_type" = "node",
 *      "bundle" = "article",
 *      "view_mode" = "teaser"
 *   },
 *   regions = {
 *     "top" = {
 *       {
 *        "type" = "main_content"
 *      }
 *     },
 *    "bottom" = {
 *       {
 *        "type" = "markup",
 *        "markup" = "!!!!Test test test FOO BAR!!!!"
 *      }
 *     }
 *   }
 * )
 */
class SeemDisplayExampleTest extends SeemDisplayBase {

//  function __construct(array $configuration, $plugin_id, $plugin_definition, $layout_manager) {
//    parent::__construct($configuration, $plugin_id, $plugin_definition, $seem_renderable_manager, $layout_manager);
//    $debug = 1;
//  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'setting_1' => 'Default',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->getConfiguration();
    $form['setting_1'] = [
      '#type' => 'textfield',
      '#title' => 'Blah',
      '#default_value' => $configuration['setting_1'],
    ];
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['setting_1'] = $form_state->getValue('setting_1');
  }

}

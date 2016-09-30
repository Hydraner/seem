<?php

/**
 * @file
 * Contains \Drupal\seem_example\Plugin\SeemDisplay\SeemDisplayNodeArticleFullDisplay
 */

namespace Drupal\seem_example\Plugin\SeemDisplay;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\layout_plugin\Plugin\Layout\LayoutBase;
use Drupal\seem\Plugin\SeemDisplayBase;

class SeemDisplayNodeArticleFullDisplay extends SeemDisplayBase {

  /**
   * @todo:
   * Make it possible to add regions
   * Make it possible to add render arrays to regions
   * Add response
   * Get/Manipulate Kontext
   */
//  use StringTranslationTrait;

  public function getRegionDefinitions() {
    $region_definitions = parent::getRegionDefinitions();
    $region_definitions['second'][] = [
      'type' => 'markup',
      'markup' => 'NICE SOLLTE DAS SEIN',
    ];

    return $region_definitions;
  }

  public function getProcessedRegions() {
    $regions = parent::getProcessedRegions();

    $regions['second'][] = ['#markup' =>  new TranslatableMarkup('MIAU MIAU MIAU')];
    return $regions;
  }


  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
//    return parent::defaultConfiguration() + [
//      'extra_classes' => 'Default',
//    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $configuration = $this->getConfiguration();
    $form['on'] = [
      '#type' => 'boolean',
      '#title' => $this->t('News active'),
      '#default_value' => $configuration['on'],
    ];
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

//    $this->configuration['extra_classes'] = $form_state->getValue('extra_classes');
  }

}

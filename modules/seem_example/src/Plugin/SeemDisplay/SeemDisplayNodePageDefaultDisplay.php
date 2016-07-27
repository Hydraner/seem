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
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayBase;

class SeemDisplayNodePageDefaultDisplay extends SeemDisplayBase {



  public function getExistingRegions() {
    $existing_regions = parent::getExistingRegions();

    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node->bundle() == 'page') {
      $existing_regions['sidebar_first'][] = [
          'type' => 'hide',
          'key' => 'bartik_tools'
        ];
    }

    return $existing_regions;
  }


  /**
   * {@inheritdoc}
   */
//  public function defaultConfiguration() {
////    return parent::defaultConfiguration() + [
////      'extra_classes' => 'Default',
////    ];
//  }
//
//  /**
//   * {@inheritdoc}
//   */
//  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
////    $configuration = $this->getConfiguration();
////    $form['extra_classes'] = [
////      '#type' => 'textfield',
////      '#title' => $this->t('Extra classes'),
////      '#default_value' => $configuration['extra_classes'],
////    ];
////    return $form;
//  }
//
//  /**
//   * @inheritDoc
//   */
//  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
//    parent::submitConfigurationForm($form, $form_state);
//
////    $this->configuration['extra_classes'] = $form_state->getValue('extra_classes');
//  }

}

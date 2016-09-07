<?php

namespace Drupal\seem\Plugin\SeemDisplayable;

use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Entity\EntityTypeBundleInfo;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\seem\Plugin\SeemDisplayableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo A better description here.
 *
 * @SeemDisplayable(
 *   id = "entity",
 *   label = @Translation("Entity")
 * )
 */
class EntitySeemDisplayable extends SeemDisplayableBase implements ContainerFactoryPluginInterface {

  protected $entityDisplayRepository;
  protected $entityTypeBundleInfo;
  protected $entityTypeManager;
  protected $context;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityDisplayRepository $entity_display_repository, EntityTypeBundleInfo $entity_type_bundle_info) {
    $this->entityDisplayRepository = $entity_display_repository;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityTypeManager = \Drupal::entityTypeManager();

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_display.repository'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getContext($element) {
    $this->context = [
      'entity_type' => $element['#entity_type'],
      'bundle' => $element['#bundle'],
      'view_mode' => $element['#view_mode']
    ];

    return $this->context;
  }

  // @todo: Make this work. does it?
  public function getConfigContext($element) {
    $this->context = [
      'entity_type' => $element['#entity_type'],
      'bundle' => $element['#bundle'],
      'view_mode' => $element['#view_mode'],
      'entity_id' => $element['#entity_id']
    ];

    return $this->context;
  }

  /**
   * {@inheritdoc}
   */
  public function getPattern($element) {
    return $element['#entity_type'] . '__' . $element['#bundle'] . '__' . $element['#view_mode'];
  }

}

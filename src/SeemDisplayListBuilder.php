<?php

namespace Drupal\seem;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Seem display entities and plugins.
 */
class SeemDisplayListBuilder extends ConfigEntityListBuilder {

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
   */
  protected $seemDisplayManager;

  /**
   * Constructs a new ActionAdminManageForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\seem\SeemDisplayManager $seem_display_manager
   *   The seem display manager
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, $seem_display_manager) {
    $this->seemDisplayManager = $seem_display_manager;
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    return $this->buildPluginHeader() + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['plugin_header']['#markup'] = '<h3>' . t('Display plugins:') . '</h3>';
    $build['plugin_table'] = $this->renderPluginList();

    $build['config_header']['#markup'] = '<br /><h3>' . t('Overridden displays:') . '</h3>';
    $build['config_table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * We need to add some context to the links in order to get the right config
   * loaded in the form, since the form is not loaded by entity_id but instead
   * by the context it belongs to.
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    /** @var \Drupal\Core\Url $edit_url */
    $edit_url = $operations['edit']['url'];
    $edit_url->setRouteParameter('parameters', Json::encode($entity->getParameters()));
    $edit_url->setRouteParameter('display', Json::encode(['display' => $this->seemDisplayManager->getDefinitionByContext($entity->getContext())]));

    return $operations;
  }

  /**
   * Builds the header row for the plugin listing.
   *
   * @return array
   *   A render array structure of header strings.
   */
  public function buildPluginHeader() {
    $header['label'] = $this->t('Seem display');
    $header['id'] = $this->t('Machine name');

    return $header;
  }

  /**
   * Builds a row for an plugin in the plugin listing.
   *
   * @param $definition
   *   The plugins definition.
   *
   * @return array
   *   A render array structure of fields for this plugin.
   */
  public function buildPluginRow($definition) {
    $row['label'] = $definition['label'];
    $row['id'] = $definition['id'];

    return $row;
  }

  /**
   * Builds a listing of plugins.as renderable array for table.html.twig.
   *
   * @return array
   *   A render array as expected by drupal_render().
   */
  public function renderPluginList() {
    $build['table'] = [
      '#type' => 'table',
      '#header' => $this->buildPluginHeader(),
      '#title' => $this->getTitle(),
      '#rows' => [],
      '#empty' => $this->t('There is no @label yet.', ['@label' => $this->entityType->getLabel()]),
      '#cache' => [
        'contexts' => $this->entityType->getListCacheContexts(),
        'tags' => $this->entityType->getListCacheTags(),
      ],
    ];

    foreach ($this->seemDisplayManager->getDefinitions() as $id => $definition) {
      if ($row = $this->buildPluginRow($definition)) {
        $build['table']['#rows'][$definition['id']] = $row;
      }
    }

    // Only add the pager if a limit is specified.
    if ($this->limit) {
      $build['pager'] = array(
        '#type' => 'pager',
      );
    }

    return $build;
  }

}

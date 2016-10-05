<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\block_content\Entity\BlockContent;
use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\devel_generate\Plugin\DevelGenerate\ContentDevelGenerate;
use Drupal\seem\Plugin\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a content block.
 *
 * @SeemRenderable(
 *   id = "entity",
 *   label = @Translation("Entity")
 * )
 */
class EntitySeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The menu tree.
   */
  protected $entityTypeManager;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
      array $configuration,
      $plugin_id,
      $plugin_definition,
      EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
      ContainerInterface $container,
      array $configuration,
      $plugin_id,
      $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $entity_type = $content['entity_type'];
    $entity_id = $content['id'];
    $view_mode = $content['view_mode'];

    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($entity_id);
    if ($entity) {
      return $this->entityTypeManager->getViewBuilder($entity_type)
        ->view($entity, $view_mode);
    }

    return $this->doDummy($content, $seem_display);
  }

  /**
   * Do a dummy.
   *
   * @param $content
   * @param \Drupal\seem\Plugin\SeemDisplayInterface $seem_display
   * @return array
   */
  public function doDummy($content, SeemDisplayInterface $seem_display) {
    $entity_type = $content['entity_type'];
    $entity_id = $content['id'];
    $view_mode = $content['view_mode'];

    /** @var \Drupal\Component\Uuid\Php $uuid */
    $uuid = \Drupal::service('uuid');
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)
      ->create([
        'nid' => $entity_id,
        'type' => 'article',
        'created' => time(),
        'changed' => time(),
        'revision_timestamp' => time(),
      ]);
    $entity->in_preview = TRUE;
    $controller = new EntityViewController(
      \Drupal::getContainer()->get('entity.manager'),
      \Drupal::getContainer()->get('renderer')
    );
    ContentDevelGenerate::populateFields($entity, [
      'created',
      'changed',
      'revision_timestamp',
    ]);
    return $controller->view($entity, $view_mode);
  }

}

<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\devel_generate\DevelGenerateBase;
use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\seem\Plugin\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a content block.
 *
 * @SeemRenderable(
 *   id = "content_block",
 *   label = @Translation("Markup")
 * )
 */
class ContentBlockSeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

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
    parent::__construct($configuration, $plugin_id, $plugin_definition);
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
    $bid = $content['bid'];
    $block = $this->entityTypeManager->getStorage('block_content')->load($bid);
    if ($block) {
      return $this->entityTypeManager->getViewBuilder('block_content')
        ->view($block);
    }
  }

}

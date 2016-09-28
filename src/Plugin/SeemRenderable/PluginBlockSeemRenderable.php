<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\Core\Block\BlockManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\seem\Plugin\SeemDisplay\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a plugin block.
 *
 * @SeemRenderable(
 *   id = "plugin_block",
 *   label = @Translation("Markup")
 * )
 */
class PluginBlockSeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $blockManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new Seem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Block\BlockManager $block_manager
   *   The block manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockManager $block_manager, AccountInterface $current_user) {
    $this->blockManager = $block_manager;
    $this->currentUser = $current_user;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $config = isset($content['settings']) ? $content['settings'] : [];
    $plugin_block = $this->blockManager->createInstance($content['plugin_id'], $config);
    if ($plugin_block instanceof AccessInterface) {
      // Some blocks might implement access check.
      $access_result = $this->blockManager->access($this->currentUser);
      // Return empty render array if user doesn't have access.
      if ($access_result->isForbidden()) {
        // @todo: We might need to add some cache tags/contexts.
        return [];
      }
    }
    // In some cases, you need to add the cache tags/context depending on
    // the block implemention. As it's possible to add the cache tags and
    // contexts in the render method and in ::getCacheTags and
    // ::getCacheContexts methods.
    return $plugin_block->build();
  }

}

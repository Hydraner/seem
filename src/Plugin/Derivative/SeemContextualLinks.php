<?php

namespace Drupal\seem\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\config_translation\ConfigMapperManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The idea is, to have contextual links on all the displayables which have or
 * may not have displays attached to it. This way we could provide a flexible
 * way to configure the displays from the UI in-place.
 */
class SeemContextualLinks extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The mapper plugin discovery service.
   *
   * @var \Drupal\config_translation\ConfigMapperManagerInterface
   */
  protected $mapperManager;

  /**
   * Constructs a new ConfigTranslationContextualLinks.
   *
   * @param \Drupal\config_translation\ConfigMapperManagerInterface $mapper_manager
   *   The mapper plugin discovery service.
   */
  public function __construct() {
//    $this->mapperManager = $mapper_manager;
    $debug = 1;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    $debug = 1;
    return new static();
//    return new static(
//      $container->get('plugin.manager.config_translation.mapper')
//    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Create contextual links for all mappers.
//    $mappers = $this->mapperManager->getMappers();
//    foreach ($mappers as $plugin_id => $mapper) {
//      // @todo Contextual groups do not map to entity types in a predictable
//      //   way. See https://www.drupal.org/node/2134841 to make them
//      //   predictable.
//      $group_name = $mapper->getContextualLinkGroup();
//      if (empty($group_name)) {
//        continue;
//      }
//
//      /** @var \Drupal\config_translation\ConfigMapperInterface $mapper */
//      $route_name = $mapper->getOverviewRouteName();
//      $this->derivatives[$route_name] = $base_plugin_definition;
//      $this->derivatives[$route_name]['config_translation_plugin_id'] = $plugin_id;
//      $this->derivatives[$route_name]['class'] = '\Drupal\config_translation\Plugin\Menu\ContextualLink\ConfigTranslationContextualLink';
//      $this->derivatives[$route_name]['route_name'] = $route_name;
//      $this->derivatives[$route_name]['group'] = $group_name;
    $route_name = 'entity.node.edit_form';
    $group_name = 'node';
    $plugin_id = 'Test';
//      $this->derivatives[$route_name] = $base_plugin_definition;
//      $this->derivatives[$route_name]['config_translation_plugin_id'] = $plugin_id;
//      $this->derivatives[$route_name]['class'] = '\Drupal\config_translation\Plugin\Menu\ContextualLink\ConfigTranslationContextualLink';
      $this->derivatives[$route_name]['title'] = 'TEST!"§2222';
      $this->derivatives[$route_name]['route_name'] = $route_name;
      $this->derivatives[$route_name]['group'] = $group_name;
//    }
    // @todo: Generate route based on parameters defined in Displaybaöe
    // @todo: Generate contextual link based on generated route pattern.
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}

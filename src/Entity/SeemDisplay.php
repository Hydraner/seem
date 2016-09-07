<?php

namespace Drupal\seem\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Seem display entity.
 *
 * @ConfigEntityType(
 *   id = "seem_display",
 *   label = @Translation("Seem display"),
 *   handlers = {
 *     "list_builder" = "Drupal\seem\SeemDisplayListBuilder",
 *     "form" = {
 *       "add" = "Drupal\seem\Form\SeemDisplayForm",
 *       "edit" = "Drupal\seem\Form\SeemDisplayForm",
 *       "delete" = "Drupal\seem\Form\SeemDisplayDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\seem\SeemDisplayHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "seem_display",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/seem_display/{seem_display}",
 *     "add-form" = "/admin/structure/seem_display/add",
 *     "edit-form" = "/admin/structure/seem_display/{seem_display}/edit",
 *     "delete-form" = "/admin/structure/seem_display/{seem_display}/delete",
 *     "collection" = "/admin/structure/seem_display"
 *   }
 * )
 */
class SeemDisplay extends ConfigEntityBase implements SeemDisplayInterface {

  /**
   * The Seem display ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Seem display label.
   *
   * @var string
   */
  protected $label;

  protected $config = [];
  protected $context = [];
  protected $plugin;
  protected $parameters = [];

  public function getParameters() {
    return $this->parameters;
  }
  public function getContext() {
    return $this->context;
  }
}

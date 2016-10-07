<?php

namespace Drupal\seem\Plugin\SeemRenderable;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\seem\Plugin\SeemDisplayInterface;
use Drupal\seem\Plugin\SeemRenderableBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders a contact form.
 *
 * @SeemRenderable(
 *   id = "contact_form",
 *   label = @Translation("Contact form")
 * )
 */
class ContactFormSeemRenderable extends SeemRenderableBase implements ContainerFactoryPluginInterface {

  use UrlGeneratorTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The menu tree.
   */
  protected $entityTypeManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The config factory
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityFormBuilder $entity_form_builder
   *   The entity form builder.
   * @param \Drupal\Core\Session\AccountInterface
   *   The current user.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    RendererInterface $renderer,
    ConfigFactory $config_factory,
    EntityFormBuilder $entity_form_builder,
    AccountInterface $current_user
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->configFactory = $config_factory;
    $this->entityFormBuilder = $entity_form_builder;
    $this->currentUser = $current_user;
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
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('config.factory'),
      $container->get('entity.form_builder'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function doRenderable($content, SeemDisplayInterface $seem_display) {
    $contact_form = [];
    $config = $this->configFactory->get('contact.settings');

    // Try to load the passed contact form based on the id.
    if (isset($content['id'])) {
      $contact_form_id = $content['id'];
      $contact_form = $this->entityManager()
        ->getStorage('contact_form')
        ->load($contact_form_id);
    }

    // Use the default form if no form has been passed.
    if (empty($contact_form)) {
      $contact_form = $this->entityTypeManager->getStorage('contact_form')
        ->load($config->get('default_form'));
      // If there are no forms, do not display the form.
      if (empty($contact_form)) {
        if ($this->currentUser->hasPermission('administer contact forms')) {
          drupal_set_message($this->t('The contact form has not been configured. <a href=":add">Add one or more forms</a> .', array(
            ':add' => $this->url('contact.form_add')
          )), 'error');
          return [];
        }
      }
    }

    $message = $this->entityTypeManager->getStorage('contact_message')
      ->create([
        'contact_form' => $contact_form->id()
      ]);

    $form = $this->entityFormBuilder->getForm($message);
    $form['#title'] = $contact_form->label();
    $form['#cache']['contexts'][] = 'user.permissions';
    $this->renderer->addCacheableDependency($form, $config);
    return $form;
  }

}

<?php

namespace Drupal\seem\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base form for action forms.
 */
abstract class SeemDisplayFormBase extends EntityForm {

  /**
   * The action plugin being configured.
   *
   * @var \Drupal\Core\Action\ActionInterface
   */
  protected $plugin;

  /**
   * The action storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a new action form.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The action storage.
   */
  public function __construct(EntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('seem_display')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (isset($_POST['dialogOptions']) && isset($_POST['dialogOptions']['plugin_id'])) {
      $parameters = $_POST['dialogOptions']['parameters'];
      $context = $_POST['dialogOptions']['context'];
      $plugin_id = $_POST['dialogOptions']['plugin_id'];

      $seem_displayable = \Drupal::getContainer()
        ->get('plugin.manager.seem_displayable.processor')
        ->createInstance($plugin_id);
      // @todo: User LazyPluginCollection
      $this->plugin = $seem_displayable;

      $form_state->setUserInput($form_state->getUserInput() + [
        'plugin' => $plugin_id,
        'context' => $context,
        'parameters' => $parameters
      ]);
//
//      $form_state->setValue('plugin', $plugin_id);
//      $form_state->setValue('context', $context);
//      $form_state->setValue('parameters', $parameters);
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $debug = 1;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $this->entity->label(),
      '#maxlength' => '255',
      '#description' => $this->t('A unique label for this advanced action. This label will be displayed in the interface of modules that integrate with actions.'),
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#disabled' => !$this->entity->isNew(),
      '#maxlength' => 64,
      '#description' => $this->t('A unique name for this action. It must only contain lowercase letters, numbers and underscores.'),
      '#machine_name' => array(
        'exists' => array($this, 'exists'),
      ),
    );
    $input = $form_state->getUserInput();
    $form['plugin'] = array(
      '#type' => 'hidden',
      '#value' => $input['plugin'],
    );
    $form['context'] = array(
      '#type' => 'hidden',
      '#value' => $input['context'],
    );
    $form['parameters'] = array(
      '#type' => 'hidden',
      '#value' => $input['parameters'],
    );

    if (!$this->plugin) {
      $this->plugin = \Drupal::getContainer()
        ->get('plugin.manager.seem_displayable.processor')
        ->createInstance($input['plugin']);
    }
      $form += $this->plugin->buildConfigurationForm($form, $form_state);

      $form['config_keys'] = [
        '#type' => 'hidden',
        '#value' => isset($form['config']) ? array_keys($form['config']) : [],
      ];

      if (!$this->entity->isNew()) {
        foreach ($this->entity->get('config') as $config => $value) {
          $form['config'][$config]['#default_value'] = $value;
        }
      }
    return parent::form($form, $form_state);
  }

  /**
   * Determines if the action already exists.
   *
   * @param string $id
   *   The action ID.
   *
   * @return bool
   *   TRUE if the action exists, FALSE otherwise.
   */
  public function exists($id) {
    $action = $this->storage->load($id);
    return !empty($action);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    unset($actions['delete']);
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($this->plugin instanceof PluginFormInterface) {
      $this->plugin->validateConfigurationForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    if ($this->plugin instanceof PluginFormInterface) {
      $this->plugin->submitConfigurationForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $seem_display = $this->entity;
    $data = [];
    foreach ($form['config_keys']['#value'] as $delta => $key) {
      $data[$key] = $form_state->getValue($key);
    }
    $seem_display->set('config', $data);
    $seem_display->set('parameters', Json::decode($form_state->getValue('parameters')));
    $seem_display->set('context', Json::decode($form_state->getValue('context')));
    $seem_display->set('plugin', $form_state->getValue('plugin'));
    $status = $seem_display->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Seem display.', [
          '%label' => $seem_display->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Seem display.', [
          '%label' => $seem_display->label(),
        ]));
    }
    // $form_state->setRedirectUrl($seem_display->urlInfo('collection'));
  }

  /**
   * {@inheritdoc}
   */
//  public function save(array $form, FormStateInterface $form_state) {
//    $this->entity->save();
//    drupal_set_message($this->t('The action has been successfully saved.'));
//
////    $form_state->setRedirect('entity.action.collection');
//  }

}

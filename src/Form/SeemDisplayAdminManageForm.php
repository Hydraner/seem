<?php

namespace Drupal\seem\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormStateInterface;
use Drupal\seem\SeemDisplayManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a configuration form for configurable seem displays.
 */
class SeemDisplayAdminManageForm extends FormBase {

  /**
   * The seem_display plugin manager.
   *
   * @var \Drupal\seem\SeemDisplayManagerInterface.
   */
  protected $seemDisplayManager;

  /**
   * Constructs a new ActionAdminManageForm.
   *
   * @param \Drupal\seem\SeemDisplayManager $seem_display_manager
   *   The seem display manager
   */
  public function __construct(SeemDisplayManager $seem_display_manager) {
    $this->seemDisplayManager = $seem_display_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.seem_display')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'seem_display_admin_manage';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $actions = array();
    foreach ($this->seemDisplayManager->getDefinitions() as $id => $definition) {
      if (is_subclass_of($definition['class'], '\Drupal\Core\Plugin\PluginFormInterface')) {
        $key = Crypt::hashBase64($id);
        $actions[$key] = $definition['label'] . '...';
      }
    }
    $form['parent'] = array(
      '#type' => 'details',
      '#title' => $this->t('Create an advanced action'),
      '#attributes' => array('class' => array('container-inline')),
      '#open' => TRUE,
    );
    $form['parent']['action'] = array(
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#title_display' => 'invisible',
      '#options' => $actions,
      '#empty_option' => $this->t('Choose an advanced action'),
    );
    $form['parent']['actions'] = array(
      '#type' => 'actions'
    );
    $form['parent']['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Create'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('action')) {
      $form_state->setRedirect(
        'action.admin_add',
        array('action_id' => $form_state->getValue('action'))
      );
    }
  }

}

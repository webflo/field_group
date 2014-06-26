<?php

/**
 * @file
 * Contains \Drupal\field_ui\Form\FieldInstanceConfigDeleteForm.
 */

namespace Drupal\field_group\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;

/**
 * Provides a form for removing a fieldgroup from a bundle.
 */
class FieldGroupDeleteForm extends ConfirmFormBase {

  /**
   * The fieldgroup to delete.
   *
   * @var stdClass
   */
  protected $fieldGroup;

  /**
   * Construct the delete form: get the group config out of the request.
   * @param Request $request
   */
  public function __construct(Request $request) {
    $this->fieldGroup = (object)$request->attributes->get('field_group');
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'field_group_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $field_group = '', $test = '') {
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {

    $bundles = entity_get_bundles();
    $bundle_label = $bundles[$this->fieldGroup->entity_type][$this->fieldGroup->bundle]['label'];

    field_group_group_delete($this->fieldGroup);
    \Drupal::cache()->invalidate('field_groups');
    
    drupal_set_message(t('The group %group has been deleted from the %type content type.', array('%group' => t($this->fieldGroup->label), '%type' => $bundle_label)));

    // Redirect.
    $form_state['redirect_route'] = $this->getCancelRoute();

  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the group %group?', array('%group' => t($this->fieldGroup->label)));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {

    $entity_type_id = $this->fieldGroup->entity_type;
    $entity_type = \Drupal::entityManager()->getDefinition($entity_type_id);
    if (!$entity_type->hasLinkTemplate('admin-form')) {
      return;
    }

    $options = array(
      $entity_type->getBundleEntityType() => $this->fieldGroup->bundle,
    );

    // Redirect to correct route.
    if ($this->fieldGroup->context == 'form') {
      if ($this->fieldGroup->mode == 'default') {
        $route_name = "field_ui.form_display_overview_$entity_type_id";
      }
      else {
        $route_name = "field_ui.form_display_overview_form_mode_$entity_type_id";
        $options['form_mode_name'] = $this->fieldGroup->mode;
      }
    }
    else {
      if ($this->fieldGroup->mode == 'default') {
        $route_name = "field_ui.display_overview_$entity_type_id";
      }
      else {
        $route_name = "field_ui.display_overview_view_mode_$entity_type_id";
        $options['view_mode_name'] = $this->fieldGroup->mode;
      }
    }

    return new Url($route_name, $options);

  }

}

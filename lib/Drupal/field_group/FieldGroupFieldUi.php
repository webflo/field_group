<?php
/**
 * @file
 * Contains \Drupal\field_group\FieldGroupFieldUi.
 */

namespace Drupal\field_group;

use Drupal\Component\Uuid\Uuid;
use Drupal\Core\Entity\EntityStorageControllerInterface;

/**
 * Provides, well we will see.
 */
class FieldGroupFieldUi {

  protected $storageController;
  private $form;
  // private $form_state;

  public function __construct(EntityStorageControllerInterface $storage_controller) {
    $this->storageController = $storage_controller;
  }

  public function setFormData(&$form, &$form_state, $form_id) {
    $this->form = $form;
    // $this->form_state = $form_state;
  }

  public function submitForm(&$form, &$form_state) {
    $values = $form_state['input']['fields'];

    // dsm($form_state);

    $save_to_field_group = array();
    // TODO: Save field group changes on existing ones.
    foreach ($this->getDraggableFields() as $key => $field_name) {
      // dsm($values[$field_name]);
      if(!empty($values[$field_name]['parent'])) {
        $parent = $values[$field_name]['parent'];
        $save_to_field_group[$parent][$field_name] = $field_name;
      }
    }

    // If _add_new_field_group is used.
    if(!empty($values['_add_new_field_group']['field_name'])) {
      $this->addNewFieldGroup($values['_add_new_field_group'], $save_to_field_group['_add_new_field_group']);
    }

    // Save existing field_groups.
    foreach ($save_to_field_group as $field_group_id => $value) {
      $id = $this->getEntityType() . '.' . $this->getBundle() . '.' . $this->getMode() . '.' . $field_group_id;
      $storage_controller = \Drupal::entityManager()->getStorageController('field_group');
      $entity = $storage_controller->load($id);
      dsm($entity);
      // $entity = reset($entity);
      $entity->set('parent', $parent);
      $entity->set('fields', $value);
      $entity->set('widget_type', $values[$field_group_id]['widget_type']);
      $entity->save($entity);
    }

  }

  private function addNewFieldGroup($values, $fields) {
    $machine_name = 'field_group_' . $values['field_name'];
    $field_group_id = $this->getEntityType() . '.' . $this->getBundle() . '.' . $this->getMode() . '.' . $machine_name;

    $widget_type = $values['widget_type'];
    $parent = $values['parent'];
    dsm($parent);

    $uuid = new Uuid();
    $field_group = array(
      'id' => $field_group_id,
      // 'field_order' => $field_order,
      // 'field_groups' => $field_group,
      'entity_type' => $this->getEntityType(),
      'bundle' => $this->getBundle(),
      'mode' => $this->getMode(),
      'widget_type' => $widget_type,
      'fields' => $fields,
      'parent' => $parent,
      'machine_name' => $machine_name,
      'uuid' => $uuid->generate(),
      'label' => 'test',
    );

    $storage_controller = \Drupal::entityManager()->getStorageController('field_group');
    $entity = $storage_controller->create($field_group);
    $entity->save();
  }

  private function getEntityType() {
    return $this->form['#entity_type'];
  }
  private function getBundle() {
    return $this->form['#bundle'];
  }
  private function getMode() {
    return isset($this->form['#view_mode']) ? $this->form['#view_mode'] : 'form';
  }

  public function getFieldGroups() {
    $field_groups = \Drupal::entityQuery('field_group')
                   ->condition('entity_type', $this->getEntityType())
                   ->condition('bundle', $this->getBundle())
                   ->condition('mode', $this->getMode())
                   ->execute();
   return !empty($field_groups) ? $field_groups : array();
  }

  public function getMachineNames() {
    $machine_names = array();
    $field_groups = $this->getFieldGroups();

    $storage_controller = \Drupal::entityManager()->getStorageController('field_group');
    $field_groups = isset($field_groups) ? $storage_controller->loadMultiple($field_groups) : array();
    foreach($field_groups as $entity) {
      $machine_name = $entity->machine_name;
      $machine_names[$machine_name] = $machine_name;
    }
    return array_keys($machine_names);
  }

  public function getDraggableFields() {
    $fieldGroupKeys = $this->getMachineNames();
    return array_merge($this->form['#fields'], $this->form['#extra'], array(
        '_add_new_field',
        '_add_existing_field',
        '_add_new_field_group',
      ),
      $fieldGroupKeys
    );
  }

}

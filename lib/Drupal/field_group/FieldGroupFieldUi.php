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
  private $entity_type;
  private $bundle;
  private $display_mode;
  private $view_mode;
  // private $form_state;

  public function __construct(EntityStorageControllerInterface $storage_controller) {
    $this->storageController = $storage_controller;
  }

  public function setFormData(&$form, $entity_type, $bundle, $display_mode, $view_mode) {
    $this->form = $form;

    $this->entity_type = $entity_type;
    $this->bundle = $bundle;
    $this->display_mode = $display_mode;
    $this->view_mode = $view_mode;
    // $this->form_state = $form_state;
  }

  public function submitForm(&$form, &$form_state) {
    $values = $form_state['input']['fields'];

    // dsm($form_state);

    // TODO: SAVE EMPTY FIELD GROUPS.
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

    // TODO: This is still crappy. Empty field groups are not stored correctly.
    // TODO: Make it possible to save _add_new_field_group together with nested fields.
    //       This might become a little tricky :/
    $already_saved = array();
    $storage_controller = \Drupal::entityManager()->getStorageController('field_group');
    // Save existing field_groups.
    foreach ($save_to_field_group as $field_group_id => $value) {
      $id = $this->getEntityType() . '.' . $this->getBundle() . '.' . $this->getDisplayMode() . '.' . $this->getViewmode() . '.' . $field_group_id;
      $already_saved += array($id => $id);
      // dsm($id);
      $entity = $storage_controller->load($id);
      $entity->set('parent', $parent);
      $entity->set('fields', $value);
      $entity->set('widget_type', $values[$field_group_id]['widget_type']);
      $entity->save($entity);
    }

    // We assume that a field_group which is not saved above, is an empty one.
    foreach ($this->getFieldGroups() as $key => $key) {
      if(!in_array($key, $already_saved)) {
        $entity = $storage_controller->load($key);
        $entity->set('parent', $parent);
        $entity->set('fields', array());
        $entity->set('widget_type', $values[$field_group_id]['widget_type']);
        $entity->save($entity);
      }
    }
  }

  private function addNewFieldGroup($values, $fields) {
    $machine_name = 'field_group_' . $values['field_name'];
    $field_group_id = $this->getEntityType() . '.' . $this->getBundle() . '.' . $this->getDisplayMode() . '.' . $this->getViewmode() . '.' . $machine_name;

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
      'display_mode' => $this->getDisplayMode(),
      'view_mode' => $this->getViewMode(),
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
    return $this->entity_type;
  }
  private function getBundle() {
    return $this->bundle;
  }
  private function getDisplayMode() {
    return $this->display_mode;
  }
  private function getViewMode() {
    return $this->view_mode;
  }

  public function getFieldGroups() {
    $field_groups = \Drupal::entityQuery('field_group')
                   ->condition('entity_type', $this->entity_type)
                   ->condition('bundle', $this->bundle)
                   ->condition('display_mode', $this->display_mode)
                   ->condition('view_mode', $this->view_mode)
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

<?php

/**
 * @file
 * Contains \Drupal\views\FieldGroup.
 */

namespace Drupal\field_group;

use Drupal;

/**
 * TODO.
 */
class FieldGroup {

  // private $form;
  // private $form_state;
  // private $form_id;

  private $entity_type;
  private $bundle;
  private $display_mode;
  private $view_mode;

  public function __construct($entity_type, $bundle, $display_mode, $view_mode) {
    $this->entity_type = $entity_type;
    $this->bundle = $bundle;
    $this->display_mode = $display_mode;
    $this->view_mode = $view_mode;
  }

  /**
   * Returns the plugin manager for a certain field_group plugin type.
   *
   * @param string $type
   *   The plugin type, for example filter.
   *
   * @return \Drupal\views\Plugin\FieldGroupPluginManager
   */
  public static function pluginManager($type) {
    return Drupal::service('plugin.manager.field_group.' . $type);
  }

  public function getFieldGroups() {
    return Drupal::entityQuery('field_group')
                   ->condition('entity_type', $this->entity_type)
                   ->condition('bundle', $this->bundle)
                   ->condition('display_mode', $this->display_mode)
                   ->condition('view_mode', $this->view_mode)
                   ->execute();
  }





  private function getMachineNames() {
    $machine_names = array();
    foreach($this->getFieldGroups() as $id) {
      $machine_name = config('field_group.' . $id)->get('machine_name');
      $machine_names[] = $machine_name;
    }
    return $machine_names;
  }

  public function getDraggableFields() {
    $options = $this->getFieldgroupInstance($this->getMachineNames());
    $options = (array) array_keys($options);

    return array_merge($this->form['#fields'], $this->form['#extra'], array(
        '_add_new_field',
        '_add_existing_field',
        '_add_new_field_group',
      ),
      $options
    );
  }

  public function add_new_fieldgroup($values) {
    $machine_name = 'field_group_' . $values['field_name'];
    $field_group_id = $this->entity_type . '.' . $this->bundle . '.' . $this->mode . '.' . $machine_name;

    $widget_type = $values['widget_type'];
    $parent = $values['parent'];

    $uuid = new Uuid();
    $field_group = array(
      'id' => $field_group_id,
      // 'field_order' => $field_order,
      // 'field_groups' => $field_group,
      'entity_type' => $this->entity_type,
      'bundle' => $this->bundle,
      'display_mode' => $this->display_mode,
      'view_mode' => $this->view_mode,
      'widget_type' => $widget_type,
      'parent' => $parent,
      'machine_name' => $machine_name,
      'uuid' => $uuid->generate(),
      'label' => 'test',
    );

    $entity = entity_create('field_group', $field_group);
    $entity->save();
  }

  private function getId() {
    return $this->entity_type . '.' . $this->bundle . '.' . $this->display_mode . '.' . $this->view_mode;
  }

  public function getFieldgroupInstance($keys = array()) {
    $groups = array();
    foreach($keys as $delta => $name) {
      $id = 'field_group.' . $this->getId() . '.' . $name;
      $groups[$name] = array(
        '#attributes' => array(
          'class' => array(
            'draggable',
            'field-group',
            'new-group2',
          ),
        ),
        '#row_type' => 'field_group',
        '#region_callback' => 'field_group_field_overview_row_region',
        'label' => array(
          // TODO: should be dynamically.
          '#markup' => 'Title',
        ),
        'weight' => array(
          '#type' => 'textfield',
          '#default_value' => '2',
          '#size' => 3,
          '#attributes' => array(
            'class' => array(
              'field-weight',
            ),
          ),
          '#title_display' => 'invisible',
          '#title' => 'Weight for Title',
        ),
        'parent_wrapper' => array(
          'parent' => array(
            '#type' => 'select',
            '#title' => 'Parent for Title',
            '#title_display' => 'invisible',
            '#options' => array(),
            '#empty_value' => '',
            '#attributes' => array(
              'class' => array(
                'field-parent',
              ),
            ),
            '#parents' => array(
              'fields',
              $name,
              'parent',
            ),
          ),
          'hidden_name' => array(
            '#type' => 'hidden',
            '#default_value' => $name,
            '#attributes' => array(
              'class' => array(
                'field-name',
              ),
            ),
          ),
        ),
        'field_name' => array(
          '#markup' => 'field_group-' . $name,
        ),
        'type' => array(
          // TODO: Should be the selected Widget?
          '#markup' => 'Field Group',
        ),
        'widget_type' => array(
          // TODO: This should be dynamically.
          '#type' => 'select',
          '#title' => 'Widget for new field group',
          '#title_display' => 'invisible',
          '#default_value' => config($id)->get('widget_type'),
          '#options' => field_group_widget_options(),
          // TODO: Check how to make this translatable.
          '#empty_option' => '- Select a field group type -',
          '#description' => 'Form element to edit the data.',
          '#attributes' => array(
            'class' => array(
              'widget-type-select',
            ),
          ),
          '#cell_attributes' => array(
            'colspan' => 1,
          ),
          '#prefix' => '<div class="add-new-placeholder"> </div>',
        ),
        'operations' => array(
          '#markup' => l('delete', 'field_group/delete'),
        ),
      );
    }

    return $groups;

  }

}

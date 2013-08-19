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
   * This should be getAllFieldGroups() by
   * entity_type, bundle, display_mode, view_mode.
   */
  public function getFieldGroups() {
    return Drupal::entityQuery('field_group')
                   ->condition('entity_type', $this->entity_type)
                   ->condition('bundle', $this->bundle)
                   ->condition('display_mode', $this->display_mode)
                   ->condition('view_mode', $this->view_mode)
                   ->execute();
  }




  // TODO: CHeck if this is necessary.
  private function getMachineNames() {
    $machine_names = array();
    foreach($this->getFieldGroups() as $id) {
      $machine_name = config('field_group.' . $id)->get('machine_name');
      $machine_names[] = $machine_name;
    }
    return $machine_names;
  }


  // Get a list of all dragable fields, this should be a helper function.
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


  private function getId() {
    return $this->entity_type . '.' . $this->bundle . '.' . $this->display_mode . '.' . $this->view_mode;
  }

  /**
   * Generate fieldgroup isntances for field_ui.
   */
  public function getFieldgroupInstance($keys = array()) {
    $groups = array();

    foreach($keys as $delta => $name) {
      $id = 'field_group.' . $this->getId() . '.' . $name;
      $field_group = \Drupal::config($id)->get();

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
        // '#js_settings' => array(
        //   'rowHandler' => 'field_group',
        //   'defaultPlugin' => 'div',
        // ),
        'human_name' => array(
          // TODO: should be dynamically.
          '#markup' => $field_group['label'],
        ),
        'weight' => array(
          '#type' => 'textfield',
          // TODO: Save and reade weight attribtue.
          '#default_value' => '2',
          '#size' => 3,
          '#attributes' => array(
            'class' => array(
              'field-weight',
            ),
          ),
          '#title_display' => 'invisible',
          '#title' => 'Weight for ' + $name,
        ),
        'parent_wrapper' => array(
          'parent' => array(
            '#type' => 'select',
            '#title' => 'Parent for ' + $name,
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
        'label' => array(
          // '#type' => 'select',
          // '#title' => 'Label display for Image',
          // '#title_display' => 'invisible',
          // '#options' => array(
          //   'above' => 'Above',
          //   'inline' => 'Inline',
          //   'hidden' => '- Hidden -'
          // ),
          // '#default_value' => 'above',
          '#markup' => 'No settings available yet',
        ),
        // 'type' => array(
        //   // TODO: Should be the selected Widget?
        //   '#markup' => 'Field Group',
        // ),
        'plugin' => array(
          'type' => array(
            // TODO: This should be dynamically.
            '#type' => 'select',
            '#title' => 'Widget for new field group',
            '#title_display' => 'invisible',
            '#default_value' => config($id)->get('widget_type'),
            '#options' => field_group_widget_options(),
            // TODO: Check how to make this translatable.
            '#empty_option' => '- Select a field group type -',
            '#description' => 'Form element to edit the data.',
            '#parents' => array(
              'fields',
              $name,
              'type'
            ),
            '#attributes' => array(
              'class' => array(
                ' field-plugin-type',
              ),
            ),
          ),
          // 'settings_edit_form' => array(),
          'settings_edit_form' => array(),
          '#title' => 'Widget for Fieldgroup',
          // '#cell_attributes' => array(
          //   'colspan' => 1,
          // ),
          // '#prefix' => '<div class="add-new-placeholder"> </div>',
        ),
        'settings_summary' => array(
          '#prefix' => '<div class="field-plugin-summary">',
          '#markup' => 'We need some generic method to generate this.',
          '#sufix' => '</div>',
          '#cell_attributes' => array(
            'class' => array(
              'field-plugin-summary-cell',
            ),
          ),
        ),
        // 'operations' => array(
        //   '#markup' => l('delete', 'field_group/delete'),
        // ),
      );
    }

    return $groups;

  }

}

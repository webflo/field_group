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

  protected $entity_type;
  protected $bundle;
  protected $display_mode;
  protected $view_mode;

  public function __construct($entity_type, $bundle, $display_mode, $view_mode) {
    $this->entity_type = $entity_type;
    $this->bundle = $bundle;
    $this->display_mode = $display_mode;
    $this->view_mode = $view_mode;
  }

  private function getStorageController() {
    return \Drupal::entityManager()->getStorageController('field_group');
  }

  /**
   * This one needs a rewrite...
   *
   *
   */
  public function submitForm(&$form, &$form_state) {
    $values = $form_state['input']['fields'];


    // First clear all fields in every group to catch empty groups.
    foreach($this->getFieldGroups() as $id => $field_group) {
      $values[$field_group->field_group_name]['fields'] = array();
    }
    foreach ($this->getDraggableFields($form) as $delta => $field_name) {
      // dsm($values[$field_name]);
      if(!empty($values[$field_name]['parent'])) {
        $parent = $values[$field_name]['parent'];
        $values[$parent]['fields'][] = $field_name;
      }
    }

    // If _add_new_field_group is used.
    if(!empty($values['_add_new_field_group']['field_group_name'])) {
      $this->createFieldGroup($values['_add_new_field_group']);
    }
    foreach($form['#field_groups'] as $field_group_name => $field_group) {
      $values[$field_group_name]['field_group_name'] = $field_group_name;
      // dsm($field_group);
      $this->updateFieldGroup($values[$field_group_name]);
    }


  }

  public function submitSettingsForm($field_group, $values) {
    // TODO: Save settings to entity.
    $values['plugin_settings'] = $values;
    foreach ($values as $key => $value) {
      $field_group->set($key, $value);
    }
    dsm($field_group);
    dsm($field_group->save());
  }

  /**
   * Needs at least ID parameter.
   */
  public function createFieldGroup($values) {

    $id = $this->entity_type . '.' . $this->bundle . '.' . $this->display_mode . '.' . $this->view_mode . '.' . $values['field_group_name'];
    $values['id'] = (isset($values['id']) && !empty($values['id'])) ? $values['id'] : $id;
    $values['entity_type'] = $this->entity_type;
    $values['bundle'] = $this->bundle;
    $values['display_mode'] = $this->display_mode;
    $values['view_mode'] = $this->view_mode;
    $values['field_group_name'] = 'field_group_' . $values['field_group_name'];

    // $storageController = \Drupal::entityManager()->getStorageController('field_group');
    $entity = $this->getStorageController()->create($values);
    return $entity->save();
  }

  private function updateFieldGroup($values) {
    // $storageController = \Drupal::entityManager()->getStorageController('field_group');
    $entity = $this->getStorageController()->loadByProperties(
      array(
        'field_group_name' => $values['field_group_name'],
        'entity_type' => $this->entity_type,
        'bundle' => $this->bundle,
        'display_mode' => $this->display_mode,
        'view_mode' => $this->view_mode
      )
    );
    $entity = reset($entity);
    foreach ($values as $key => $value) {
      $entity->set($key, $value);
    }
    $entity->save();
  }

  private function deleteFieldGroup($fieldGroup) {
    $this->deleteFieldGroupsMultiple(array($fieldGroup));
  }
  private function deleteFieldGroupsMultiple($fieldGroups) {
    // $storageController = \Drupal::entityManager()->getStorageController('field_group');
    $this->getStorageController()->delete($fieldGroups);
  }


  /**
   * Fetch fieldGroup id's by given properies.
   */
  public function getFieldGroups() {
    // $storage_controller = \Drupal::entityManager()->getStorageController('field_group');
    $field_groups = $this->getStorageController()->loadByProperties(
      array(
        'entity_type' => $this->entity_type,
        'bundle' => $this->bundle,
        'display_mode' => $this->display_mode,
        'view_mode' => $this->view_mode
      )
    );
    return $field_groups;
  }

  public function getDraggableFields($form) {
    $field_groups = $this->getFieldGroups();
    $fieldGroupKeys = array();
    foreach($field_groups as $field_group) {
      $fieldGroupKeys[$field_group->field_group_name] = $field_group->field_group_name;
    }
    return array_merge($form['#fields'], $form['#extra'], array(
        '_add_new_field',
        '_add_existing_field',
        '_add_new_field_group',
      ),
      $fieldGroupKeys
    );
  }


  /**
   * Generate fieldgroup isntances for field_ui.
   */
  public function getFieldgroupInstance($form, $form_state, $field_group) {
    return array(
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
          '#markup' => $field_group->label,
        ),
        'weight' => array(
          '#type' => 'textfield',
          '#default_value' => $field_group->weight,
          '#size' => 3,
          '#attributes' => array(
            'class' => array(
              'field-weight',
            ),
          ),
          '#title_display' => 'invisible',
          '#title' => 'Weight for ' + $field_group->label,
        ),
        'parent_wrapper' => array(
          'parent' => array(
            '#type' => 'select',
            '#title' => 'Parent for ' + $field_group->label,
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
              $field_group->field_group_name,
              'parent',
            ),
          ),
          'hidden_name' => array(
            '#type' => 'hidden',
            '#default_value' => $field_group->field_group_name,
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
        'plugin' => array(
          'type' => array(
            // TODO: This should be dynamically.
            '#type' => 'select',
            '#title' => 'Widget for new field group',
            '#title_display' => 'invisible',
            '#default_value' => $field_group->type,
            '#options' => $this->field_group_widget_options(),
            // TODO: Check how to make this translatable.
            '#empty_option' => '- Select a field group type -',
            '#description' => 'Form element to edit the data.',
            '#parents' => array(
              'fields',
              $field_group->field_group_name,
              'type'
            ),
            '#attributes' => array(
              'class' => array(
                ' field-plugin-type',
              ),
            ),
          ),
          'settings_edit_form' => array(),
          '#title' => 'Widget for Fieldgroup',
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
        // TODO: Add delete link somewhere.
        // 'operations' => array(
        //   '#markup' => l('delete', 'field_group/delete'),
        // ),
        'settings_edit' => array(
          '#submit' => array(array($form_state['build_info']['callback_object'], 'multistepSubmit')),
          '#ajax' => array(
            'callback' => array($form_state['build_info']['callback_object'], 'multistepAjax'),
            // 'callback' => array($field_group_field_ui, 'multistepAjax'),
            'wrapper' => 'field-display-overview-wrapper',
            'effect' => 'fade',
          ),
          '#field_name' => $field_group->field_group_name,
          '#type' => 'image_button',
          '#name' => $field_group->field_group_name . '_settings_edit',
          '#src' => 'core/misc/configure-dark.png',
          '#attributes' => array(
            'class' => array(
              0 => 'field-plugin-settings-edit',
            ),
            'alt' => 'Edit',
          ),
          '#op' => 'edit',
          '#limit_validation_errors' => array(
            0 => array(
              0 => 'fields',
              1 => $field_group->field_group_name,
              2 => 'type'
            )
          ),
          '#prefix' => '<div class="field-plugin-settings-edit-wrapper">',
          '#suffix' => '</div>'
        ),

      );

  }

  public function getRowRegion($row) {
    switch ($row['#row_type']) {
      case 'add_new_field':
        return 'hidden';
    }
  }

  public function field_group_add_group() {
    $name = '_add_new_field_group';

    return array(
      '#attributes' => array(
        'class' => array(
          'draggable',
          // 'tabledrag-leaf',
          'add-new',
        ),
      ),
      '#row_type' => 'add_new_field',
      '#region_callback' => array($this, 'getRowRegion'),
      'label' => array(
        '#type' => 'textfield',
        '#title' => 'New field label',
        '#title_display' => 'invisible',
        '#size' => 15,
        '#description' => 'Label',
        '#prefix' => '<div class="label-input"><div class="add-new-placeholder">Add new field group</div>',
        '#suffix' => '</div>',
      ),
      'weight' => array(
        '#type' => 'textfield',
        '#default_value' => 4,
        '#size' => 3,
        '#title_display' => 'invisible',
        '#title' => 'Weight for new field',
        '#attributes' => array(
          'class' => array(
            'field-weight',
          ),
        ),
        '#prefix' => '<div class="add-new-placeholder"> </div>',
      ),
      'parent_wrapper' => array(
        'parent' => array(
          '#type' => 'select',
          '#title' => t('Parent for default field'),
          '#title_display' => 'invisible',
          '#options' => array(),
          '#empty_value' => '',
          '#attributes' => array(
            'class' => array(
              'field-parent'
            ),
          ),
          '#prefix' => '<div class="add-new-placeholder">&nbsp;</div>',
          '#parents' => array(
            'fields',
            $name,
            'parent'
          ),
        ),
        'hidden_name' => array(
          '#type' => 'hidden',
          '#default_value' => $name,
          '#attributes' => array(
            'class' => array(
              'field-name'
            ),
          ),
        ),
      ),
      'field_group_name' => array(
        '#type' => 'machine_name',
        '#title' => 'New field name',
        '#title_display' => 'invisible',
        '#field_prefix' => '<span dir="ltr">field_group_',
        '#field_suffix' => '</span>‎',
        '#size' => 15,
        '#description' => 'A unique machine-readable name containing letters, numbers, and underscores.',
        '#maxlength' => 26,
        '#prefix' => '<div class="add-new-placeholder"> </div>',
        '#machine_name' => array(
          'source' => array(
            'fields',
            '_add_new_field_group',
            'label',
          ),
          'exists' => '_field_group_field_name_exists',
          'standalone' => TRUE,
          'label' => '',
        ),
        '#required' => FALSE,
      ),
      'type' => array(),
      'type' => array(
        '#type' => 'select',
        '#title' => 'Widget for new field group',
        '#title_display' => 'invisible',
        '#options' => $this->field_group_widget_options(),
        // TODO: Check how to make this translatable.
        '#empty_option' => '- Select a field group type -',
        '#description' => 'Form element to edit the data.',
        '#attributes' => array(
          'class' => array(
            'widget-type-select',
          ),
        ),
        '#cell_attributes' => array(
          'colspan' => 3,
        ),
        '#prefix' => '<div class="add-new-placeholder"> </div>',
      ),
      'translatable' => array(
        '#type' => 'value',
        '#value' => FALSE,
      ),
    );
  }

  private function field_group_widget_options() {
    $widget_options = array();
    $widgets = \Drupal::service('plugin.manager.field_group')->getDefinitions();
    // dsm($widgets);
    // dsm(\Drupal::service('plugin.manager.field_group')->getDefinitions());
    foreach($widgets as $widget_name => $widget) {
      $field_type = key(array_flip($widget['field_types']));
      if($field_type == 'field_group') {
        $widget_options[$widget_name] = $widget_name;
      }
    }
    return $widget_options;
  }

}

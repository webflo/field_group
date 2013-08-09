<?php
/**
 * @file
 * Contains \Drupal\field_group\FieldGroupAddGroup.
 */

namespace Drupal\field_group;

/**
 * Provides an interface defining a field_group data object.
 */
class FieldGroupAddGroup {

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
		  '#region_callback' => 'field_ui_field_overview_row_region',
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
		  'field_name' => array(
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
		  'widget_type' => array(
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

	  $widgets = drupal_container()->get('plugin.manager.field_group')->getDefinitions();
	  foreach($widgets as $widget_name => $widget) {
	    $field_type = key(array_flip($widget['field_types']));
	    if($field_type == 'field_group') {
	      $widget_options[$widget_name] = $widget_name;
	    }
	  }
	  return $widget_options;
	}

}

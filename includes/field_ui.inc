<?php

/**
 * @file
 * Field_group.field_ui.inc is a file that contains most functions
 * needed on the Fields UI Manage forms (display and fields).
 */

use Drupal\Core\Url;

/**
 * Helper function to get the form parameters to use while
 * building the fields and display overview form.
 */
function field_group_field_ui_form_params($form, $context) {

  $entity_type_definition = \Drupal::entityManager()->getDefinition($form['#entity_type']);
  $admin_url = new Url($entity_type_definition->getLinkTemplate('admin-form'), array(
    $entity_type_definition->getBundleEntityType() => $form['#bundle'],
  ));

  $params = new stdClass();
  $params->entity_type = $form['#entity_type'];
  $params->bundle = $form['#bundle'];
  $params->admin_path = $admin_url->toString();
  $params->region_callback = 'field_group_display_overview_row_region';
  $params->mode = $form['#mode'];
  $params->context = $context;

  $params->groups = array();
  $params->groups = field_group_info_groups($params->entity_type, $params->bundle, $params->context, $params->mode, TRUE);

  // Gather parenting data.
  $params->parents = array();
  foreach ($params->groups as $name => $group) {
    foreach ($group->children as $child) {
      $params->parents[$child] = $name;
    }
  }

  return $params;
}

/**
 * Function to alter the display overview screens.
 */
function field_group_field_ui_display_form_alter(&$form, &$form_state, $context) {

  // Only start altering the form if we need to.
  if (empty($form['#fields']) && empty($form['#extra'])) {
    return;
  }

  $params = field_group_field_ui_form_params($form, $context);
  $form['#groups'] = array_keys($params->groups);
  $form['#context'] = $context;

  $table = &$form['fields'];

  // Add a region for 'add_new' rows, but only when fields are
  // available and thus regions.
  if (isset($table['#regions'])) {
    $table['#regions'] += array(
      'add_new' => array(
        'rows_order' => array('_add_new_group'),
      ),
    );
  }

  // Extend available parenting options.
  foreach ($params->groups as $name => $group) {
    $table['#parent_options'][$name] = $group->label;
  }
  $table['#parent_options']['_add_new_group'] = t('Add new group');

  // Update existing rows accordingly to the parents.
  foreach (element_children($table) as $name) {
    $table[$name]['parent_wrapper']['parent']['#options'] = $table['#parent_options'];
    // Inherit the value of the parent when default value is empty.
    if (empty($table[$name]['parent_wrapper']['parent']['#default_value'])) {
      $table[$name]['parent_wrapper']['parent']['#default_value'] = isset($params->parents[$name]) ? $params->parents[$name] : '';
    }
  }

  $formatter_options = field_group_field_formatter_options($context);

  $refresh_rows = isset($form_state['values']['refresh_rows']) ? $form_state['values']['refresh_rows'] : (isset($form_state['input']['refresh_rows']) ? $form_state['input']['refresh_rows'] : NULL);

  // Create the group rows and check actions.
  foreach ($form['#groups'] as $name) {

    // Play around with form_state so we only need to hold things
    // between requests, until the save button was hit.
    if (isset($form_state['field_group'][$name])) {
      $group = & $form_state['field_group'][$name];
    }
    else {
      $group = & $params->groups[$name];
    }

    // Check the currently selected formatter, and merge persisted values for
    // formatter settings for the group.
    // This needs to be done first, so all fields are updated before creating form elements.
    if (isset($refresh_rows) && $refresh_rows == $name) {
      $settings = isset($form_state['values']['fields'][$name]) ? $form_state['values']['fields'][$name] : (isset($form_state['input']['fields'][$name]) ? $form_state['input']['fields'][$name] : NULL);
      if (array_key_exists('settings_edit', $settings)) {
        //$group->format_type = $form_state['field_group'][$name]->format_type;
        $group = $form_state['field_group'][$name];
      }
      field_group_formatter_row_update($group, $settings);
    }

    // Save the group when the configuration is submitted.
    if (!empty($form_state['values'][$name . '_plugin_settings_update'])) {
      field_group_formatter_settings_update($group, $form_state['values']['fields'][$name]);
    }
    // After all updates are finished, let the form_state know.
    $form_state['field_group'][$name] = $group;

    $settings = field_group_format_settings_form($group);

    $id = strtr($name, '_', '-');
    $js_rows_data[$id] = array('type' => 'group', 'name' => $name);
    // A group cannot be selected as its own parent.
    $parent_options = $table['#parent_options'];
    unset($parent_options[$name]);
    $table[$name] = array(
      '#attributes' => array('class' => array('draggable', 'field-group'), 'id' => $id),
      '#row_type' => 'group',
      '#region_callback' => $params->region_callback,
      '#js_settings' => array('rowHandler' => 'group'),
      'human_name' => array(
        '#markup' => check_plain(t($group->label)),
        '#prefix' => '<span class="group-label">',
        '#suffix' => '</span>',
      ),
      'weight' => array(
        '#type' => 'textfield',
        '#default_value' => $group->weight,
        '#size' => 3,
        '#attributes' => array('class' => array('field-weight')),
      ),
      'parent_wrapper' => array(
        'parent' => array(
          '#type' => 'select',
          '#options' =>  $parent_options,
          '#empty_value' => '',
          '#default_value' => isset($params->parents[$name]) ? $params->parents[$name] : '',
          '#attributes' => array('class' => array('field-parent')),
          '#parents' => array('fields', $name, 'parent'),
        ),
        'hidden_name' => array(
          '#type' => 'hidden',
          '#default_value' => $name,
          '#attributes' => array('class' => array('field-name')),
        ),
      ),
    );

    $table[$name] += array(
      'format' => array(
        'type' => array(
          '#type' => 'select',
          '#options' => $formatter_options,
          '#default_value' => $group->format_type,
          '#attributes' => array('class' => array('field-group-type')),
        ),
      ),
    );

    $base_button = array(
      '#submit' => array(
        array($form_state['build_info']['callback_object'], 'multistepSubmit')
      ),
      '#ajax' => array(
        'callback' => array($form_state['build_info']['callback_object'], 'multistepAjax'),
        'wrapper' => 'field-display-overview-wrapper',
        'effect' => 'fade',
      ),
      '#field_name' => $name,
    );

    if ($form_state['plugin_settings_edit'] == $name) {
      $table[$name]['format']['#cell_attributes'] = array('colspan' => 3);
      $table[$name]['format']['format_settings'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('field-plugin-settings-edit-form')),
        '#parents' => array('fields', $name, 'settings_edit_form'),
        '#weight' => -5,
        'label' => array(
          '#markup' => t('Field group format:') . ' <span class="formatter-name">' . $group->format_type . '</span>',
        ),
        // Create a settings form where hooks can pick in.
        'settings' => $settings,
        'actions' => array(
          '#type' => 'actions',
          'save_settings' => $base_button + array(
            '#type' => 'submit',
            '#name' => $name . '_plugin_settings_update',
            '#value' => t('Update'),
            '#op' => 'update',
          ),
          'cancel_settings' => $base_button + array(
            '#type' => 'submit',
            '#name' => $name . '_plugin_settings_cancel',
            '#value' => t('Cancel'),
            '#op' => 'cancel',
            // Do not check errors for the 'Cancel' button.
            '#limit_validation_errors' => array(),
          ),
        ),
      );
      $table[$name]['#attributes']['class'][] = 'field-formatter-settings-editing';
      $table[$name]['format']['type']['#attributes']['class'] = array('element-invisible');
    }
    else {
      // After saving, the settings are updated here aswell. First we create
      // the element for the table cell.
      $table[$name]['settings_summary'] = array('#markup' => '');
      if (!empty($group->format_settings)) {
        $table[$name]['settings_summary'] = field_group_format_settings_summary($name, $group);
      }
      // Add the configure button.
      $table[$name]['settings_edit'] = $base_button + array(
        '#type' => 'image_button',
        '#name' => $name . '_group_settings_edit',
        '#src' => 'core/misc/configure-dark.png',
        '#attributes' => array('class' => array('field-plugin-settings-edit'), 'alt' => t('Edit')),
        '#op' => 'edit',
            // Do not check errors for the 'Edit' button, but make sure we get
            // the value of the 'plugin type' select.
        '#limit_validation_errors' => array(array('fields', $name, 'type')),
        '#prefix' => '<div class="field-plugin-settings-edit-wrapper">',
        '#suffix' => '</div>',
      );
      $table[$name]['settings_edit']['#suffix'] .= l(t('delete'), $params->admin_path . '/groups/' . $name . '/delete/' . $params->mode);
    }

  }

  // Additional row: add new group.
  $parent_options = $table['#parent_options'];
  unset($parent_options['_add_new_group']);
  $table['_add_new_group'] = field_group_add_row('_add_new_group', $parent_options, $params);

  $table['_add_new_group'] += array(
    'format' => array(
      'type' => array(
        '#type' => 'select',
        '#options' => $formatter_options,
        '#default_value' => 'fieldset',
        '#prefix' => '<div class="add-new-placeholder">&nbsp;</div>',
      ),
      '#cell_attributes' => array('colspan' => 3),
    ),
  );

  $form['#attached']['css'][] = drupal_get_path('module', 'field_group') . '/css/field_group.field_ui.css';
  $form['#attached']['js'][] = drupal_get_path('module', 'field_group') . '/js/field_group.field_ui.js';

  $form['#validate'][] = 'field_group_field_overview_validate';
  $form['#submit'][] = 'field_group_field_overview_submit';

  // Create the settings for fieldgroup as vertical tabs (merged with DS).
  field_group_field_ui_create_vertical_tabs($form, $form_state, $params);

  // Show a warning if the user has not set up required containers
  if ($form['#groups']) {

    $parent_requirements = array(
      'multipage' => array(
        'parent' => 'multipage-group',
        'message' => 'Each Multipage element needs to have a parent Multipage group element.',
      ),
      'htab' => array(
        'parent' => 'htabs',
        'message' => 'Each Horizontal tab element needs to have a parent Horizontal tabs group element.',
      ),
      'accordion-item' => array(
        'parent' => 'accordion',
        'message' => 'Each Accordion item element needs to have a parent Accordion group element.',
      ),
    );

    // On display overview tabs need to be checked.
    if ($context == 'view') {
      $parent_requirements['tab'] = array(
        'parent' => 'tabs',
        'message' => 'Each Vertical tab element needs to have a parent Vertical tabs group element.',
      );
    }

    foreach ($form['#groups'] as $group_name) {
      $group_check = field_group_load_field_group($group_name, $params->entity_type, $params->bundle, $params->context, $params->mode);
      if (isset($parent_requirements[$group_check->format_type])) {
        if (!$group_check->parent_name || field_group_load_field_group($group_check->parent_name, $params->entity_type, $params->bundle, $params->context, $params->mode)->format_type != $parent_requirements[$group_check->format_type]['parent']) {
          drupal_set_message(t($parent_requirements[$group_check->format_type]['message']), 'warning', FALSE);
        }
      }
    }
  }
}

/**
 * Return an array of field_group_formatter options.
 */
function field_group_field_formatter_options($type) {
  $options = &drupal_static(__FUNCTION__);

  if (!isset($options)) {
    $options = array();
    $field_group_types = field_group_formatter_info();
    if (!empty($field_group_types[$type])) {
      foreach ($field_group_types[$type] as $name => $field_group_type) {
        $options[$name] = $field_group_type['label'];
      }
    }
  }
  return $options;
}

/**
 * Helper function to add a row in the overview forms.
 */
function field_group_add_row($name, $parent_options, $params) {

  $display = entity_get_display($params->entity_type, $params->bundle, $params->mode);

  return array(
    '#attributes' => array('class' => array('draggable', 'field-group', 'add-new')),
    '#row_type' => 'add_new_group',
    '#js_settings' => array('rowHandler' => 'group'),
    '#region_callback' => $params->region_callback,
    'label' => array(
      '#title_display' => 'invisible',
      '#title' => t('Label for new group'),
      '#type' => 'textfield',
      '#size' => 15,
      '#description' => t('Label'),
      '#prefix' => '<div class="label-input"><div class="add-new-placeholder">' . t('Add new group') . '</div>',
      '#suffix' => '</div>',
    ),
    'weight' => array(
      '#type' => 'textfield',
      '#default_value' => $display->getHighestWeight() + 3,
      '#size' => 3,
      '#title_display' => 'invisible',
      '#title' => t('Weight for new group'),
      '#attributes' => array('class' => array('field-weight')),
      '#prefix' => '<div class="add-new-placeholder">&nbsp;</div>',
    ),
    'parent_wrapper' => array(
      'parent' => array(
        '#title_display' => 'invisible',
        '#title' => t('Parent for new group'),
        '#type' => 'select',
        '#options' => $parent_options,
        '#empty_value' => '',
        '#attributes' => array('class' => array('field-parent')),
        '#prefix' => '<div class="add-new-placeholder">&nbsp;</div>',
        '#parents' => array('fields', $name, 'parent'),
      ),
      'hidden_name' => array(
        '#type' => 'hidden',
        '#default_value' => $name,
        '#attributes' => array('class' => array('field-name')),
      ),
    ),
    'group_name' => array(
      '#type' => 'textfield',
      '#title_display' => 'invisible',
      '#title' => t('Machine name for new group'),
      // This field should stay LTR even for RTL languages.
      '#field_prefix' => '<span dir="ltr">group_',
      '#field_suffix' => '</span>&lrm;',
      '#attributes' => array('dir' => 'ltr'),
      '#size' => 15,
      '#description' => t('Group name (a-z, 0-9, _)'),
      '#prefix' => '<div class="add-new-placeholder">&nbsp;</div>',
      '#cell_attributes' => array('colspan' => 1),
    ),
  );
}

/**
 * Create vertical tabs.
 */
function field_group_field_ui_create_vertical_tabs(&$form, &$form_state, $params) {

  $form_state['field_group_params'] = $params;

  $existing_group_config = \Drupal::configFactory()->listAll('field_group.' . $params->entity_type . '.' . $params->bundle);
  $displays = array();
  foreach ($existing_group_config as $config) {
    $group = \Drupal::config($config)->get();
    if ($group['context'] == $params->context && $group['mode'] == $params->mode) {
      continue;
    }
    $displays[$group['context'] . '.' . $group['mode']] = $group['context'] . ':' . $group['mode'];
  }

  // No displays to clone.
  if (empty($displays)) {
    return;
  }

  // Add additional settings vertical tab.
  if (!isset($form['additional_settings'])) {
    $form['additional_settings'] = array(
      '#type' => 'vertical_tabs',
      '#theme_wrappers' => array('vertical_tabs'),
      '#prefix' => '<div>',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    );
  }

  // Add extra guidelines for webmaster.
  $form['field_group'] = array(
    '#type' => 'details',
    '#group' => 'additional_settings',
    '#title' => t('Fieldgroups'),
    '#description' => t('<p class="fieldgroup-help">Fields can be dragged into groups with unlimited nesting. Each fieldgroup format comes with a configuration form, specific for that format type.<br />Note that some formats come in pair. These types have a html wrapper to nest its fieldgroup children. E.g. Place accordion items into the accordion, vertical tabs in vertical tab group and horizontal tabs in the horizontal tab group. There is one exception to this rule, you can use a vertical tab without a wrapper when the additional settings tabs are available. E.g. node forms.</p>'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['field_group']['fieldgroup_clone'] = array(
    '#title' => t('Select source display'),
    '#description' => t('Clone fieldgroups from selected display to the current display'),
    '#type' => 'select',
    '#options' => $view_modes,
    '#default_value' => 'none'
  );
  $form['field_group']['fieldgroup_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Clone'),
    '#validate' => array('field_group_field_ui_clone_field_groups_validate'),
    '#submit' => array('field_group_field_ui_clone_field_groups')
  );

}

/**
 * Returns the region to which a row in the 'Manage display' screen belongs.
 * @param Array $row A field or field_group row
 * @return String the current region.
 */
function field_group_display_overview_row_region($row) {
  switch ($row['#row_type']) {
    case 'group':
      return ($row['format']['type']['#value'] == 'hidden' ? 'hidden' : 'content');
    case 'add_new_group':
      // If no input in 'label', assume the row has not been dragged out of the
      // 'add new' section.
      if (empty($row['label']['#value'])) {
        return 'add_new';
      }
      return ($row['format']['type']['#value'] == 'hidden' ? 'hidden' : 'content');
  }
}

/**
 * Validate handler for the overview screens.
 * @param Array $form The complete form.
 * @param Array $form_state The state of the form.
 */
function field_group_field_overview_validate($form, &$form_state) {
  $form_values = $form_state['values']['fields'];
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $context = $form['#context'];
  $mode = $form['#mode'];

  $group = $form_values['_add_new_group'];

  // Validate if any information was provided in the 'add new group' row.
  if (array_filter(array($group['label'], $group['group_name']))) {

    // Missing group name.
    if (!$group['group_name']) {
      form_set_error('fields][_add_new_group][group_name', t('Add new group: you need to provide a group name.'));
    }
    // Group name validation.
    else {
      $group_name = $group['group_name'];

      // Add the 'group_' prefix.
      if (drupal_substr($group_name, 0, 6) != 'group_') {
        $group_name = 'group_' . $group_name;
        form_set_value($form['fields']['_add_new_group']['group_name'], $group_name, $form_state);
      }

      // Invalid group name.
      if (!preg_match('!^group_[a-z0-9_]+$!', $group_name)) {
        \Drupal::formBuilder()->setErrorByName('fields][_add_new_group][group_name', $form_state, t('Add new group: the group name %group_name is invalid. The name must include only lowercase unaccentuated letters, numbers, and underscores.', array('%group_name' => $group_name)));
      }
      if (drupal_strlen($group_name) > 32) {
        \Drupal::formBuilder()->setErrorByName('fields][_add_new_group][group_name', $form_state, t("Add new group: the group name %group_name is too long. The name is limited to 32 characters, including the 'group_' prefix.", array('%group_name' => $group_name)));
      }

      // Group name already exists.
      if (field_group_exists($group_name, $entity_type, $bundle, $context, $mode)) {
        \Drupal::formBuilder()->setErrorByName('fields][_add_new_group][group_name', $form_state, t('Add new group: the group name %group_name already exists.', array('%group_name' => $group_name)));
      }
    }
  }
}

/**
 * Submit handler for the overview screens.
 * @param Array $form The complete form.
 * @param Array $form_state The state of the form.
 */
function field_group_field_overview_submit($form, &$form_state) {

  $form_values = $form_state['values']['fields'];
  $entity_type = $form['#entity_type'];
  $bundle = $form['#bundle'];
  $mode = $form['#mode'];
  $context = $form['#context'];

  // Collect children.
  $children = array_fill_keys($form['#groups'], array());
  foreach ($form_values as $name => $value) {
    if (!empty($value['parent'])) {
      // Substitute newly added fields, in case they were dragged
      // directly in a group.
      if ($name == '_add_new_field' && isset($form_state['fields_added']['_add_new_field'])) {
        $name = $form_state['fields_added']['_add_new_field'];
      }
      elseif ($name == '_add_existing_field' && isset($form_state['fields_added']['_add_existing_field'])) {
        $name = $form_state['fields_added']['_add_existing_field'];
      }
      $children[$value['parent']][$name] = $name;
    }
  }

  // Create new group.
  if (!empty($form_values['_add_new_group']['group_name'])) {
    $values = $form_values['_add_new_group'];

    $field_group_types = field_group_formatter_info();
    $formatter = $field_group_types[($mode == 'form' ? 'form' : 'display')][$values['format']['type']];

    $new_group = (object) array(
      'identifier' => $values['group_name'] . '|' . $entity_type . '|' . $bundle . '|' . $mode,
      'group_name' => $values['group_name'],
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'mode' => $mode,
      'context' => $context,
      'children' => isset($children['_add_new_group']) ? array_keys($children['_add_new_group']) : array(),
      'parent_name' => $values['parent'],
      'weight' => $values['weight'],
      'label' => $values['label'],
      'format_type' => $values['format']['type'],
      'disabled' => FALSE,
    );
    $new_group->format_settings = array('formatter' => isset($formatter['default_formatter']) ? $formatter['default_formatter'] : '');
    if (isset($formatter['instance_settings'])) {
      $new_group->format_settings['instance_settings'] = $formatter['instance_settings'];
    }

    $classes = _field_group_get_html_classes($new_group);
    $new_group->format_settings['instance_settings']['classes'] = implode(' ', $classes->optional);

    field_group_group_save($new_group);

    // Store new group information for any additional submit handlers.
    $form_state['groups_added']['_add_new_group'] = $new_group->group_name;
    drupal_set_message(t('New group %label successfully created.', array('%label' => $new_group->label)));

    // Replace the newly created group in the $children array, in case it was
    // dragged directly in an existing field.
    foreach (array_keys($children) as $parent) {
      if (isset($children[$parent]['_add_new_group'])) {
        unset($children[$parent]['_add_new_group']);
        $children[$parent][$new_group->group_name] = $new_group->group_name;
      }
    }
  }

  // Update existing groups.
  $groups = field_group_info_groups($entity_type, $bundle, $context, $mode, TRUE);
  foreach ($form['#groups'] as $group_name) {
    $group = $groups[$group_name];
    $group->label = $form_state['field_group'][$group_name]->label;
    $group->children = array_keys($children[$group_name]);
    $group->parent_name = $form_values[$group_name]['parent'];
    $group->weight = $form_values[$group_name]['weight'];

    $old_format_type = $group->format_type;
    $group->format_type = isset($form_values[$group_name]['format']['type']) ? $form_values[$group_name]['format']['type'] : 'visible';
    if (isset($form_state['field_group'][$group_name]->format_settings)) {
      $group->format_settings = $form_state['field_group'][$group_name]->format_settings;
    }

    // If the format type is changed, make sure we have all required format settings.
    if ($group->format_type != $old_format_type) {
      $mode = $group->mode == 'form' ? 'form' : 'display';
      $default_formatter_settings = _field_group_get_default_formatter_settings($group->format_type, $mode);
      $group->format_settings += $default_formatter_settings;
      $group->format_settings['instance_settings'] += $default_formatter_settings['instance_settings'];
    }

    field_group_group_save($group);
  }

  \Drupal::cache()->invalidate('field_groups');
}

/**
 * Creates a form for field_group formatters.
 * @param Object $group The FieldGroup object.
 */
function field_group_format_settings_form(&$group) {
  $form = array();
  $form['label'] = array(
    '#type' => 'textfield',
    '#title' => t('Field group label'),
    '#default_value' => $group->label,
    '#weight' => -5,
    '#element_validate' => array('field_group_format_settings_label_validate'),
  );
  $addition = module_invoke_all('field_group_format_settings', $group);
  $form += $addition;
  // Give fieldgroup implementations the chance to alter the description.
  if (!empty($addition['label']['#description'])) {
    $form['label']['#description'] = $addition['label']['#description'];
  }
  $form['#validate'] = array('field_group_format_settings_form_validate');
  return $form;
}

/**
 * Validate the label. Label is required for fieldsets that are collapsible.
 */
function field_group_format_settings_label_validate($element, &$form_state) {

  $group = $form_state['values']['fields'][$element['#parents'][1]];
  $settings = $group['settings_edit_form']['settings'];
  if ($group['format']['type'] == 'fieldset' && ($settings['formatter'] == 'collapsed' || $settings['formatter'] == 'collapsible') && empty($settings['label'])) {
    form_error($element, t('The label is required when formatter is collapsible or collapsed'));
  }

}

/**
 * Validate the entered css class from the submitted format settings.
 * @param Array $element The validated element
 * @param Array $form_state The state of the form.
 */
function field_group_validate_css_class($element, &$form_state) {
  if (!empty($form_state['values']['fields'][$form_state['plugin_settings_edit']]['settings_edit_form']['settings']['instance_settings']['classes']) && !preg_match('!^[A-Za-z0-9-_ ]+$!', $form_state['values']['fields'][$form_state['plugin_settings_edit']]['settings_edit_form']['settings']['instance_settings']['classes'])) {
    form_error($element, t('The css class must include only letters, numbers, underscores and dashes.'));
  }
}

/**
 * Validate the entered id attribute from the submitted format settings.
 * @param Array $element The validated element
 * @param Array $form_state The state of the form.
 */
function field_group_validate_id($element, &$form_state) {
  if (!empty($form_state['values']['fields'][$form_state['plugin_settings_edit']]['settings_edit_form']['settings']['instance_settings']['id']) && !preg_match('!^[A-Za-z0-9-_]+$!', $form_state['values']['fields'][$form_state['plugin_settings_edit']]['settings_edit_form']['settings']['instance_settings']['id'])) {
    form_error($element, t('The id must include only letters, numbers, underscores and dashes.'));
  }
}

/**
 * Update the row so that the group variables are updated.
 * The rendering of the elements needs the updated defaults.
 * @param Object $group
 * @param array $settings
 */
function field_group_formatter_row_update(& $group, $settings) {
  // if the row has changed formatter type, update the group object
  if (!empty($settings['format']['type']) && $settings['format']['type'] != $group->format_type) {
    $group->format_type = $settings['format']['type'];
    field_group_formatter_settings_update($group, $settings);
  }
}

/**
 * Update handler for field_group configuration settings.
 * @param Object $group The group object
 * @param Array $settings Configuration settings
 */
function field_group_formatter_settings_update(& $group, $settings) {

  // for format changes we load the defaults.
  if (empty($settings['settings_edit_form']['settings'])) {
    $group->format_settings = _field_group_get_default_formatter_settings($group->format_type, $group->context);
  }
  else {
    $group->format_type = $settings['format']['type'];
    $group->label = $settings['settings_edit_form']['settings']['label'];
    $group->format_settings = $settings['settings_edit_form']['settings'];
  }
}

/**
 * Creates a summary for the field format configuration summary.
 * @param String $group_name The name of the group
 * @param Object $group The group object
 * @return Array ready to be rendered.
 */
function field_group_format_settings_summary($group_name, $group) {
  $summary = implode('<br />', module_invoke_all('field_group_format_summary', $group));
  return array(
    '#markup' => '<div class="field-formatter-summary">' . $summary . '</div>',
    '#cell_attributes' => array('class' => array('field-formatter-summary-cell')),
  );
}
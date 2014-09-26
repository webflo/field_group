<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\VerticalTab.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'vertical_tab' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "vertical_tab",
 *   label = @Translation("Vertical tab"),
 *   description = @Translation("This fieldgroup renders the content in a fieldset, part of vertical tabs group."),
 *   format_types = {
 *     "open",
 *     "closed",
 *   },
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   },
 * )
 */
class VerticalTab extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {

    $form = parent::settingsForm();

    $form['formatter'] = array(
      '#title' => t('Default state'),
      '#type' => 'select',
      '#options' => array_combine($this->pluginDefinition['format_types'], $this->pluginDefinition['format_types']),
      '#default_value' => $this->getSetting('formatter'),
      '#weight' => -4,
    );

    if ($this->context == 'form') {
      $form['required_fields'] = array(
        '#type' => 'checkbox',
        '#title' => t('Mark group as required if it contains required fields.'),
        '#default_value' => $this->getSetting('required_fields'),
        '#weight' => 2,
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'formatter' => 'closed',
      'description' => '',
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
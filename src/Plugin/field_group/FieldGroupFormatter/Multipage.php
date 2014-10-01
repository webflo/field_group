<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\Multipage.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'multipage' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "multipage",
 *   label = @Translation("Multipage"),
 *   description = @Translation("This fieldgroup renders the content in a page."),
 *   format_types = {
 *     "start",
 *     "no-start",
 *   },
 *   supported_contexts = {
 *     "form",
 *   },
 * )
 */
class Multipage extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender($element) {
    return $element;
  }

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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $summary = parent::settingsSummary();

    if ($this->getSetting('required_fields')) {
      $summary[] = \Drupal::translation()->translate('Mark as required');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'required_fields' => 1,
      'formatter' => 'no-start',
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
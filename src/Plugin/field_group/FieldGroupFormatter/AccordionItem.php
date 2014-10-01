<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\AccordionItem.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'accordion_item' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "accordion_item",
 *   label = @Translation("Accordion Item"),
 *   description = @Translation("This fieldgroup renders the content in a div, part of accordion group."),
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
class AccordionItem extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element) {

    $active_class = $this->getSetting('collapsed') ? '' : ' field-group-accordion-active';
    $extra_classes = $this->getSetting('classes') ? ' ' . $this->getSetting('classes') : '';

    $element += array(
      '#type' => 'markup',
      '#prefix' => '<h3 class="field-group-format-toggler accordion-item' . $active_class . '">
        <a href="#">' . String::checkPlain(\Drupal::translation()->translate($this->getLabel())) . '</a></h3>
        <div class="field-group-format-wrapper' . $extra_classes . '">',
      '#suffix' => '</div>',
    );

    if ($this->getSetting('description')) {
      $element['#prefix'] .= '<div class="description">' . $this->getSetting('description') . '</div>';
    }
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
  public function settingsSummary() {

    $summary = array();

    if ($this->getSetting('required_fields')) {
      $summary[] = \Drupal::translation()->translate('Mark as required');
    }

    if ($this->getSetting('description')) {
      $summary[] = \Drupal::translation()->translate('Description : @description',
        array('@description' => $this->getSetting('description'))
      );
    }

    return $summary;
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
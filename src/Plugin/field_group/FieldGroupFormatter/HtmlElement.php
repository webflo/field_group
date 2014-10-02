<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\HtmlElement.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'html_element' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "html_element",
 *   label = @Translation("Html element"),
 *   description = @Translation("This fieldgroup renders the inner content in a HTML element with classes and attributes."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class HtmlElement extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element) {
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {

    $form = parent::settingsForm();

    $form['element'] = array(
      '#title' => t('Element'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('element'),
      '#description' => t('E.g. div, section, aside etc.'),
      '#weight' => 1,
    );

    $form['show_label'] = array(
      '#title' => t('Show label'),
      '#type' => 'select',
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->getSetting('show_label'),
      '#weight' => 2,
    );

    $form['label_element'] = array(
      '#title' => t('Label element'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('label_element'),
      '#weight' => 3,
    );

    $form['attributes'] = array(
      '#title' => t('Attributes'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('attributes'),
      '#description' => t('E.g. name="anchor"'),
      '#weight' => 4,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $summary = parent::settingsSummary();
    $summary[] = \Drupal::translation()->translate('Element: @element',
      array('@element' => $this->getSetting('element'))
    );

    if ($this->getSetting('show_label')) {
      $summary[] = \Drupal::translation()->translate('Label element: @element',
        array('@element' => $this->getSetting('label_element'))
      );
    }

    if ($this->getSetting('attributes')) {
      $summary[] = \Drupal::translation()->translate('Attributes: @attributes',
        array('@attributes' => $this->getSetting('attributes'))
      );
    }

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
      'element' => 'div',
      'show_label' => 0,
      'label_element' => 'div',
      'attributes' => '',
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
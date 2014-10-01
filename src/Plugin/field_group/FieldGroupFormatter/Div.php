<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\Div.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'div' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "div",
 *   label = @Translation("Div"),
 *   description = @Translation("This fieldgroup renders the inner content in a simple div with the title as legend."),
 *   format_types = {
 *     "open",
 *     "collapsible",
 *     "collapsed",
 *   },
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   },
 * )
 */
class Div extends FieldGroupFormatterBase {

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
      '#title' => t('Format'),
      '#type' => 'select',
      '#options' => array_combine($this->pluginDefinition['format_types'], $this->pluginDefinition['format_types']),
      '#default_value' => $this->getSetting('formatter'),
      '#weight' => -4,
    );

    $form['label']['#description'] = t('Please enter a label for collapsible elements');
    $form['show_label'] = array(
      '#title' => t('Show label'),
      '#type' => 'select',
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->getSetting('show_label'),
      '#weight' => 2,
    );
    $form['label_element'] = array(
      '#title' => t('Label element'),
      '#type' => 'select',
      '#options' => array('h2' => t('Header 2'), 'h3' => t('Header 3')),
      '#default_value' => $this->getSetting('label_element'),
      '#weight' => 2,
    );
    $form['effect'] = array(
      '#title' => t('Effect'),
      '#type' => 'select',
      '#options' => array('none' => t('None'), 'blind' => t('Blind')),
      '#default_value' => $this->getSetting('effect'),
      '#weight' => 3,
    );
    $form['speed'] = array(
      '#title' => t('Speed'),
      '#type' => 'select',
      '#options' => array('none' => t('None'), 'slow' => t('Slow'), 'fast' => t('Fast')),
      '#default_value' => $this->getSetting('speed'),
      '#weight' => 3,
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

    $summary = parent::settingsSummary();

    if ($this->getSetting('effect') != 'none') {
      $summary[] = \Drupal::translation()->translate('Effect : @effect',
        array('@effect' => $this->getSetting('effect'))
      );
      $summary[] = \Drupal::translation()->translate('Speed : @speed',
        array('@speed' => $this->getSetting('speed'))
      );
    }

    if ($this->getSetting('show_label')) {
      $summary[] = \Drupal::translation()->translate('Label element @element',
        array('@element' => $this->getSetting('label_element'))
      );
    }

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
      'formatter' => 'open',
      'description' => '',
      'show_label' => 1,
      'label_element' => 'h3',
      'effect' => 'none',
      'speed' => 'fast',
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
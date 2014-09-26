<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\MultipageGroup.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'multipage_group' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "multipage_group",
 *   label = @Translation("Multipage group"),
 *   description = @Translation("This fieldgroup renders groups on separate pages."),
 *   supported_contexts = {
 *     "form",
 *   }
 * )
 */
class MultipageGroup extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {

    $form = parent::settingsForm();

    $form['page_header'] = array(
      '#title' => t('Format page title'),
      '#type' => 'select',
      '#options' => array(0 => t('None'), 1 => t('Label only'), 2 => t('Step 1 of 10'), 3 => t('Step 1 of 10 [Label]'),),
      '#default_value' => $this->getSetting('page_header'),
      '#weight' => 1,
    );

    $form['page_counter'] = array(
      '#title' => t('Add a page counter at the bottom'),
      '#type' => 'select',
      '#options' => array(0 => t('No'), 1 => t('Format 1 / 10'), 2 => t('The count number only')),
      '#default_value' => $this->getSetting('page_counter'),
      '#weight' => 2,
    );

    $form['move_button'] = array(
      '#title' => t('Move submit button to last multipage'),
      '#type' => 'select',
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->getSetting('move_button'),
      '#weight' => 3,
    );

    $form['move_additional'] = array(
      '#title' => t('Move additional settings to last multipage (if available)'),
      '#type' => 'select',
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->getSetting('move_additional'),
      '#weight' => 4,
    );

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $header_options = array(
      0 => \Drupal::translation()->translate('None'),
      1 => \Drupal::translation()->translate('Label only'),
      2 => \Drupal::translation()->translate('Step 1 of 10'),
      3 => \Drupal::translation()->translate('Step 1 of 10 [Label]')
    );

    $summary = parent::settingsSummary();
    $summary[] = \Drupal::translation()->translate('Format page title : @format',
      array('@format' => $header_options[$this->getSetting('page_header')])
    );

    if ($this->getSetting('move_additional')) {
      $summary[] = \Drupal::translation()->translate('Move submit button to last multipage');
    }

    if ($this->getSetting('move_additional')) {
      $summary[] = \Drupal::translation()->translate('Move additional settings to last multipage');
    }

    if ($this->getSetting('page_counter')) {
      $summary[] = \Drupal::translation()->translate('Move additional settings to last multipage');
    }

    if ($this->getSetting('move_button')) {
      $summary[] = \Drupal::translation()->translate('Add a page counter at the bottom');
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
      'page_header' => 3,
      'move_additional' => 1,
      'page_counter' => 1,
      'move_button' => 0,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
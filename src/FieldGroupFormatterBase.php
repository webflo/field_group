<?php

/**
 * @file
 * Contains \Drupal\field_group\FieldGroupFormatterBase.
 */

namespace Drupal\field_group;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Base class for 'Fieldgroup formatter' plugin implementations.
 *
 * @ingroup field_group_formatter
 */
abstract class FieldGroupFormatterBase extends PluginBase implements FieldGroupFormatterInterface {

  /**
   * The formatter settings.
   *
   * @var array
   */
  protected $settings;

  /**
   * The label display setting.
   *
   * @var string
   */
  protected $label;

  /**
   * The view mode.
   *
   * @var string
   */
  protected $viewMode;

  /**
   * The context mode.
   *
   * @var string
   */
  protected $context;

  /**
   * Get the label for current formatter.
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'classes' => '',
      'id' => '',
    );
  }

}

<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\HorizontalTab.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'horizontal_tab' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "horizontal_tab",
 *   label = @Translation("Horizontal tab"),
 *   description = @Translation("This fieldgroup renders the content in a fieldset, part of horizontal tabs group."),
 *   format_types = {
 *     "open",
 *     "closed",
 *   },
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   },
 *   default_format_type = "closed",
 * )
 */
class HorizontalTab extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'description' => '',
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
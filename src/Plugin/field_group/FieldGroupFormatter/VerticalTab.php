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
 *   default_format_type = "closed",
 * )
 */
class VerticalTab extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function defaultSettings() {
    return array(
      'description' => '',
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
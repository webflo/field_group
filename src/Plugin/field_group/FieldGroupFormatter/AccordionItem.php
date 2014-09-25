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
 *   default_format_type = "closed",
 * )
 */
class AccordionItem extends FieldGroupFormatterBase {

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
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
 *   default_format_type = "no-start",
 * )
 */
class Multipage extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'required_fields' => 1,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
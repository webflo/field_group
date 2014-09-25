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
 *   default_format_type = "open",
 * )
 */
class Div extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
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
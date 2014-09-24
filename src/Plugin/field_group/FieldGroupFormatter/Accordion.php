<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\Accordion.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'accordion' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "accordion",
 *   label = @Translation("Accordion"),
 *   description = @Translation("This fieldgroup renders child groups as jQuery accordion."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class Accordion extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function defaultSettings() {
    return array(
      'effect' => 'none',
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
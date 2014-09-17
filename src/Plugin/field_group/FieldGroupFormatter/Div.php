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
 *   supported_contexts = {
 *     "form",
 *     "view"
 *   }
 * )
 */
class Div extends FieldGroupFormatterBase {

  public function settingsForm() {
    return $form;
  }

  public function render() {

  }

}
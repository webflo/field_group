<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\VerticalTabs.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'div' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "vertical_tabs",
 *   label = @Translation("Vertical tabs"),
 *   description = @Translation("This fieldgroup renders child groups in its own vertical tabs wrapper."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class VerticalTabs extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender($element) {
    return $element;
  }

}
<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\HorizontalTabs.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'horizontal_tabs' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "horizontal_tabs",
 *   label = @Translation("Horizontal tabs"),
 *   description = @Translation("This fieldgroup renders child groups in its own horizontal tabs wrapper."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class HorizontalTabs extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function preRender($element) {
    return $element;
  }

}
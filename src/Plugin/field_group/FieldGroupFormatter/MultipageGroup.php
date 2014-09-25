<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\FieldGroupFormatter\MultipageGroup.
 */

namespace Drupal\field_group\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\FieldGroupFormatterBase;

/**
 * Plugin implementation of the 'multipage_group' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "multipage_group",
 *   label = @Translation("Multipage group"),
 *   description = @Translation("This fieldgroup renders groups on separate pages."),
 *   supported_contexts = {
 *     "form",
 *   }
 * )
 */
class MultipageGroup extends FieldGroupFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'page_header' => 3,
      'move_additional' => 1,
      'page_counter' => 1,
      'move_button' => 0,
    ) + parent::defaultSettings();
  }

  public function render() {

  }

}
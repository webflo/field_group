<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\Fieldset.
 */

namespace Drupal\field_group\Plugin\field_group\Fieldset;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

use Drupal\Core\Entity\EntityInterface;

/**
 * Plugin implementation of the 'fieldset' type.
 *
 * @Plugin(
 *   id = "fieldset",
 *   module = "field_group",
 *   label = @Translation("Fieldset"),
 *   field_types = {
 *     "field_group"
 *   },
 *   default_value = FALSE
 * )
 */
class Fieldset {

  public function settingsF1orm() {

    return $form;
  }

  public function render() {

  }

}

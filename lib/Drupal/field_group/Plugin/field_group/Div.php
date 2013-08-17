<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\field_group\Div.
 */

namespace Drupal\field_group\Plugin\div;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;


// use Drupal\field_group\Plugin\Type\Widget\WidgetBase;


use Drupal\Core\Entity\EntityInterface;

/**
 * Plugin implementation of the 'div' type.
 *
 * @Plugin(
 *   id = "div",
 *   module = "field_group",
 *   label = @Translation("Div container"),
 *   field_types = {
 *     "field_group"
 *   },
 *   default_value = FALSE
 * )
 */
class Div {

  public function settingsForm() {

    return $form;
  }

  public function render() {

  }

}

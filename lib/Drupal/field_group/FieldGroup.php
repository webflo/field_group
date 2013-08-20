<?php

/**
 * @file
 * Contains \Drupal\views\FieldGroup.
 */

namespace Drupal\field_group;

use Drupal;

/**
 * TODO.
 */
class FieldGroup {

  // private $form;
  // private $form_state;
  // private $form_id;

  private $entity_type;
  private $bundle;
  private $display_mode;
  private $view_mode;

  public function __construct($entity_type, $bundle, $display_mode, $view_mode) {
    $this->entity_type = $entity_type;
    $this->bundle = $bundle;
    $this->display_mode = $display_mode;
    $this->view_mode = $view_mode;
  }



}

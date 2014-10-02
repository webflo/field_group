<?php

/**
 * @file
 * Contains \Drupal\field_group\Routing\Paramconverter.
 */

namespace Drupal\field_group\Routing;

use Drupal\Core\ParamConverter\ParamConverterInterface;

/**
 * Parameter converter for upcasting fieldgroup config ids to fieldgroup object.
 */
class FieldGroupConverter implements ParamConverterInterface {

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, \Symfony\Component\Routing\Route $route) {
    return isset($definition['type']) && $definition['type'] == 'field_group';
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    $config = \Drupal::config('field_group.'. $value)->get();
    return empty($config) ? NULL : $config;
  }


}

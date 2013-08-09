<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\FieldGroupPluginManager.
 */

namespace Drupal\field_group\Plugin;

use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Core\Plugin\Discovery\AnnotatedClassDiscovery;

class FieldGroupPluginManager extends PluginManagerBase {
  
  /**
   * Overrides Drupal\Component\Plugin\PluginManagerBase:$defaults.
   */
  protected $defaults = array(
    'field_types' => array(),
    'settings' => array(),
  );

  /**
   * Constructs a FormatterPluginManager object.
   *
   * @param array $namespaces
   *   An array of paths keyed by their corresponding namespaces.
   */
  public function __construct(\Traversable $namespaces) {
    // This is the essential line you have to use in your manager.
    $this->discovery = new AnnotatedClassDiscovery('field_group', $namespaces);
    // Every other line is a good practice.
    // $this->discovery = new ProcessDecorator($this->discovery, array($this, 'processDefinition'));
    // $this->discovery = new AlterDecorator($this->discovery, 'field_formatter_info');
    // $this->discovery = new CacheDecorator($this->discovery, 'field_formatter_types', 'field');
  }

}
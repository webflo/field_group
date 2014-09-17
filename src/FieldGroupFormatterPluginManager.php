<?php

/**
 * @file
 * Contains \Drupal\field_group\FieldgroupFormatterPluginManager.
 */

namespace Drupal\field_group;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Plugin type manager for all fieldgroup formatters.
 */
class FieldGroupFormatterPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new FieldGroupFormatterPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/field_group/FieldGroupFormatter', $namespaces, $module_handler, 'Drupal\field_group\Annotation\FieldGroupFormatter');

    $this->alterInfo('field_group_formatter');
    $this->setCacheBackend($cache_backend, 'field_group_formatter_info');
  }

}

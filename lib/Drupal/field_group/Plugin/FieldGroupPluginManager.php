<?php

/**
 * @file
 * Contains \Drupal\field_group\Plugin\FieldGroupPluginManager.
 */

namespace Drupal\field_group\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;


class FieldGroupPluginManager extends DefaultPluginManager {

  /**
   * Constructs the FieldGroupManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Language\LanguageManager $language_manager
   *   The language manager.
   */
  public function __construct(\Traversable $namespaces) {
    parent::__construct('Plugin/field_group', $namespaces);
  }

  // public function createInstance($plugin_id, array $configuration) {
  //   $plugin_definition = $this->getDefinition($plugin_id);

  //   $plugin_class = DefaultFactory::getPluginClass($plugin_id, $plugin_definition);
  //   dsm($plugin_class);
  //   return $plugin_class($plugin_id, $plugin_definition);
  // }

}

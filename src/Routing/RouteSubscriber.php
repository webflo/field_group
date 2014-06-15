<?php

/**
 * @file
 * Contains \Drupal\field_group\Routing\RouteSubscriber.
 */

namespace Drupal\field_group\Routing;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Field group routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The entity type manager
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $manager;

  /**
   * Constructs a RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $manager
   *   The entity type manager.
   */
  public function __construct(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
return;
    // Create a delete fieldgroup route for every entity.
    foreach ($this->manager->getDefinitions() as $entity_type_id => $entity_type) {
      $defaults = array();
      if ($entity_type->isFieldable() && $entity_type->hasLinkTemplate('admin-form')) {
        // Try to get the route from the current collection.
        if (!$entity_route = $collection->get($entity_type->getLinkTemplate('admin-form'))) {
          continue;
        }
        $path = $entity_route->getPath();

        $route = new Route(
          "$path/groups/{field_group}/delete",
          array('_entity_form' => 'field_instance_config.delete'),
          array('_entity_access' => 'field_instance_config.delete')
        );
        $collection->add("field_ui.delete_$entity_type_id", $route);

      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    $events[RoutingEvents::ALTER] = array('onAlterRoutes', -100);
    return $events;
  }

}

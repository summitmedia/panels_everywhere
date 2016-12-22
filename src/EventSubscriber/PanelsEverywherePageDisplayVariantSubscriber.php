<?php

/**
 * @file
 * Contains \Drupal\panels_everywhere_poc\EventSubscriber\PanelsEverywherePageDisplayVariantSubscriber.
 */

namespace Drupal\panels_everywhere\EventSubscriber;

use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Display\ContextAwareVariantInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\page_manager\Entity\PageVariantAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Selects the appropriate page display variant from 'site_template'.
 */
class PanelsEverywherePageDisplayVariantSubscriber implements EventSubscriberInterface {
  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $entityStorage;

  use ConditionAccessResolverTrait;

  /**
   * Constructs a new PageManagerRoutes.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityStorage = $entity_type_manager->getStorage('page');
  }

  /**
   * Selects the page display variant.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onSelectPageDisplayVariant(PageDisplayVariantSelectionEvent $event) {
    $page = $this->entityStorage->load('site_template');
    $route_options = $event->getRouteMatch()->getRouteObject()->getOptions();
    $isAdminRoute = array_key_exists('_admin_route', $route_options) && $route_options['_admin_route'];

    if (!is_object($page) || !$page->get('status') || $isAdminRoute) {
      return;
    }
    /** @var \Drupal\page_manager\Entity\PageVariant $variant */
    foreach ($page->getVariants() as $variant) {
      $variant_context = $variant->getContexts();
      /** @var \Drupal\Core\Condition\ConditionPluginCollection $conditions */
      $conditions = $variant->getSelectionConditions();
      /** @var  $condition */
      foreach ($conditions as $condition) {
        $condition->setContext('user', $variant_context['current_user']);
      }
      $access = $this->resolveConditions($conditions, $variant->getSelectionLogic());
//      $access = $this->resolveConditions($variant->getSelectionConditions(), $variant->getSelectionLogic());

      if (!$access) {
        continue;
      }

      $plugin = $variant->getVariantPlugin();
//      kpr($variant->access());
//      if ($variant->access()) {
        $event->setPluginId($plugin->getPluginId());
        $event->setPluginConfiguration($plugin->getConfiguration());
        $event->setContexts($variant->getContexts());
//      }
      break;
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = array('onSelectPageDisplayVariant');
    return $events;
  }

}

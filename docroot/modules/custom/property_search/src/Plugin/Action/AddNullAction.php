<?php

namespace Drupal\property_search\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides an action
 *
 * @Action(
 *   id = "property_search_add_custom_null_action",
 *   label = @Translation("-- Select --"),
 *   type = "node"
 * )
 */
class AddNullAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function access($operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    //$entity->id();
  }

}

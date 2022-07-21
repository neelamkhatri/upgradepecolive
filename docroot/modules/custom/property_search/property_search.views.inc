<?php

/**
 * @file
 * Views hooks.
 */
use Drupal\property_search\Plugin\views\filter\CustomNumericFilter;

/**
 * Implements hook_views_plugins_filter_alter().
 */
function property_search_views_plugins_filter_alter(&$info) {
    #echo "hello";kint($info);exit;
    if (isset($info['numeric'])) {
        $info['numeric']['class'] = 'Drupal\property_search\Plugin\views\filter\CustomNumericFilter';
    }
}

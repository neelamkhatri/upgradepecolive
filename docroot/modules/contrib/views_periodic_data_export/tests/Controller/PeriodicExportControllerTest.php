<?php

namespace Drupal\views_periodic_data_export\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the views_periodic_data_export module.
 */
class PeriodicExportControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "views_periodic_data_export PeriodicExportController's controller functionality",
      'description' => 'Test Unit for module views_periodic_data_export and controller PeriodicExportController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests views_periodic_data_export functionality.
   */
  public function testPeriodicExportController() {
    // Check that the basic functions of module views_periodic_data_export.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}

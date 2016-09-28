<?php

namespace Drupal\seem\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the seem module.
 */
class PageControllerTest extends WebTestBase {

  /**
   * Drupal\seem\SeemLayoutPluginManager definition.
   *
   * @var \Drupal\seem\SeemLayoutPluginManager
   */
  protected $plugin_manager_seem_layout_plugin;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "seem PageController's controller functionality",
      'description' => 'Test Unit for module seem and controller PageController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests seem functionality.
   */
  public function testPageController() {
    // Check that the basic functions of module seem.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via App Console.');
  }

}

<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\phpunit\patch;

use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy tests for lib/mutenancylib.php core additions.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class navigationlib_test extends \advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * @covers \settings_navigation::initialise, \settings_navigation::load_tenant_settings
     */
    public function test_settings_navigation_initialise(): void {
        global $CFG, $PAGE;
        require_once("$CFG->libdir/navigationlib.php");

        if (tenancy::is_active()) {
            tenancy::deactivate();
        }

        $this->setAdminUser();

        $syscontext = \context_system::instance();

        $PAGE = new \moodle_page();
        $PAGE->set_url(new \moodle_url('/'));
        $PAGE->set_context($syscontext);
        $nav = new \settings_navigation($PAGE);
        $nav->initialise();
        $this->assertNotContains('tenantsettings', $nav->get_children_key_list());

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant = $generator->create_tenant();
        $tenantcontext = \context_tenant::instance($tenant->id);

        $PAGE = new \moodle_page();
        $PAGE->set_url(new \moodle_url('/'));
        $PAGE->set_context($syscontext);
        $nav = new \settings_navigation($PAGE);
        $nav->initialise();
        $this->assertNotContains('tenantsettings', $nav->get_children_key_list());

        $PAGE = new \moodle_page();
        $PAGE->set_url(new \moodle_url('/'));
        $PAGE->set_context($tenantcontext);
        $nav = new \settings_navigation($PAGE);
        $nav->initialise();
        $this->assertContains('tenantsettings', $nav->get_children_key_list());
    }
}

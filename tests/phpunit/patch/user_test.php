<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\phpunit\patch;

use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy tests for upstream modifications.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_test extends \advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * @covers \core\user::require_active_user
     */
    public function test_require_active_user(): void {
        tenancy::activate();

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant(['archived' => 1]);

        $user0 = $this->getDataGenerator()->create_user();
        $user1 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id]);
        $user2 = $this->getDataGenerator()->create_user(['tenantid' => $tenant2->id]);
        $user3 = $this->getDataGenerator()->create_user(['suspended' => 1]);
        $user4 = $this->getDataGenerator()->create_user(['tenantid' => $tenant1->id, 'suspended' => 1]);

        \core\user::require_active_user($user0, false);
        \core\user::require_active_user($user1, false);
        \core\user::require_active_user($user2, false);
        \core\user::require_active_user($user3, false);
        \core\user::require_active_user($user4, false);

        \core\user::require_active_user($user0, true);
        \core\user::require_active_user($user1, true);
        try {
            \core\user::require_active_user($user2, true);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertSame('Suspended account', $ex->getMessage());
        }
        try {
            \core\user::require_active_user($user3, true);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertSame('Suspended account', $ex->getMessage());
        }
        try {
            \core\user::require_active_user($user3, true);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertSame('Suspended account', $ex->getMessage());
        }
    }
}

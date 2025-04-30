<?php
// This file is part of Multi-tenancy plugin for Moodle™.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong
// phpcs:disable moodle.Commenting.DocblockDescription.Missing

namespace tool_mutenancy\phpunit\external;

use tool_mutenancy\external\form_tenant_managers_userids;

/**
 * Multi-tenancy external function tests.
 *
 * @group       MuTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \tool_mutenancy\external\form_tenant_managers_userids
 */
final class form_tenant_managers_userids_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers ::execute
     */
    public function test_execute(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $syscontext = \context_system::instance();

        $roleid = create_role('man', 'man', 'man');
        assign_capability('tool/mutenancy:admin', CAP_ALLOW, $roleid, $syscontext->id);

        $tenant1 = $generator->create_tenant();
        $tenantcontext1 = \context_tenant::instance($tenant1->id);
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $admin = get_admin();
        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $user0 = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'User',
            'email' => 'user0@example.com',
        ]);
        $user1 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant1->id,
            'firstname' => 'First',
            'lastname' => 'User',
            'email' => 'user1@example.com',
        ]);
        $user2 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant2->id,
            'firstname' => 'Second',
            'lastname' => 'User',
            'email' => 'user2@example.com',
        ]);
        $user3 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant3->id,
            'firstname' => 'Third',
            'lastname' => 'User',
            'email' => 'user3@example.com',
        ]);

        $this->setUser($manager);
        $result = form_tenant_managers_userids::execute('', $tenant1->id);
        $this->assertNull($result['notice']);
        $this->assertCount(4, $result['list']);
        $this->assertSame($result['list'][0]['value'], $manager->id);
        $this->assertSame($result['list'][1]['value'], $admin->id);
        $this->assertSame($result['list'][2]['value'], $user1->id);
        $this->assertSame($result['list'][3]['value'], $user0->id);

        $this->setUser($manager);
        $result = form_tenant_managers_userids::execute('First', $tenant1->id);
        $this->assertNull($result['notice']);
        $this->assertCount(1, $result['list']);
        $this->assertSame($result['list'][0]['value'], $user1->id);

        $this->setUser($admin);
        $result = form_tenant_managers_userids::execute('First', $tenant1->id);
        $this->assertNull($result['notice']);
        $this->assertCount(1, $result['list']);
        $this->assertSame($result['list'][0]['value'], $user1->id);
    }

    /**
     * @covers ::get_label_callback
     */
    public function test_get_label_callback(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $syscontext = \context_system::instance();

        $roleid = create_role('man', 'man', 'man');
        assign_capability('tool/mutenancy:admin', CAP_ALLOW, $roleid, $syscontext->id);

        $tenant1 = $generator->create_tenant();
        $tenantcontext1 = \context_tenant::instance($tenant1->id);
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $user0 = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'User',
            'email' => 'user0@example.com',
        ]);
        $user1 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant1->id,
            'firstname' => 'First',
            'lastname' => 'User',
            'email' => 'user1@example.com',
        ]);
        $user2 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant2->id,
            'firstname' => 'Second',
            'lastname' => 'User',
            'email' => 'user2@example.com',
        ]);
        $user3 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant3->id,
            'firstname' => 'Third',
            'lastname' => 'User',
            'email' => 'user3@example.com',
        ]);

        $callback = form_tenant_managers_userids::get_label_callback(['tenantid' => $tenant1->id]);

        $this->setUser($manager);

        $result = $callback($user0->id);
        $this->assertStringContainsString('Global User', $result);
        $this->assertStringNotContainsString($user0->email, $result);

        $result = $callback($user1->id);
        $this->assertStringContainsString('First User', $result);
        $this->assertStringNotContainsString($user1->email, $result);

        $result = $callback($user2->id);
        $this->assertStringContainsString('Second User', $result);
        $this->assertStringNotContainsString($user2->email, $result);

        assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $roleid, $syscontext->id);

        $result = $callback($user0->id);
        $this->assertStringContainsString('Global User', $result);
        $this->assertStringContainsString($user0->email, $result);

        $result = $callback($user1->id);
        $this->assertStringContainsString('First User', $result);
        $this->assertStringContainsString($user1->email, $result);

        $result = $callback($user2->id);
        $this->assertStringContainsString('Second User', $result);
        $this->assertStringContainsString($user2->email, $result);
    }

    /**
     * @covers ::validate_userid
     */
    public function test_validate_userid(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $syscontext = \context_system::instance();

        $roleid = create_role('man', 'man', 'man');
        assign_capability('tool/mutenancy:admin', CAP_ALLOW, $roleid, $syscontext->id);

        $tenant1 = $generator->create_tenant();
        $tenantcontext1 = \context_tenant::instance($tenant1->id);
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant();

        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $user0 = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'User',
            'email' => 'user0@example.com',
        ]);
        $user1 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant1->id,
            'firstname' => 'First',
            'lastname' => 'User',
            'email' => 'user1@example.com',
        ]);
        $user2 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant2->id,
            'firstname' => 'Second',
            'lastname' => 'User',
            'email' => 'user2@example.com',
        ]);
        $user3 = $this->getDataGenerator()->create_user([
            'tenantid' => $tenant3->id,
            'firstname' => 'Third',
            'lastname' => 'User',
            'email' => 'user3@example.com',
        ]);

        $this->setUser($manager);

        $this->assertSame(null, form_tenant_managers_userids::validate_userid($user0->id, $tenant1->id));
        $this->assertSame(null, form_tenant_managers_userids::validate_userid($user1->id, $tenant1->id));
        $this->assertSame(null, form_tenant_managers_userids::validate_userid($user2->id, $tenant2->id));
        $this->assertSame('Error', form_tenant_managers_userids::validate_userid($user2->id, $tenant1->id));
    }
}

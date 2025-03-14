<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\phpunit\external;

use tool_mutenancy\external\form_tenant_assoccohortid;

/**
 * Multi-tenancy external function tests.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \tool_mutenancy\external\form_tenant_assoccohortid
 */
final class form_tenant_assoccohortid_test extends \advanced_testcase {
    public function setUp(): void {
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
        $tenantcontext2 = \context_tenant::instance($tenant2->id);

        $admin = get_admin();
        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'name' => 'First kohort',
            'idnumber' => 'koh1',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'name' => 'Second kohort',
            'idnumber' => 'koh2',
        ]);
        $cohort3 = $this->getDataGenerator()->create_cohort([
            'name' => 'Third kohort',
            'idnumber' => 'koh2',
            'visible' => 0,
        ]);
        $cohort4 = $this->getDataGenerator()->create_cohort([
            'name' => 'Fourth kohort',
            'idnumber' => 'koh2',
            'contextid' => $tenantcontext2->id,
        ]);

        $this->setUser($manager);

        $result = form_tenant_assoccohortid::execute('', $tenant1->id);
        $this->assertNull($result['notice']);
        $expected = [
            ['value' => $cohort1->id, 'label' => $cohort1->name],
            ['value' => $cohort2->id, 'label' => $cohort2->name],
        ];
        $this->assertSame($expected, $result['list']);

        $result = form_tenant_assoccohortid::execute('First', $tenant1->id);
        $this->assertNull($result['notice']);
        $expected = [
            ['value' => $cohort1->id, 'label' => $cohort1->name],
        ];
        $this->assertSame($expected, $result['list']);

        $result = form_tenant_assoccohortid::execute('koh2', $tenant1->id);
        $this->assertNull($result['notice']);
        $expected = [
            ['value' => $cohort2->id, 'label' => $cohort2->name],
        ];
        $this->assertSame($expected, $result['list']);

        $this->setUser($admin);

        $result = form_tenant_assoccohortid::execute('', $tenant1->id);
        $this->assertNull($result['notice']);
        $expected = [
            ['value' => $cohort1->id, 'label' => $cohort1->name],
            ['value' => $cohort2->id, 'label' => $cohort2->name],
            ['value' => $cohort3->id, 'label' => $cohort3->name],
        ];
        $this->assertSame($expected, $result['list']);

        $result = form_tenant_assoccohortid::execute('', 0);
        $this->assertNull($result['notice']);
        $this->assertSame($expected, $result['list']);
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
        $tenantcontext2 = \context_tenant::instance($tenant2->id);

        $admin = get_admin();
        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'name' => 'First kohort',
            'idnumber' => 'koh1',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'name' => 'Second kohort',
            'idnumber' => 'koh2',
        ]);
        $cohort3 = $this->getDataGenerator()->create_cohort([
            'name' => 'Third kohort',
            'idnumber' => 'koh2',
            'visible' => 0,
        ]);
        $cohort4 = $this->getDataGenerator()->create_cohort([
            'name' => 'Fourth kohort',
            'idnumber' => 'koh2',
            'contextid' => $tenantcontext2->id,
        ]);

        $callback = form_tenant_assoccohortid::get_label_callback(['tenantid' => $tenant1->id]);

        $this->assertSame($cohort1->name, $callback($cohort1->id));
        $this->assertSame($cohort2->name, $callback($cohort2->id));
        $this->assertSame($cohort3->name, $callback($cohort3->id));

        $this->assertSame('Error', $callback(-1));
    }

    /**
     * @covers ::validate_cohortid
     */
    public function test_validate_cohortid(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $syscontext = \context_system::instance();

        $roleid = create_role('man', 'man', 'man');
        assign_capability('tool/mutenancy:admin', CAP_ALLOW, $roleid, $syscontext->id);

        $tenant1 = $generator->create_tenant();
        $tenantcontext1 = \context_tenant::instance($tenant1->id);
        $tenant2 = $generator->create_tenant();
        $tenantcontext2 = \context_tenant::instance($tenant2->id);

        $admin = get_admin();
        $manager = $this->getDataGenerator()->create_user([
            'firstname' => 'Global',
            'lastname' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        role_assign($roleid, $manager->id, $tenantcontext1->id);

        $cohort1 = $this->getDataGenerator()->create_cohort([
            'name' => 'First kohort',
            'idnumber' => 'koh1',
        ]);
        $cohort2 = $this->getDataGenerator()->create_cohort([
            'name' => 'Second kohort',
            'idnumber' => 'koh2',
        ]);
        $cohort3 = $this->getDataGenerator()->create_cohort([
            'name' => 'Third kohort',
            'idnumber' => 'koh2',
            'visible' => 0,
        ]);
        $cohort4 = $this->getDataGenerator()->create_cohort([
            'name' => 'Fourth kohort',
            'idnumber' => 'koh2',
            'contextid' => $tenantcontext2->id,
        ]);

        $this->setUser($manager);
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort1->id, $tenant1->id));
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort2->id, $tenant1->id));
        $this->assertSame('Error', form_tenant_assoccohortid::validate_cohortid($cohort3->id, $tenant1->id));
        $this->assertSame('Error', form_tenant_assoccohortid::validate_cohortid($cohort4->id, $tenant1->id));
        $this->assertSame('Error', form_tenant_assoccohortid::validate_cohortid($cohort1->id, $tenant2->id));

        $this->setUser($admin);
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort1->id, $tenant1->id));
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort2->id, $tenant1->id));
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort3->id, $tenant1->id));
        $this->assertSame('Error', form_tenant_assoccohortid::validate_cohortid($cohort4->id, $tenant1->id));
        $this->assertSame(null, form_tenant_assoccohortid::validate_cohortid($cohort1->id, $tenant2->id));
    }
}

<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\phpunit\reportbuidler\datasource;

use tool_mutenancy\reportbuilder\datasource\tenants as templatesource;

/**
 * Multi-tenancy tenants datasource tests.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \tool_mutenancy\reportbuilder\datasource\tenants
 */
final class tenants_test extends \core_reportbuilder\tests\core_reportbuilder_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_datasource(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');
        /** @var \core_reportbuilder_generator $rbgenerator */
        $rbgenerator = self::getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant(['archived' => 1]);

        $report = $rbgenerator->create_report([
            'name' => 'RB tenants',
            'source' => templatesource::class,
            'default' => false,
        ]);

        $rbgenerator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tenant:name']);
        $rbgenerator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tenant:idnumber']);
        $rbgenerator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tenant:archived']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        $contentcerts = [
            [$tenant1->name, $tenant1->idnumber, 'No'],
            [$tenant2->name, $tenant2->idnumber, 'No'],
            [$tenant3->name, $tenant3->idnumber, 'Yes'],
        ];
        $this->assertEqualsCanonicalizing($contentcerts, $content);
    }

    public function test_stress_datasource(): void {
        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $tenant2 = $generator->create_tenant();
        $tenant3 = $generator->create_tenant(['archived' => 1]);

        $this->datasource_stress_test_columns(templatesource::class);
        $this->datasource_stress_test_columns_aggregation(templatesource::class);
        $this->datasource_stress_test_conditions(templatesource::class, 'tenant:name');
        $this->datasource_stress_test_conditions(templatesource::class, 'tenant:idnumber');
        $this->datasource_stress_test_conditions(templatesource::class, 'tenant:archived');
    }
}

<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

/**
 * Tenancy behat generator.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_mutenancy_generator extends \behat_generator_base {

    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'tenants' => [
                'singular' => 'tenant',
                'datagenerator' => 'tenant',
                'required' => ['name', 'idnumber'],
            ],
            'tenant managers' => [
                'singular' => 'tenant manager',
                'datagenerator' => 'tenant_manager',
                'required' => ['tenant', 'user'],
                'switchids' => ['tenant' => 'tenantid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Gets the tenant id from idnumber.
     *
     * @param string $idnumber
     * @return int
     */
    protected function get_tenant_id(string $idnumber): int {
        global $DB;

        if (!$id = $DB->get_field('tool_mutenancy_tenant', 'id', ['idnumber' => $idnumber])) {
            throw new Exception('The specified tenant with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }
}

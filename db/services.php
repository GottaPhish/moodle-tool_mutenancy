<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy services.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = [
    'tool_mutenancy_form_tenant_assoccohortid' => [
        'classname' => tool_mutenancy\external\form_tenant_assoccohortid::class,
        'description' => 'Return list of cohorts for tenant associated users.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
    ],

    'tool_mutenancy_form_tenant_managers_userids' => [
        'classname' => tool_mutenancy\external\form_tenant_managers_userids::class,
        'description' => 'Return list of candidate users for tenant managers.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
    ],

    'tool_mutenancy_form_associate_add_userids' => [
        'classname' => tool_mutenancy\external\form_associate_add_userids::class,
        'description' => 'Return list of candidate users for tenant association.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
    ],

    'tool_mutenancy_form_user_allocate_tenantid' => [
        'classname' => tool_mutenancy\external\form_user_allocate_tenantid::class,
        'description' => 'Return list of tenant candidates for tenant managers.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
    ],
];

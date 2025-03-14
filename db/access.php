<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy capabilities.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = [
    // View tenant details.
    'tool/mutenancy:view' => [
        'captype' => 'read',
        'riskbitmask' => RISK_PERSONAL,
        'contextlevel' => CONTEXT_TENANT,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    /** Add, update and delete tenants; add and remove tenant managers */
    'tool/mutenancy:admin' => [
        'captype' => 'write',
        'riskbitmask' => RISK_PERSONAL || RISK_DATALOSS || RISK_SPAM,
        'contextlevel' => CONTEXT_TENANT,
        'archetypes' => [
            'manager' => CAP_ALLOW, // Excluded from tenantmanager archetype.
        ],
    ],
    /** Switch to tenant site - not applicable to tenant members, view cap is also needed in tenant */
    'tool/mutenancy:switch' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Allocate global users as tenants members and deallocate members.
    'tool/mutenancy:allocate' => [
        'captype' => 'write',
        'riskbitmask' => RISK_DATALOSS,
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Create new user account for tenant member.
    'tool/mutenancy:membercreate' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_TENANT | RISK_PERSONAL,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Update user account of tenant member.
    'tool/mutenancy:memberupdate' => [
        'captype' => 'write',
        'riskbitmask' => RISK_DATALOSS | RISK_PERSONAL,
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Delete user account of tenant member.
    'tool/mutenancy:memberdelete' => [
        'captype' => 'write',
        'riskbitmask' => RISK_DATALOSS,
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Change tenant authentication settings.
    'tool/mutenancy:configauth' => [
        'captype' => 'write',
        'riskbitmask' => RISK_DATALOSS,
        'contextlevel' => CONTEXT_TENANT,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
    // Change theme and tenant branding.
    'tool/mutenancy:configappearance' => [
        'captype' => 'write',
        'riskbitmask' => RISK_SPAM,
        'contextlevel' => CONTEXT_TENANT,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
];

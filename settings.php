<?php
// This file is part of Multi-tenancy plugin for Moodle™.

use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy settings.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var admin_root $ADMIN */

if (tenancy::is_active()) {
    $ADMIN->add(
        'root',
        new admin_externalpage('tool_mutenancy_tenants',
            new lang_string('tenants', 'tool_mutenancy'),
            new moodle_url('/admin/tool/mutenancy/index.php'),
            'tool/mutenancy:view')
    );
} else {
    $ADMIN->add(
        'root',
        new admin_externalpage('tool_mutenancy_tenants',
            new lang_string('tenants', 'tool_mutenancy'),
            new moodle_url('/admin/tool/mutenancy/index.php'),
            'moodle/site:config')
    );
}

$settings = new admin_settingpage('tool_mutenancy_settings',
    new lang_string('settings', 'tool_mutenancy'),
    'moodle/site:config');

$settings->add(new admin_setting_configtext(
    'tool_mutenancy/tenantlimit',
    new lang_string('setting_tenantlimit', 'tool_mutenancy'),
    new lang_string('setting_tenantlimit_desc', 'tool_mutenancy'),
    50,
    PARAM_INT,
    4
));

$ADMIN->add('root', $settings, 'tool_mutenancy_tenants');


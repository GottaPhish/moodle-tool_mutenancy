<?php
// This file is part of Multi-tenancy plugin for Moodle™.

use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy uninstallation support.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Delete tool_mutenancy data and revert core changes before uninstall.
 *
 * @return bool always true
 */
function xmldb_tool_mutenancy_uninstall() {
    global $DB;

    $dbman = $DB->get_manager();

    if (tenancy::is_active()) {
        tenancy::deactivate();
    }

    $table = new xmldb_table('context');
    $index = new xmldb_index('tenantid', XMLDB_INDEX_NOTUNIQUE, ['tenantid']);
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }

    $table = new xmldb_table('context');
    $field = new xmldb_field('tenantid');
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    $table = new xmldb_table('user');
    $index = new xmldb_index('tenantid', XMLDB_INDEX_NOTUNIQUE, ['tenantid']);
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }

    $table = new xmldb_table('user');
    $field = new xmldb_field('tenantid');
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    return true;
}

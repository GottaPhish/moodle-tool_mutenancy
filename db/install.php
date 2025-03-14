<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy installation script.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Make core changes necessary for multi-tenancy plugin.
 *
 * @return bool always true
 */
function xmldb_tool_mutenancy_install(): bool {
    global $DB;

    $dbman = $DB->get_manager();

    $table = new xmldb_table('context');
    $field = new xmldb_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'locked');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    $table = new xmldb_table('context');
    $index = new xmldb_index('tenantid', XMLDB_INDEX_NOTUNIQUE, ['tenantid']);
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    $table = new xmldb_table('user');
    $field = new xmldb_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'moodlenetprofile');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    $table = new xmldb_table('user');
    $index = new xmldb_index('tenantid', XMLDB_INDEX_NOTUNIQUE, ['tenantid']);
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    return true;
}

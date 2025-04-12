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

namespace tool_mutenancy\external;

use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Associated users cohort autocompletion.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class form_tenant_assoccohortid extends \tool_mulib\external\form_autocomplete_field {
    /** @var int max returned results */
    const MAX_RESULTS = 20;

    /**
     * True means returned field data is array, false means value is scalar.
     *
     * @return bool
     */
    public static function is_multi_select_field(): bool {
        return false;
    }

    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(PARAM_RAW, 'The search query', VALUE_REQUIRED),
            'tenantid' => new external_value(PARAM_INT, 'Tenant id, 0 if creating new tenant', VALUE_REQUIRED),
        ]);
    }

    /**
     * Gets list of available cohorts.
     *
     * @param string $query The search request.
     * @param int $tenantid
     * @return array
     */
    public static function execute(string $query, int $tenantid): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query, 'tenantid' => $tenantid]);
        $query = $params['query'];
        $tenantid = $params['tenantid'];

        if ($tenantid) {
            $context = \context_tenant::instance($tenantid);
        } else {
            $context = \context_system::instance();
        }
        self::validate_context($context);
        require_capability('tool/mutenancy:admin', $context);

        list($searchsql, $params) = self::get_cohort_search_query($query, 'ch');
        if ($tenantid) {
            $params['tenantid'] = $tenantid;
            $ortenantid = "OR c.tenantid = :tenantid";

        } else {
            $ortenantid = "";
        }

        $sql = "SELECT ch.id, ch.name, ch.contextid, ch.visible
                  FROM {cohort} ch
                  JOIN {context} c ON c.id = ch.contextid AND (c.tenantid IS NULL $ortenantid)
             LEFT JOIN {tool_mutenancy_tenant} t ON t.cohortid = ch.id
                 WHERE t.id IS NULL AND $searchsql
              ORDER BY ch.name ASC";
        $rs = $DB->get_recordset_sql($sql, $params);

        $notice = null;
        $list = [];
        $count = 0;
        foreach ($rs as $cohort) {
            $context = \context::instance_by_id($cohort->contextid);
            if (!$cohort->visible) {
                if (!has_capability('moodle/cohort:view', $context)) {
                    continue;
                }
            }
            $count++;
            if ($count > self::MAX_RESULTS) {
                $notice = get_string('toomanyrecords', 'tool_mulib', self::MAX_RESULTS);
                break;
            }
            $list[] = ['value' => $cohort->id, 'label' => format_string($cohort->name)];
        }
        $rs->close();

        return [
            'notice' => $notice,
            'list' => $list,
        ];
    }

    /**
     * Return function that return label for given value.
     *
     * @param array $arguments
     * @return callable
     */
    public static function get_label_callback(array $arguments): callable {
        return function($value) use ($arguments): string {
            global $DB;

            $cohort = $DB->get_record('cohort', ['id' => $value]);
            if (!$cohort) {
                return get_string('error');
            }
            return format_string($cohort->name);
        };
    }

    /**
     * Validate user can select cohort as associate cohort users including all permissions.
     *
     * @param int $cohortid
     * @param int $tenantid 0 menas no tenant yet
     * @return string|null null means ids ok, string is error
     */
    public static function validate_cohortid(int $cohortid, int $tenantid): ?string {
        global $DB;
        $cohort = $DB->get_record('cohort', ['id' => $cohortid]);
        if (!$cohort) {
            return get_string('error');
        }
        $context = \context::instance_by_id($cohort->contextid, IGNORE_MISSING);
        if (!$context) {
            return get_string('error');
        }

        if ($tenantid) {
            $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid]);
            if (!$tenant) {
                return get_string('error');
            }
            if ($tenant->assoccohortid == $cohortid) {
                // Keep whatever existing cohort is there.
                return null;
            }
            if ($context->tenantid && $context->tenantid != $tenantid) {
                // Do not allow cohorts from other tenants.
                return get_string('error');
            }
            $tenantcontext = \context_tenant::instance($tenant->id);
        } else {
            $tenantcontext = \context_system::instance();
        }
        if (!has_capability('tool/mutenancy:admin', $tenantcontext)) {
            return get_string('error');
        }

        if ($DB->record_exists('tool_mutenancy_tenant', ['cohortid' => $cohortid])) {
            // Do not allow tenant member cohorts.
            return get_string('error');
        }

        if (!$cohort->visible) {
            if (!has_capability('moodle/cohort:view', $context)) {
                return get_string('error');
            }
        }

        return null;
    }
}

<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\external;

use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Allocate user to tenant candidates.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class form_user_allocate_tenantid extends \tool_mulib\external\form_autocomplete_field {
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
            'tenantid' => new external_value(PARAM_INT, 'Tenant id, 0 means no tenant', VALUE_REQUIRED),
        ]);
    }

    /**
     * Gets list of tenant allocation targets.
     *
     * @param int $tenantid 0 menas no tenant
     * @param string $query The search request.
     * @return array
     */
    public static function execute(string $query, int $tenantid): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query, 'tenantid' => $tenantid]);
        $query = $params['query'];
        $tenantid = $params['tenantid'];

        $syscontext = \context_system::instance();
        self::validate_context($syscontext);
        require_capability('tool/mutenancy:allocate', $syscontext);

        list($searchsql, $params) = self::get_tenant_search_query($query, 't');
        $params['tenantid'] = $tenantid;

        $sql = "SELECT t.id, t.name
                  FROM {tool_mutenancy_tenant} t
                 WHERE $searchsql AND t.id <> :tenantid
              ORDER BY t.name ASC";
        $rs = $DB->get_recordset_sql($sql, $params);

        $notice = null;
        $list = [];
        $count = 0;
        foreach ($rs as $tenant) {
            $count++;
            if ($count > self::MAX_RESULTS) {
                $notice = get_string('toomanyrecords', 'tool_mulib', self::MAX_RESULTS);
                break;
            }
            $list[] = ['value' => $tenant->id, 'label' => format_string($tenant->name)];
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

            $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $value]);
            if (!$tenant) {
                return get_string('error');
            }
            return format_string($tenant->name);
        };
    }
}

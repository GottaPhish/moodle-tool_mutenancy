<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

namespace tool_mutenancy\external;

use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Tenant managers assignment candidates.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class form_tenant_managers_userids extends \tool_mulib\external\form_autocomplete_field {
    /**
     * True means returned field data is array, false means value is scalar.
     *
     * @return bool
     */
    public static function is_multi_select_field(): bool {
        return true;
    }

    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(PARAM_RAW, 'The search query', VALUE_REQUIRED),
            'tenantid' => new external_value(PARAM_INT, 'Tenant id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Gets list of available tenant manager candidates.
     *
     * @param string $query The search request.
     * @param int $tenantid
     * @return array
     */
    public static function execute(string $query, int $tenantid): array {
        global $DB, $OUTPUT, $CFG;

        $params = self::validate_parameters(self::execute_parameters(), ['query' => $query, 'tenantid' => $tenantid]);
        $query = $params['query'];
        $tenantid = $params['tenantid'];

        $context = \context_tenant::instance($tenantid);
        self::validate_context($context);
        require_capability('tool/mutenancy:admin', $context);

        $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);

        $fields = \core_user\fields::for_name()->with_identity($context, false);
        $extrafields = $fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        list($searchsql, $searchparams) = users_search_sql($query, 'u', true, $extrafields);
        list($sortsql, $sortparams) = users_order_by_sql('u', $query, $context);
        $params = array_merge($searchparams, $sortparams);
        $params['tenantid'] = $tenant->id;

        $sql = "SELECT u.*
                  FROM {user} u
                 WHERE {$searchsql}
                       AND u.deleted = 0 AND u.confirmed = 1
                       AND u.tenantid IS NULL OR u.tenantid = :tenantid
              ORDER BY {$sortsql}";

        $rs = $DB->get_recordset_sql($sql, $params, 0, $CFG->maxusersperpage + 1);

        return self::prepare_user_list($rs, $extrafields);
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

            $record = $DB->get_record('user', ['id' => $value]);

            $context = \context_tenant::instance($arguments['tenantid']);

            return self::prepare_user_label($record, $context);
        };
    }

    /**
     * Validate user can be added as manager.
     *
     * @param int $userid
     * @param int $tenantid tenant id
     * @return string|null null means ids ok, string is error
     */
    public static function validate_userid(int $userid, int $tenantid): ?string {
        global $DB;

        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0, 'confirmed' => 1]);
        if (!$user) {
            return get_string('error');
        }

        if ($user->tenantid && $user->tenantid != $tenantid) {
            return get_string('error');
        }

        return null;
    }
}

<?php
namespace tool_mutenancy\external;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use tool_mutenancy\local\tenant as tenant_manager;
use tool_mutenancy\local\tenancy;

class create_tenant extends \core_external\external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'name' => new external_value(PARAM_TEXT, 'Tenant name'),
            'idnumber' => new external_value(PARAM_RAW_TRIMMED, 'Unique tenant identifier'),
            'loginshow' => new external_value(PARAM_BOOL, 'Display tenant on login selector', VALUE_DEFAULT, 0),
            'memberlimit' => new external_value(PARAM_INT, 'Maximum members, 0 for unlimited', VALUE_DEFAULT, 0),
            'assoccohortid' => new external_value(
                PARAM_INT,
                'Existing cohort to associate',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'assoccohortcreate' => new external_value(
                PARAM_BOOL,
                'Create a new associated cohort automatically',
                VALUE_DEFAULT,
                0
            ),
            'sitefullname' => new external_value(
                PARAM_TEXT,
                'Tenant specific site full name',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'siteshortname' => new external_value(
                PARAM_TEXT,
                'Tenant specific site short name',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'categoryid' => new external_value(
                PARAM_INT,
                'Existing top level category to reuse',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'categoryname' => new external_value(
                PARAM_TEXT,
                'Name for the new tenant category',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'categoryidnumber' => new external_value(
                PARAM_TEXT,
                'ID number for the new tenant category',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'cohortname' => new external_value(
                PARAM_TEXT,
                'Name for the tenant member cohort',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'cohortidnumber' => new external_value(
                PARAM_TEXT,
                'ID number for the tenant member cohort',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'archived' => new external_value(PARAM_BOOL, 'Create tenant as archived', VALUE_DEFAULT, 0),
        ]);
    }

    public static function execute(
        string $name,
        string $idnumber,
        int $loginshow = 0,
        int $memberlimit = 0,
        ?int $assoccohortid = null,
        int|string $assoccohortcreate = 0,
        ?string $sitefullname = null,
        ?string $siteshortname = null,
        ?int $categoryid = null,
        ?string $categoryname = null,
        ?string $categoryidnumber = null,
        ?string $cohortname = null,
        ?string $cohortidnumber = null,
        int $archived = 0
    ): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'name' => $name,
            'idnumber' => $idnumber,
            'loginshow' => $loginshow,
            'memberlimit' => $memberlimit,
            'assoccohortid' => $assoccohortid,
            'assoccohortcreate' => $assoccohortcreate,
            'sitefullname' => $sitefullname,
            'siteshortname' => $siteshortname,
            'categoryid' => $categoryid,
            'categoryname' => $categoryname,
            'categoryidnumber' => $categoryidnumber,
            'cohortname' => $cohortname,
            'cohortidnumber' => $cohortidnumber,
            'archived' => $archived,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('tool/mutenancy:admin', $context);

        if (!tenancy::is_active()) {
            throw new \core\exception\invalid_parameter_exception('Multi-tenancy is not active');
        }

        $assoccohortcreate = (int)$params['assoccohortcreate'];
        if ($assoccohortcreate && !has_capability('moodle/cohort:manage', $context)) {
            throw new \required_capability_exception($context, 'moodle/cohort:manage', 'nopermissions', '');
        }

        $params['assoccohortcreate'] = $assoccohortcreate;
        $tenant = tenant_manager::create((object)$params);

        return self::format_tenant($tenant);
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Tenant ID'),
            'name' => new external_value(PARAM_TEXT, 'Tenant name'),
            'idnumber' => new external_value(PARAM_RAW, 'Tenant identifier'),
            'loginshow' => new external_value(PARAM_BOOL, 'Visible in login selector'),
            'memberlimit' => new external_value(
                PARAM_INT,
                'Maximum members',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'categoryid' => new external_value(PARAM_INT, 'Category ID'),
            'cohortid' => new external_value(PARAM_INT, 'Tenant cohort ID'),
            'assoccohortid' => new external_value(
                PARAM_INT,
                'Associated cohort ID',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'sitefullname' => new external_value(
                PARAM_TEXT,
                'Tenant specific site full name',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'siteshortname' => new external_value(
                PARAM_TEXT,
                'Tenant specific site short name',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'archived' => new external_value(PARAM_BOOL, 'Archived flag'),
            'timecreated' => new external_value(PARAM_INT, 'Creation timestamp'),
            'timemodified' => new external_value(PARAM_INT, 'Last modification timestamp'),
        ]);
    }

    private static function format_tenant(\stdClass $tenant): array {
        return [
            'id' => (int)$tenant->id,
            'name' => $tenant->name,
            'idnumber' => $tenant->idnumber,
            'loginshow' => (bool)$tenant->loginshow,
            'memberlimit' => $tenant->memberlimit === null ? null : (int)$tenant->memberlimit,
            'categoryid' => (int)$tenant->categoryid,
            'cohortid' => (int)$tenant->cohortid,
            'assoccohortid' => $tenant->assoccohortid === null ? null : (int)$tenant->assoccohortid,
            'sitefullname' => $tenant->sitefullname,
            'siteshortname' => $tenant->siteshortname,
            'archived' => (bool)$tenant->archived,
            'timecreated' => (int)$tenant->timecreated,
            'timemodified' => (int)$tenant->timemodified,
        ];
    }
}

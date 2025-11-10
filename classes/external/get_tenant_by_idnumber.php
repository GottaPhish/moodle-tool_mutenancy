<?php
namespace tool_mutenancy;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\tenant as tenant_manager;

class get_tenant_by_idnumber extends \core_external\external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'idnumber' => new external_value(PARAM_RAW_TRIMMED, 'Tenant idnumber'),
        ]);
    }

    public static function execute(string $idnumber): array {
        if (!tenancy::is_active()) {
            throw new \core\exception\invalid_parameter_exception('Multi-tenancy is not active');
        }

        $params = self::validate_parameters(self::execute_parameters(), [
            'idnumber' => $idnumber,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('tool/mutenancy:admin', $context);

        $tenant = tenant_manager::fetch_by_idnumber($params['idnumber']);
        if (!$tenant) {
            throw new \core\exception\invalid_parameter_exception('Unknown tenant idnumber');
        }

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

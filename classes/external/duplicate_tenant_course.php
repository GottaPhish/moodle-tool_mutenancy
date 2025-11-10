<?php
namespace tool_mutenancy;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use tool_mutenancy\local\tenancy;

class duplicate_tenant_course extends \core_external\external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tenantid' => new external_value(PARAM_INT, 'Tenant ID'),
            'courseid' => new external_value(PARAM_INT, 'Source course ID'),
            'fullname' => new external_value(
                PARAM_TEXT,
                'Full name for the duplicated course',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'shortname' => new external_value(
                PARAM_TEXT,
                'Short name for the duplicated course',
                VALUE_OPTIONAL,
                null,
                NULL_ALLOWED
            ),
            'visible' => new external_value(PARAM_BOOL, 'Whether the course is visible', VALUE_DEFAULT, 1),
            'includeusers' => new external_value(
                PARAM_BOOL,
                'Include enrolled users when duplicating',
                VALUE_DEFAULT,
                0
            ),
        ]);
    }

    public static function execute(
        int $tenantid,
        int $courseid,
        ?string $fullname = null,
        ?string $shortname = null,
        int $visible = 1,
        int $includeusers = 0
    ): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/externallib.php');
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        if (!tenancy::is_active()) {
            throw new \core\exception\invalid_parameter_exception('Multi-tenancy is not active');
        }

        $params = self::validate_parameters(self::execute_parameters(), [
            'tenantid' => $tenantid,
            'courseid' => $courseid,
            'fullname' => $fullname,
            'shortname' => $shortname,
            'visible' => $visible,
            'includeusers' => $includeusers,
        ]);

        $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $params['tenantid']], '*', MUST_EXIST);

        $tenantcontext = \context_tenant::instance($tenant->id);
        self::validate_context($tenantcontext);
        require_capability('tool/mutenancy:admin', $tenantcontext);

        $sourcecourse = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);

        $categorycontext = \context_coursecat::instance($tenant->categoryid);
        require_capability('moodle/course:create', $categorycontext);
        $coursecontext = \context_course::instance($sourcecourse->id);
        require_capability('moodle/backup:backupcourse', $coursecontext);
        require_capability('moodle/restore:restorecourse', $categorycontext);

        $newfullname = $params['fullname'] ?? ($sourcecourse->fullname . ' - ' . $tenant->name);

        $base = $params['shortname'] ?? ($sourcecourse->shortname . '_' . $tenant->idnumber ?? $tenant->id);
        $newshortname = $base;
        $suffix = 1;
        while ($DB->record_exists('course', ['shortname' => $newshortname])) {
            $suffix++;
            $newshortname = $base . '_' . $suffix;
        }

        $options = [];
        if ($params['includeusers']) {
            $options[] = ['name' => 'users', 'value' => 1];
            $options[] = ['name' => 'enrolments', 'value' => \backup::ENROL_WITHUSERS];
        }

        $duplicated = \core_course_external::duplicate_course(
            $sourcecourse->id,
            $newfullname,
            $newshortname,
            $tenant->categoryid,
            (int)$params['visible'],
            $options
        );

        $newcourseid = $duplicated['id'];
        $newcoursecontext = \context_course::instance($newcourseid);
        $DB->set_field('context', 'tenantid', $tenant->id, ['id' => $newcoursecontext->id]);
        $DB->execute(
            "UPDATE {context} SET tenantid = :tenantid WHERE path LIKE :path",
            [
                'tenantid' => $tenant->id,
                'path' => $newcoursecontext->path . '/%',
            ]
        );
        $newcoursecontext->mark_dirty();

        return [
            'id' => $duplicated['id'],
            'shortname' => $duplicated['shortname'],
            'fullname' => $duplicated['fullname'],
            'visible' => (bool)$duplicated['visible'],
            'categoryid' => $duplicated['categoryid'],
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'New course ID'),
            'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
            'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
            'visible' => new external_value(PARAM_BOOL, 'Course visibility'),
            'categoryid' => new external_value(PARAM_INT, 'Category ID'),
        ]);
    }
}

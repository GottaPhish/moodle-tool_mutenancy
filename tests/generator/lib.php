<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenant;
use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy test data generator.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tool_mutenancy_generator extends component_generator_base {
    /** @var int tenant count */
    private $tenantcount = 0;

    #[\Override]
    public function reset() {
        $this->tenantcount = 0;
    }

    /**
     * Create new tenant.
     *
     * NOTE: multi-tenancy is activated automatically.
     *
     * @param stdClass|array|null $record
     * @return stdClass tenant record
     */
    public function create_tenant($record = null): stdClass {
        global $DB;

        $this->tenantcount++;

        if (!tenancy::is_active()) {
            tenancy::activate();
        }

        $record = (object)(array)$record;

        if (!isset($record->name)) {
            $record->name = 'Tenant ' . $this->tenantcount;
        }

        if (!isset($record->idnumber)) {
            $record->idnumber = 'ten' . $this->tenantcount;
        }

        if (!empty($record->assoccohort)) {
            $cohort = $DB->get_record('cohort', ['idnumber' => $record->assoccohort], '*', MUST_EXIST);
            $record->assoccohortid = $cohort->id;
        }
        unset($record->assoccohort);

        if (!empty($record->category)) {
            $category = $DB->get_record('course_categories', ['idnumber' => $record->category], '*', MUST_EXIST);
            $record->categoryid = $category->id;
        }

        return tenant::create($record);
    }

    /**
     * Add new tenant manager.
     *
     * @param stdClass|array $record userid and tenantid are required
     * @return void
     */
    public function create_tenant_manager($record = null): void {
        $record = (object)(array)$record;

        if (!\tool_mutenancy\local\manager::add($record->tenantid, $record->userid)) {
            throw new \core\exception\invalid_parameter_exception('cannot add manager');
        }
    }
}

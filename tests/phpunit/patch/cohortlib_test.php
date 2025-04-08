<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong
// phpcs:disable moodle.Commenting.DocblockDescription.Missing

namespace tool_mutenancy\phpunit\patch;

/**
 * Multi-tenancy tests for cohort/lib.php modifications.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cohortlib_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers ::cohort_delete_cohort()
     */
    public function test_cohort_delete_cohort(): void {
        global $DB;

        /** @var \tool_mutenancy_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_mutenancy');

        $tenant1 = $generator->create_tenant();
        $this->assertTrue($DB->record_exists('cohort', ['id' => $tenant1->cohortid]));

        $cohort = $DB->get_record('cohort', ['id' => $tenant1->cohortid], '*', MUST_EXIST);
        cohort_delete_cohort($cohort);
        $this->assertTrue($DB->record_exists('cohort', ['id' => $tenant1->cohortid]));

        $tenant1 = \tool_mutenancy\local\tenant::archive($tenant1->id);
        \tool_mutenancy\local\tenant::delete($tenant1->id);
        $this->assertTrue($DB->record_exists('cohort', ['id' => $tenant1->cohortid]));

        cohort_delete_cohort($cohort);
        $this->assertFalse($DB->record_exists('cohort', ['id' => $tenant1->cohortid]));
    }
}

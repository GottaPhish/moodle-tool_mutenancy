<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong
// phpcs:disable moodle.Commenting.DocblockDescription.Missing

namespace tool_mutenancy\phpunit\patch;

/**
 * Multi-tenancy upstream patch test.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \tool_dataprivacy\api
 */
final class tool_dataprivacy_api_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers ::get_course_contexts_for_view_filter
     */
    public function test_get_course_contexts_for_view_filter(): void {
        \tool_dataprivacy\api::get_course_contexts_for_view_filter(-1);
    }
}

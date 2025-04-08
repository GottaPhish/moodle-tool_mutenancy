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
 * @coversDefaultClass \core_contentbank\contentbank
 */
final class core_contentbank_contentbank_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * @covers ::get_contexts_with_capabilities_by_user
     */
    public function test_get_contexts_with_capabilities_by_user(): void {
        global $CFG;
        $code = file_get_contents("$CFG->dirroot/contentbank/classes/contentbank.php");
        $this->assertStringContainsString('get_preload_record_columns', $code);
    }
}

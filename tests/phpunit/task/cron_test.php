<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\phpunit\local;

use tool_mutenancy\task\cron;
use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy cron tests.
 *
 * @group       muTMS
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @coversDefaultClass \tool_mutenancy\task\cron
 */
final class cron_test extends \advanced_testcase {
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * @covers ::execute
     */
    public function test_execute(): void {
        if (tenancy::is_active()) {
            tenancy::deactivate();
        }

        $cron = new cron();
        $cron->execute();

        tenancy::activate();

        ob_start();
        $cron = new cron();
        $cron->execute();
        $output = ob_get_clean();

        $this->assertStringContainsString('user::sync', $output);
        $this->assertStringContainsString('manager::sync', $output);
    }
}

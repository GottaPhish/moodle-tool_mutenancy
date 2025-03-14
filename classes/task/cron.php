<?php
// This file is part of Certifications plugin for Moodle™.

namespace tool_mutenancy\task;

/**
 * Multi-tenancy cron task.
 *
 * @package    tool_mutenancy
 * @copyright  2025 Petr Skoda
 * @author     Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskcron', 'tool_mutenancy');
    }

    /**
     * Run task for all program cron stuff.
     */
    public function execute() {
        if (!\tool_mutenancy\local\tenancy::is_active()) {
            return;
        }

        $trace = new \text_progress_trace();

        $trace->output('user::sync');
        \tool_mutenancy\local\user::sync(null, null);

        $trace->output('manager::sync');
        \tool_mutenancy\local\manager::sync();

        $trace->finished();
    }
}

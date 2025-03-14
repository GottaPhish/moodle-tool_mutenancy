<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\output\tenant_users;

/**
 * Tenant users renderer.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class renderer extends \tool_mutenancy\output\tenant_renderer_base {
    #[\Override]
    public function render_section(\stdClass $tenant): string {
        $context = \context_tenant::instance($tenant->id);
        $report = \core_reportbuilder\system_report_factory::create(
            \tool_mutenancy\reportbuilder\local\systemreports\users::class,
            $context);
        return $report->output();
    }
}

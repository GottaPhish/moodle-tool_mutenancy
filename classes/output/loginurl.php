<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

namespace tool_mutenancy\output;

/**
 * Tenant login URL.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class loginurl implements \renderable, \core\output\named_templatable {
    /** @var int */
    protected $tenantid;

    /**
     * Constructor.
     *
     * @param int $tenantid must be a non-archived tenant id
     */
    public function __construct(int $tenantid) {
        $this->tenantid = $tenantid;
    }

    /**
     * Export data for template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $url = \tool_mutenancy\local\tenant::get_login_url($this->tenantid);
        if ($url) {
            return ['url' => $url->out(false)];
        } else {
            return [];
        }
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'tool_mutenancy/loginurl';
    }
}

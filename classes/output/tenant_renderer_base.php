<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

namespace tool_mutenancy\output;

/**
 * Tenant renderer.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tenant_renderer_base extends \plugin_renderer_base {
    /**
     * Set up page.
     *
     * @param \stdClass $tenant
     * @return void
     */
    public function setup_page(\stdClass $tenant): void {
        $syscontext = \context_system::instance();
        if (has_capability('tool/mutenancy:view', $syscontext)) {
            $url = new \moodle_url('/admin/tool/mutenancy/index.php');
            $this->page->navbar->add(get_string('tenants', 'tool_mutenancy'), $url);
        }
        $this->page->navbar->add(format_string($tenant->name), $this->page->url);

        $this->page->set_heading($tenant->name);

        $secondarynav = new \tool_mutenancy\navigation\views\tenant_secondary($this->page);
        $secondarynav->initialise();
        $this->page->set_secondarynav($secondarynav);
    }

    /**
     * Render section details.
     *
     * @param \stdClass $tenant
     * @return string
     */
    abstract public function render_section(\stdClass $tenant): string;
}

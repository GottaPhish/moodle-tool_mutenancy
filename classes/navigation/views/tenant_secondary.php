<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\navigation\views;

/**
 * Tenant page secondary menu.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tenant_secondary extends \core\navigation\views\secondary {
    /**
     * Init secondary menu.
     */
    public function initialise(): void {
        global $DB;

        $this->id = 'secondary_navigation';
        $context = $this->context;
        $this->headertitle = get_string('menu');

        $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $context->instanceid], '*', MUST_EXIST);

        $url = new \moodle_url('/admin/tool/mutenancy/tenant.php', ['id' => $context->instanceid]);
        $this->add(get_string('secondary_tenant_details', 'tool_mutenancy'), $url, \navigation_node::TYPE_SETTING, null, 'tenant_details');

        $url = new \moodle_url('/admin/tool/mutenancy/tenant_users.php', ['id' => $context->instanceid]);
        $this->add(get_string('secondary_tenant_users', 'tool_mutenancy'), $url, \navigation_node::TYPE_SETTING, null, 'tenant_users');

        $url = new \moodle_url('/admin/tool/mutenancy/tenant_auth.php', ['id' => $context->instanceid]);
        $this->add(get_string('secondary_tenant_auth', 'tool_mutenancy'), $url, \navigation_node::TYPE_SETTING, null, 'tenant_auth');

        $url = new \moodle_url('/admin/tool/mutenancy/tenant_appearance.php', ['id' => $context->instanceid]);
        $this->add(get_string('secondary_tenant_appearance', 'tool_mutenancy'), $url, \navigation_node::TYPE_SETTING, null, 'tenant_appearance');

        $this->scan_for_active_node($this);
        $this->initialised = true;
    }
}

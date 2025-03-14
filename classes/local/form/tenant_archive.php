<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

/**
 * Archive tenant form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenant_archive extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];

        $info = '<div class="alert alert-warning">' . markdown_to_html(get_string('tenant_archive_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $mform->addElement('static', 'statictenant', get_string('tenant', 'tool_mutenancy'), format_string($tenant->name));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $tenant->id);

        $this->add_action_buttons(true, get_string('tenant_archive', 'tool_mutenancy'));
    }
}

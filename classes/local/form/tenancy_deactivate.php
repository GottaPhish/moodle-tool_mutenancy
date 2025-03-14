<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

/**
 * Multi-tenancy deactivation form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenancy_deactivate extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('tenancy_deactivate_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $this->add_action_buttons(true, get_string('tenancy_deactivate', 'tool_mutenancy'));
    }
}

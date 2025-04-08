<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong

namespace tool_mutenancy\local\form;

/**
 * Restore tenant form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenant_restore extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('tenant_restore_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $mform->addElement('static', 'statictenant', get_string('tenant', 'tool_mutenancy'), format_string($tenant->name));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $tenant->id);

        $this->add_action_buttons(true, get_string('tenant_restore', 'tool_mutenancy'));
    }
}

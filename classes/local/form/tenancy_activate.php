<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong

namespace tool_mutenancy\local\form;

/**
 * Multi-tenancy activation form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenancy_activate extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('tenancy_activate_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $this->add_action_buttons(true, get_string('tenancy_activate', 'tool_mutenancy'));
    }
}

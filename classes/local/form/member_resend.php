<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

/**
 * Member confirmation email resending form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class member_resend extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $user = $this->_customdata['user'];

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('member_resend_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $mform->addElement('static', 'fullname', get_string('user'), fullname($user));
        $mform->addElement('static', 'email', get_string('email'), s($user->email));
        $mform->addElement('static', 'username', get_string('username'), s($user->username));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $user->id);

        $this->add_action_buttons(true, get_string('resendemail', 'core'));
    }
}

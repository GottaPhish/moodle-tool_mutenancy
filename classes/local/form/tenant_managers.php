<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon
// phpcs:disable moodle.Files.LineLength.TooLong

namespace tool_mutenancy\local\form;

use tool_mutenancy\external\form_tenant_managers_userids;

/**
 * Tenant managers form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenant_managers extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];
        $userids = $this->_customdata['userids'];

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('member_managers_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        form_tenant_managers_userids::add_form_element(
            $mform, ['tenantid' => $tenant->id], 'userids', get_string('tenant_managers', 'tool_mutenancy'));
        $mform->setDefault('userids', $userids);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $tenant->id);

        $this->add_action_buttons(true, get_string('update'));
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $tenant = $this->_customdata['tenant'];

        foreach ($data['userids'] as $userid) {
            $error = form_tenant_managers_userids::validate_userid($userid, $tenant->id);
            if ($error !== null) {
                $errors['userids'] = $error;
                break;
            }
        }

        return $errors;
    }
}

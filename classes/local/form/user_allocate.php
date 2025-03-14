<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

use tool_mutenancy\external\form_user_allocate_tenantid;
/**
 * User allocation form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class user_allocate extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $user = $this->_customdata['user'];

        $info = '<div class="alert alert-warning">' . markdown_to_html(get_string('user_allocate_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        form_user_allocate_tenantid::add_form_element(
            $mform, ['tenantid' => (int)$user->tenantid], 'tenantid', get_string('tenant', 'tool_mutenancy'));
        $mform->setType('tenantid', PARAM_INT);
        if ($user->tenantid) {
            $mform->setDefault('tenantid', $user->tenantid);
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $user->id);

        $this->add_action_buttons(true, get_string('user_allocate', 'tool_mutenancy'));
    }

    #[\Override]
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        $user = $this->_customdata['user'];

        if ($data['tenantid']) {
            $tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $data['tenantid']]);
            if (!$tenant) {
                $errors['tenantid'] = get_string('error');
            } else if ($tenant->id == $user->tenantid) {
                $errors['tenantid'] = get_string('error:changerequired', 'tool_mutenancy');
            } else if ($tenant->memberlimit) {
                $count = $DB->count_records('user', ['tenantid' => $tenant->id, 'deleted' => 0]);
                if ($count >= $tenant->memberlimit) {
                    $errors['tenantid'] = get_string('error:memberlimitreached', 'tool_mutenancy');
                }
            }
        } else {
            if (!$user->tenantid) {
                $errors['tenantid'] = get_string('required');
            }
        }

        return $errors;
    }
}

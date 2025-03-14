<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

use tool_mutenancy\external\form_associate_add_userids;

/**
 * Associate users form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class associate_add extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        global $DB;

        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];
        $cohort = $this->_customdata['cohort'];

        $info = '<div class="alert alert-info">' . markdown_to_html(get_string('associate_add_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $tenants = $DB->get_records_menu('tool_mutenancy_tenant', ['assoccohortid' => $cohort->id], 'name ASC', 'id, name');
        $tenants =array_map('format_string', $tenants);
        $mform->addElement('static', 'tenants',
            (count($tenants) > 1) ? get_string('tenants', 'tool_mutenancy') : get_string('tenant', 'tool_mutenancy'),
            implode(', ', $tenants)
        );

        $mform->addElement('static', 'cohortname', get_string('associate_cohort', 'tool_mutenancy'), format_string($cohort->name));

        form_associate_add_userids::add_form_element(
            $mform, ['tenantid' => $tenant->id], 'userids', get_string('users'));
        $mform->addRule('userids', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'tenantid');
        $mform->setType('tenantid', PARAM_INT);
        $mform->setConstant('tenantid', $tenant->id);

        $this->add_action_buttons(true, get_string('associate_add', 'tool_mutenancy'));
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $tenant = $this->_customdata['tenant'];

        foreach ($data['userids'] as $userid) {
            $error = form_associate_add_userids::validate_userid($userid, $tenant->id);
            if ($error !== null) {
                $errors['userids'] = $error;
                break;
            }
        }

        return $errors;
    }
}

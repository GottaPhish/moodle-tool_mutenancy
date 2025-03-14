<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

use tool_mutenancy\external\form_associate_remove_userids;

/**
 * Disassociate user form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class associate_remove extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        global $DB;

        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];
        $cohort = $this->_customdata['cohort'];
        $user = $this->_customdata['user'];

        $info = '<div class="alert alert-warning">' . markdown_to_html(get_string('associate_remove_info', 'tool_mutenancy')) . '</div>';
        $mform->addElement('html', $info);

        $tenants = $DB->get_records_menu('tool_mutenancy_tenant', ['assoccohortid' => $cohort->id], 'name ASC', 'id, name');
        $tenants =array_map('format_string', $tenants);
        $mform->addElement('static', 'tenants',
            (count($tenants) > 1) ? get_string('tenants', 'tool_mutenancy') : get_string('tenant', 'tool_mutenancy'),
            implode(', ', $tenants)
        );

        $mform->addElement('static', 'cohortname', get_string('associate_cohort', 'tool_mutenancy'), format_string($cohort->name));

        $mform->addElement('static', 'fullname', get_string('user'), fullname($user));

        $mform->addElement('hidden', 'tenantid');
        $mform->setType('tenantid', PARAM_INT);
        $mform->setConstant('tenantid', $tenant->id);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setConstant('id', $user->id);

        $this->add_action_buttons(true, get_string('associate_remove', 'tool_mutenancy'));
    }
}

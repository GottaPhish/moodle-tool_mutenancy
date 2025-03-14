<?php
// This file is part of Multi-tenancy plugin for Moodle™.

namespace tool_mutenancy\local\form;

use tool_mutenancy\external\form_tenant_assoccohortid;
use tool_mutenancy\local\tenant;

/**
 * Update tenant form.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class tenant_update extends \tool_mulib\local\dialog_form {
    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $tenant = $this->_customdata['tenant'];

        $mform->addElement('text', 'name', get_string('tenant_name', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'idnumber', get_string('tenant_idnumber', 'tool_mutenancy'), ['maxlength' => 50]);
        $mform->setType('idnumber', PARAM_RAW);
        $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');

        $mform->addElement('advcheckbox', 'loginshow', get_string('tenant_loginshow', 'tool_mutenancy'));

        $mform->addElement('text', 'memberlimit', get_string('tenant_memberlimit', 'tool_mutenancy'), ['size' => 5]);
        $mform->setType('memberlimit', PARAM_INT);

        form_tenant_assoccohortid::add_form_element(
            $mform, ['tenantid' => $tenant->id], 'assoccohortid', get_string('associate_cohort', 'tool_mutenancy'));
        $mform->setType('assoccohortid', PARAM_INT);

        $mform->addElement('text', 'sitefullname', get_string('tenant_sitefullname', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('sitefullname', PARAM_TEXT);

        $mform->addElement('text', 'siteshortname', get_string('tenant_siteshortname', 'tool_mutenancy'), ['maxlength' => 255]);
        $mform->setType('siteshortname', PARAM_TEXT);

        $mform->addElement('text', 'categoryname', get_string('tenant_categoryname', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('categoryname', PARAM_TEXT);
        $mform->addRule('categoryname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'categoryidnumber', get_string('tenant_categoryidnumber', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('categoryidnumber', PARAM_TEXT);

        $mform->addElement('text', 'cohortname', get_string('tenant_cohortname', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('cohortname', PARAM_TEXT);
        $mform->addRule('cohortname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'cohortidnumber', get_string('tenant_cohortidnumber', 'tool_mutenancy'), ['size' => 40, 'maxlength' => 255]);
        $mform->setType('cohortidnumber', PARAM_TEXT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('tenant_update', 'tool_mutenancy'));
        $this->set_data($tenant);
    }

    #[\Override]
    public function validation($data, $files): array {
        global $DB;
        $errors = parent::validation($data, $files);
        $tenant = $this->_customdata['tenant'];

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('required');
        }

        if (trim($data['idnumber']) === '') {
            $errors['idnumber'] = get_string('required');
        } else if (!preg_match(tenant::IDNUMBER_REGEX, $data['idnumber'])) {
            $errors['idnumber'] = get_string('error');
        } else if ($DB->record_exists_select('tool_mutenancy_tenant', 'LOWER(idnumber) = LOWER(?) AND id <> ?', [$data['idnumber'], $tenant->id])) {
            $errors['idnumber'] = get_string('duplicate');
        }

        if (trim($data['categoryidnumber']) !== '') {
            if ($DB->record_exists_select('course_categories', 'LOWER(idnumber) = LOWER(?) AND id <> ?', [$data['categoryidnumber'], $tenant->categoryid])) {
                $errors['categoryidnumber'] = get_string('duplicate');
            }
        }

        if (trim($data['cohortidnumber']) !== '') {
            if ($DB->record_exists_select('cohort', 'LOWER(idnumber) = LOWER(?)  AND id <> ?', [$data['cohortidnumber'], $tenant->cohortid])) {
                $errors['cohortidnumber'] = get_string('duplicate');
            }
        }

        if ($data['assoccohortid']) {
            $error = form_tenant_assoccohortid::validate_cohortid($data['assoccohortid'], 0);
            if ($error !== null) {
                $errors['assoccohortid'] = $error;
            }
        }

        return $errors;
    }
}

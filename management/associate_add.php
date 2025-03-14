<?php
// This file is part of Multi-tenancy plugin for Moodle™.

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\manager;

/**
 * Associate users with tenant.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_database $DB */
/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */
/** @var stdClass $CFG */

if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}

require(__DIR__.'/../../../../config.php');
require_once($CFG->dirroot . '/cohort/lib.php');

$tenantid = required_param('tenantid', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);

$context = context_tenant::instance($tenant->id);
require_capability('tool/mutenancy:view', $context);

if (!$tenant->assoccohortid) {
    throw new \core\exception\invalid_parameter_exception('tenant does not have associated cohort');
}
$cohort = $DB->get_record('cohort', ['id' => $tenant->assoccohortid], '*', MUST_EXIST);
if ($cohort->component) {
    throw new \core\exception\invalid_parameter_exception('Associate cohort cannot belong to any component');
}
$cohortcontext = \context::instance_by_id($cohort->contextid);
require_capability('moodle/cohort:assign', $cohortcontext);

$PAGE->set_url('/admin/tool/mutenancy/management/associate_add.php', ['tenantid' => $tenant->id]);
$PAGE->set_context($context);

$returnurl = new moodle_url('/admin/tool/mutenancy/tenant_users.php', ['id' => $tenant->id]);

$form = new \tool_mutenancy\local\form\associate_add(null, ['tenant' => $tenant, 'cohort' => $cohort]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    foreach ($data->userids as $userid) {
        cohort_add_member($cohort->id, $userid);
    }
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('associate_add', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

<?php
// This file is part of Multi-tenancy plugin for Moodle™.

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\tenant;

/**
 * Update tenant.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_database $DB */
/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */

if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}

require(__DIR__.'/../../../../config.php');

$tenantid = required_param('id', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);

$context = context_system::instance();
require_capability('tool/mutenancy:admin', $context);

$PAGE->set_url('/admin/tool/mutenancy/management/tenant_update.php', ['id' => $tenant->id]);
$PAGE->set_context($context);

$returnurl = new moodle_url('/admin/tool/mutenancy/tenant.php', ['id' => $tenant->id]);

$category = $DB->get_record('course_categories', ['id' => $tenant->categoryid]);
if ($category) {
    $tenant->categoryname = $category->name;
    $tenant->categoryidnumber = $category->idnumber;
}
$cohort = $DB->get_record('cohort', ['id' => $tenant->cohortid]);
if ($cohort) {
    $tenant->cohortname = $cohort->name;
    $tenant->cohortidnumber = $cohort->idnumber;
}

$form = new \tool_mutenancy\local\form\tenant_update(null, ['tenant' => $tenant]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $tenant = tenant::update($data);
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenant_update', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\tenant;

/**
 * Create a new tenant.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */
/** @var moodle_database $DB */

// phpcs:ignoreFile moodle.Files.MoodleInternal.MoodleInternalGlobalState
if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}
require(__DIR__.'/../../../../config.php');

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$context = context_system::instance();
require_capability('tool/mutenancy:admin', $context);

$PAGE->set_url('/admin/tool/mutenancy/management/tenant_create.php');
$PAGE->set_context($context);

$returnurl = new moodle_url('/admin/tool/mutenancy/index.php');

$tenantlimit = get_config('tool_mutenancy', 'tenantlimit');
if ($tenantlimit && $tenantlimit <= $DB->count_records('tool_mutenancy_tenant', [])) {
    redirect($returnurl);
}

$form = new \tool_mutenancy\local\form\tenant_create();

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $tenant = tenant::create($data);
    $returnurl = new moodle_url('/admin/tool/mutenancy/tenant.php', ['id' => $tenant->id]);
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenant_create', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

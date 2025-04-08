<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\tenant;

/**
 * Switch tenant.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_database $DB */
/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */

// phpcs:ignoreFile moodle.Files.MoodleInternal.MoodleInternalGlobalState
if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}
require(__DIR__.'/../../../config.php');

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$context = context_system::instance();
require_capability('tool/mutenancy:switch', $context);

if (!tenancy::can_switch()) {
    redirect('/');
}

$PAGE->set_url('/admin/tool/mutenancy/tenant_switch.php');
$PAGE->set_context($context);

$returnurl = new moodle_url('/');

$form = new \tool_mutenancy\local\form\tenant_switch(null, []);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    tenancy::switch($data->tenantid);
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenant_switch', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;

/**
 * Multi-tenancy activation.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */

// phpcs:ignoreFile moodle.Files.MoodleInternal.MoodleInternalGlobalState
if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}
require(__DIR__.'/../../../../config.php');

require_login();
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$returnurl = new moodle_url('/admin/tool/mutenancy/index.php');

if (tenancy::is_active()) {
    redirect($returnurl);
}

$PAGE->set_url('/admin/tool/mutenancy/management/tenancy_activate.php');
$PAGE->set_context($syscontext);

$form = new \tool_mutenancy\local\form\tenancy_activate();

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    tenancy::activate();
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenancy_activate', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

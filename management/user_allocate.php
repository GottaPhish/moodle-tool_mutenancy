<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;

/**
 * Allocate users to tenants.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_database $DB */
/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */
/** @var stdClass $USER */

// phpcs:ignoreFile moodle.Files.MoodleInternal.MoodleInternalGlobalState
if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}
require(__DIR__.'/../../../../config.php');

$userid = required_param('id', PARAM_INT);

if (!tenancy::is_active()) {
    redirect('/admin/tool/user.php');
}

$syscontext = context_system::instance();
require_login();
require_capability('tool/mutenancy:allocate', $syscontext);

$user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

$PAGE->set_url('/admin/tool/mutenancy/management/user_allocate.php', ['id' => $user->id]);
$PAGE->set_context($syscontext);

if (isguestuser($user) || is_siteadmin($user)) {
    throw new \core\exception\invalid_parameter_exception('guests and admins cannot be allocated');
}
if ($USER->id == $user->id) {
    throw new \core\exception\invalid_parameter_exception('cannot allocate own account');
}

$form = new \tool_mutenancy\local\form\user_allocate(null, ['user' => $user]);

if ($form->is_cancelled()) {
    if ($user->tenantid) {
        $returnurl = new moodle_url('/admin/tool/mutenancy/tenant_members', ['id' => $user->tenantid]);
    } else {
        $returnurl = new moodle_url('/admin/tool/user.php');
    }
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $user = \tool_mutenancy\local\user::allocate($user->id, (int)$data->tenantid);

    if ($user->tenantid) {
        $returnurl = new moodle_url('/admin/tool/mutenancy/tenant_members', ['id' => $user->tenantid]);
    } else {
        $returnurl = new moodle_url('/admin/tool/user.php');
    }
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('user_allocate', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

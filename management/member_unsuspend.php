<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;

/**
 * Unsuspend tenant member account.
 *
 * See original code in user/editadvanced.php file.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_page $PAGE */
/** @var core_renderer $OUTPUT */
/** @var moodle_database $DB */
/** @var stdClass $USER */
/** @var stdClass $CFG */

// phpcs:ignoreFile moodle.Files.MoodleInternal.MoodleInternalGlobalState
if (!empty($_SERVER['HTTP_X_MULIB_DIALOG_FORM_REQUEST'])) {
    define('AJAX_SCRIPT', true);
}
require(__DIR__.'/../../../../config.php');

$userid = required_param('id', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$personalcontext = context_user::instance($userid);
require_capability('tool/mutenancy:memberupdate', $personalcontext);

$PAGE->set_url('/admin/tool/mutenancy/management/member_suspend.php', ['id' => $userid]);
$PAGE->set_context($personalcontext);

$user = $DB->get_record('user', ['id' => $userid]);

if (!$user || $user->deleted || !$user->tenantid || isguestuser($user)
    || is_siteadmin($user) || $USER->id == $user->id || $user->mnethostid != $CFG->mnet_localhost_id
) {
    throw new moodle_exception('invaliduserid');
}

$returnurl = new moodle_url('/admin/tool/mutenancy/tenant_users.php', ['id' => $user->tenantid]);

if (!$user->suspended) {
    redirect($returnurl);
}

$form = new \tool_mutenancy\local\form\member_unsuspend(null, ['user' => $user]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    \tool_mutenancy\local\member::unsuspend($user->id);
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('unsuspenduser', 'admin'));

echo $form->render();

echo $OUTPUT->footer();

<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\tenant;

/**
 * Delete tenant.
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

$tenantid = required_param('id', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);

$context = context_system::instance();
require_capability('tool/mutenancy:admin', $context);

if ($DB->record_exists('user', ['tenantid' => $tenant->id, 'deleted' => 0])) {
    require_capability('tool/mutenancy:allocate', $context);
}

$PAGE->set_url('/admin/tool/mutenancy/management/tenant_delete.php', ['id' => $tenant->id]);
$PAGE->set_context($context);

$returnurl = new moodle_url('/admin/tool/mutenancy/tenant.php', ['id' => $tenant->id]);

if ($USER->tenantid == $tenant->id || !$tenant->archived) {
    redirect($returnurl);
}

$form = new \tool_mutenancy\local\form\tenant_delete(null, ['tenant' => $tenant]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    tenant::delete($tenant->id);
    $syscontext = context_system::instance();
    if (has_capability('tool/mutenancy:view', $syscontext)) {
        $returnurl = new moodle_url('/admin/tool/mutenancy/index.php', ['id' => $tenant->id]);
    } else {
        $returnurl = new moodle_url('/');
    }
    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('tenant_delete', 'tool_mutenancy'));

echo $form->render();

echo $OUTPUT->footer();

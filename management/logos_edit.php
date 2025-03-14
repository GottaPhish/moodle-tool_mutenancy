<?php
// This file is part of Multi-tenancy plugin for Moodle™.

use tool_mutenancy\local\tenancy;
use tool_mutenancy\local\config;

/**
 * Update tenant logos overrides.
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
require_once($CFG->libdir.'/filelib.php');

$tenantid = required_param('id', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/');
}

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);

$context = context_tenant::instance($tenant->id);
require_capability('tool/mutenancy:configappearance', $context);

$PAGE->set_url('/admin/tool/mutenancy/management/logos_edit.php', ['id' => $tenant->id]);
$PAGE->set_context($context);

$returnurl = new moodle_url('/admin/tool/tenant_appearance.php', ['id' => $tenant->id]);

$currentdata = (object)[
    'id' => $tenant->id,
    'logo' => file_get_submitted_draft_itemid('logo'),
    'logocompact' => file_get_submitted_draft_itemid('logocompact'),
    'favicon' => file_get_submitted_draft_itemid('favicon'),
];

$logooptions = \tool_mutenancy\local\form\logos_edit::get_logo_options();
$faviconoptions = \tool_mutenancy\local\form\logos_edit::get_favicon_options();

file_prepare_draft_area($currentdata->logo, $context->id, 'core_admin', 'logo', 0, $logooptions);
file_prepare_draft_area($currentdata->logocompact, $context->id, 'core_admin', 'logocompact', 0, $logooptions);
file_prepare_draft_area($currentdata->favicon, $context->id, 'core_admin', 'favicon', 0, $faviconoptions);

$form = new \tool_mutenancy\local\form\logos_edit(null, ['currentdata' => $currentdata, 'tenant' => $tenant]);

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $fs = get_file_storage();

    if (isset($data->logo_override)) {
        if ($data->logo_override) {
            file_save_draft_area_files($data->logo, $context->id, 'core_admin', 'logo', 0, $logooptions);
            $files = $fs->get_area_files($context->id, 'core_admin', 'logo', 0, 'id DESC', false);
            if ($files) {
                $file = reset($files);
                config::override($tenant->id, 'logo', '/' . $file->get_filename(), 'core_admin');
            } else {
                config::override($tenant->id, 'logo', '', 'core_admin');
            }
        } else {
            config::override($tenant->id, 'logo', null, 'core_admin');
            $fs->delete_area_files($context->id, 'core_admin', 'logo', 0);
        }
    }

    if (isset($data->logocompact_override)) {
        if ($data->logocompact_override) {
            file_save_draft_area_files($data->logocompact, $context->id, 'core_admin', 'logocompact', 0, $logooptions);
            $files = $fs->get_area_files($context->id, 'core_admin', 'logocompact', 0, 'id DESC', false);
            if ($files) {
                $file = reset($files);
                config::override($tenant->id, 'logocompact', '/' . $file->get_filename(), 'core_admin');
            } else {
                config::override($tenant->id, 'logocompact', '', 'core_admin');
            }
        } else {
            config::override($tenant->id, 'logocompact', null, 'core_admin');
            $fs->delete_area_files($context->id, 'core_admin', 'logocompact', 0);
        }
    }

    if (isset($data->favicon_override)) {
        if ($data->favicon_override) {
            file_save_draft_area_files($data->favicon, $context->id, 'core_admin', 'favicon', 0, $faviconoptions);
            $files = $fs->get_area_files($context->id, 'core_admin', 'favicon', 0, 'id DESC', false);
            if ($files) {
                $file = reset($files);
                config::override($tenant->id, 'favicon', '/' . $file->get_filename(), 'core_admin');
            } else {
                config::override($tenant->id, 'favicon', '', 'core_admin');
            }
        } else {
            config::override($tenant->id, 'favicon', null, 'core_admin');
            $fs->delete_area_files($context->id, 'core_admin', 'favicon', 0);
        }
    }

    if (config::is_overridden($tenant->id, 'core_admin', 'logo')
        || config::is_overridden($tenant->id, 'logocompact', 'core_admin')
        || config::is_overridden($tenant->id, 'favicon', 'core_admin')
    ) {
        theme_reset_all_caches();
    }

    $form->redirect_submitted($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('update'));

echo $form->render();

echo $OUTPUT->footer();

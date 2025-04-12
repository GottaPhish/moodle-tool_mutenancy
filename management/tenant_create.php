<?php
// This file is part of Multi-tenancy plugin for Moodle™.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

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

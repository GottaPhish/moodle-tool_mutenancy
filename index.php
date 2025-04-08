<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;

/**
 * List of all tenants.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var stdClass $CFG */
/** @var core_renderer $OUTPUT */
/** @var moodle_database $DB */
/** @var moodle_page $PAGE */

require(__DIR__ . '/../../../config.php');
require_once("$CFG->libdir/adminlib.php");

require_login();
admin_externalpage_setup('tool_mutenancy_tenants', '', null, '', ['nosearch' => true]);

$syscontext = context_system::instance();
$tenantcount = $DB->count_records('tool_mutenancy_tenant', []);

$PAGE->set_heading(get_string('tenants', 'tool_mutenancy'));
$PAGE->set_secondary_navigation(false);

if (!tenancy::is_active()) {
    echo $OUTPUT->header();
    $url = new moodle_url('/admin/tool/mutenancy/management/tenancy_activate.php');
    $button = new tool_mulib\output\dialog_form\button($url, get_string('tenancy_activate', 'tool_mutenancy'), true);
    $button->set_dialog_size('');
    echo '<div class="buttons">' . $OUTPUT->render($button) . '</div>';
    echo $OUTPUT->footer();
    die;
}

if (has_capability('tool/mutenancy:admin', $syscontext)) {
    $tenantlimit = get_config('tool_mutenancy', 'tenantlimit');
    $notenantsyet = !$DB->record_exists('tool_mutenancy_tenant', []);
    if (!$tenantlimit || $tenantlimit > $DB->count_records('tool_mutenancy_tenant', [])) {
        $url = new moodle_url('/admin/tool/mutenancy/management/tenant_create.php');
        $button = new tool_mulib\output\dialog_form\button($url, get_string('tenant_create', 'tool_mutenancy'), $notenantsyet);
        $button->set_after_submit($button::AFTER_SUBMIT_REDIRECT);
        $PAGE->add_header_action($OUTPUT->render($button));
    }
}

echo $OUTPUT->header();

$report = \core_reportbuilder\system_report_factory::create(
    \tool_mutenancy\reportbuilder\local\systemreports\tenants::class,
    context_system::instance());
echo $report->output();

$buttons = [];

if (!$tenantcount && has_capability('moodle/site:config', $syscontext)) {
    $url = new moodle_url('/admin/tool/mutenancy/management/tenancy_deactivate.php');
    $button = new tool_mulib\output\dialog_form\button($url, get_string('tenancy_deactivate', 'tool_mutenancy'));
    $button->set_dialog_size('');
    $buttons[] = $OUTPUT->render($button);
}

if ($buttons) {
    echo '<div class="buttons">' . implode(' ', $buttons) . '</div>';
}

echo $OUTPUT->footer();

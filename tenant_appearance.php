<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

use tool_mutenancy\local\tenancy;

/**
 * Tenant appearance settings.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var stdClass $CFG */
/** @var core_renderer $OUTPUT */
/** @var moodle_database $DB */
/** @var moodle_page $PAGE */

require(__DIR__.'/../../../config.php');
require_once("$CFG->libdir/adminlib.php");

$tenantid = required_param('id', PARAM_INT);

require_login();

if (!tenancy::is_active()) {
    redirect('/admin/tool/mutenancy/index.php');
}

$tenant = $DB->get_record('tool_mutenancy_tenant', ['id' => $tenantid], '*', MUST_EXIST);
$context = context_tenant::instance($tenant->id);
require_capability('tool/mutenancy:view', $context);

$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mutenancy/tenant_appearance.php', ['id' => $tenant->id]);

/** @var \tool_mutenancy\output\tenant_appearance\renderer $output */
$output = $PAGE->get_renderer('tool_mutenancy', 'tenant_appearance');

$output->setup_page($tenant);

echo $OUTPUT->header();

echo $output->render_section($tenant);

echo $OUTPUT->footer();

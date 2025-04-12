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
// phpcs:disable moodle.Files.LineLength.TooLong

namespace tool_mutenancy\output\tenant;

use tool_mutenancy\local\user;

/**
 * Tenant renderer.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class renderer extends \tool_mutenancy\output\tenant_renderer_base {
    #[\Override]
    public function render_section(\stdClass $tenant): string {
        global $DB, $USER;

        $result = '';

        $yesno = [
            0 => get_string('no'),
            1 => get_string('yes'),
        ];

        $result .= '<dl class="row">';
        $result .= '<dt class="col-3">' . get_string('tenant_name', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . format_string($tenant->name) . '</dd>';
        $result .= '<dt class="col-3">' . get_string('tenant_idnumber', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . s($tenant->idnumber) . '</dd>';

        if (!$tenant->archived) {
            $loginurl = new \tool_mutenancy\output\loginurl($tenant->id);
            $loginurl = $this->render($loginurl);
            $result .= '<dt class="col-3">' . get_string('tenant_loginurl', 'tool_mutenancy') . '</dt><dd class="col-9">'
                . $loginurl . '</dd>';
            $result .= '<dt class="col-3">' . get_string('tenant_loginshow', 'tool_mutenancy') . '</dt><dd class="col-9">'
                . $yesno[$tenant->loginshow] . '</dd>';
        }

        if ($tenant->memberlimit) {
            $count = user::count_members($tenant->id);
            $count = "$count / $tenant->memberlimit";
            $result .= '<dt class="col-3">' . get_string('tenant_memberlimit', 'tool_mutenancy') . '</dt><dd class="col-9">'
                . $count . '</dd>';
        }

        $context = \context_coursecat::instance($tenant->categoryid, IGNORE_MISSING);
        if ($context) {
            $url = null;
            if (has_capability('moodle/category:manage', $context)) {
                $url = new \moodle_url('/course/management.php', ['categoryid' => $tenant->categoryid]);
            } else if (has_capability('moodle/category:viewcourselist', $context)) {
                $url = new \moodle_url('/course/index.php', ['categoryid' => $tenant->categoryid]);
            }
            $name = $context->get_context_name(false);
            if ($url) {
                $name = \html_writer::link($url, $name);
            }
        } else {
            $name = get_string('error');
        }
        $result .= '<dt class="col-3">' . get_string('tenant_category', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . $name . '</dd>';

        $cohort = $DB->get_record('cohort', ['id' => $tenant->cohortid]);
        if ($cohort) {
            $context = \context::instance_by_id($cohort->contextid);
            $name = format_string($cohort->name);
            $url = null;
            if (has_capability('moodle/cohort:view', $context)) {
                $url = new \moodle_url('/cohort/index.php', ['contextid' => $context->id]);
            }
            if ($url) {
                $name = \html_writer::link($url, $name);
            }
        } else {
            $name = get_string('error');
        }
        $result .= '<dt class="col-3">' . get_string('tenant_cohort', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . $name . '</dd>';

        if ($tenant->assoccohortid) {
            $cohort = $DB->get_record('cohort', ['id' => $tenant->assoccohortid]);
            if ($cohort) {
                $context = \context::instance_by_id($cohort->contextid);
                $name = format_string($cohort->name);
                $url = null;
                if (has_capability('moodle/cohort:view', $context)) {
                    $url = new \moodle_url('/cohort/index.php', ['contextid' => $context->id]);
                }
                if ($url) {
                    $name = \html_writer::link($url, $name);
                }
            } else {
                $name = get_string('error');
            }
            $result .= '<dt class="col-3">' . get_string('associate_cohort', 'tool_mutenancy') . '</dt><dd class="col-9">'
                . $name . '</dd>';
        }

        $result .= '<dt class="col-3">' . get_string('tenant_sitefullname', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . format_string($tenant->sitefullname ?? $tenant->name) . '</dd>';
        $result .= '<dt class="col-3">' . get_string('tenant_siteshortname', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . format_string($tenant->siteshortname ?? $tenant->idnumber) . '</dd>';

        $count = user::count_users($tenant->id);
        $url = new \moodle_url('/admin/tool/mutenancy/tenant_users.php', ['id' => $tenant->id]);
        $count = \html_writer::link($url, $count);
        $result .= '<dt class="col-3">' . get_string('tenant_users', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . $count . '</dd>';

        $managers = \tool_mutenancy\local\manager::get_manager_users($tenant->id);
        foreach ($managers as $uid => $fullname) {
            $url = new \moodle_url('/user/profile.php', ['id' => $uid]);
            $managers[$uid] = \html_writer::link($url, $fullname);
        }
        $managers = implode(', ', $managers);
        if ($managers === '') {
            $managers = '&nbsp;';
        }
        $action = '';
        $context = \context_tenant::instance($tenant->id);
        if (has_capability('tool/mutenancy:admin', $context)) {
            $url = new \moodle_url('/admin/tool/mutenancy/management/tenant_managers.php', ['id' => $tenant->id]);
            $action = new \tool_mulib\output\dialog_form\icon($url, get_string('tenant_managers', 'tool_mutenancy'), 'i/users');
            $action->set_dialog_size('');
            $action = $this->render($action);
        }
        $result .= '<dt class="col-3">' . get_string('tenant_managers', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . $managers . $action . '</dd>';

        $action = '';
        if (has_capability('tool/mutenancy:admin', $context)) {
            if ($tenant->archived) {
                $url = new \moodle_url('/admin/tool/mutenancy/management/tenant_restore.php', ['id' => $tenant->id]);
                $action = new \tool_mulib\output\dialog_form\icon($url, get_string('tenant_restore', 'tool_mutenancy'), 't/edit');
                $action->set_dialog_size('');
            } else if ($USER->tenantid != $tenant->id) {
                $url = new \moodle_url('/admin/tool/mutenancy/management/tenant_archive.php', ['id' => $tenant->id]);
                $action = new \tool_mulib\output\dialog_form\icon($url, get_string('tenant_archive', 'tool_mutenancy'), 'i/settings');
                $action->set_dialog_size('');
            }
            $action = $this->render($action);
        }

        $result .= '<dt class="col-3">' . get_string('tenant_archived', 'tool_mutenancy') . '</dt><dd class="col-9">'
            . $yesno[$tenant->archived] . $action . '</dd>';
        $result .= '</dl>';

        return $result;
    }
}

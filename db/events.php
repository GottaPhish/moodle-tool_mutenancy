<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy event observers.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = [
    [
        'eventname' => \core\event\user_created::class,
        'callback' => \tool_mutenancy\local\member::class . '::user_created',
    ],
    [
        'eventname' => \core\event\user_deleted::class,
        'callback' => \tool_mutenancy\local\manager::class . '::user_deleted',
    ],
    [
        'eventname' => \core\event\cohort_member_added::class,
        'callback' => \tool_mutenancy\local\user::class . '::cohort_member_added',
    ],
    [
        'eventname' => \core\event\cohort_member_removed::class,
        'callback' => \tool_mutenancy\local\user::class . '::cohort_member_removed',
    ],
    [
        'eventname' => \core\event\cohort_deleted::class,
        'callback' => \tool_mutenancy\local\tenant::class . '::cohort_deleted',
    ],
];

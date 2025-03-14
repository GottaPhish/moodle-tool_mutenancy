<?php
// This file is part of Certifications plugin for Moodle™.

/**
 * Multi-tenancy tasks.
 *
 * @package    tool_mutenancy
 * @copyright  2025 Petr Skoda
 * @author     Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => \tool_mutenancy\task\cron::class,
        'minute' => 'R',
        'hour' => '10',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0
    ],
];

<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

/**
 * Multi-tenancy caches.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$definitions = [
    'config' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simpledata' => true,
        'invalidationevents' => [
            'tool_mutenancy_invalidatecaches',
        ],
        'staticacceleration' => true,
        'canuselocalstore' => false,
    ],

    'tenant' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simpledata' => true,
        'invalidationevents' => [
            'tool_mutenancy_invalidatecaches',
        ],
        'staticacceleration' => true,
        'canuselocalstore' => false,
    ],
];

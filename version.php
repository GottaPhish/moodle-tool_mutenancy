<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy plugin version.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2025040700;
$plugin->requires = 2024100703;
$plugin->component = 'tool_mutenancy';
$plugin->maturity = MATURITY_ALPHA;
$plugin->supported = [405, 405];
$plugin->release = 'mu-4.5.3-01';

$plugin->dependencies = [
    'tool_mulib' => 2025040700,
];

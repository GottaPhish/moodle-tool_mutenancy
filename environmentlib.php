<?php
// This file is part of Multi-tenancy plugin for Moodle™.

/**
 * Multi-tenancy upgrade environment tests.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check that correct version of multi-tenancy patch was applied.
 *
 * @param environment_results $result $result
 * @return environment_results updated results object
 */
function tool_mutenancy_environment_corepatch(environment_results $result): environment_results {
    $getrelease = function() {
        $plugin = new stdClass();
        require(__DIR__ . '/version.php');
        return $plugin->release;
    };
    $release = $getrelease();

    $result->setInfo("Core Multi-tenancy patch ($release is required)");

    $patchfile = __DIR__ . '/../../../patch/mutenancy.php';
    if (!file_exists($patchfile)) {
        $result->setStatus(false);
        return $result;
    }

    $patchinfo = require($patchfile);

    if ($patchinfo['release'] !== $getrelease()) {
        $result->setStatus(false);
        return $result;
    }

    $result->setStatus(true);
    return $result;
}

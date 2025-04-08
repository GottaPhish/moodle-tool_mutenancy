<?php
// This file is part of Multi-tenancy plugin for Moodle™.
// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

namespace tool_mutenancy\privacy;

use core_privacy\local\metadata\collection;

/**
 * Multi-tenancy privacy provider.
 *
 * @package     tool_mutenancy
 * @copyright   2025 Petr Skoda
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\null_provider,
    \core_privacy\local\metadata\provider {

    /**
     * Reason.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Returns data about this plugin.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_mutenancy_manager',
            [
                'tenantid' => 'privacy:metadata:tool_mutenancy_manager:tenantid',
                'userid' => 'privacy:metadata:tool_mutenancy_manager:userid',
                'usercreated' => 'privacy:metadata:tool_mutenancy_manager:usercreated',
                'timecreated' => 'privacy:metadata:tool_mutenancy_manager:timecreated',
            ],
            'privacy:metadata:tool_mutenancy_manager'
        );

        return $collection;
    }
}

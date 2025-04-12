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

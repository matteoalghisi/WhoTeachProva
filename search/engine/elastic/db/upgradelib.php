<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade lib for elasticsearch plugin.
 *
 * @package     search_elastic
 * @copyright   2020 Peter Burnett <peterburnett@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Updates the boosting settings to their new config names
 *
 * @return void
 */
function update_boosting_setting_names() {
    $configitems = get_config('search_elastic');
    foreach ($configitems as $item => $value) {
        if (strpos($item, 'boost_') === 0) {
            // Boost item found.
            $newname = str_replace('-', '_', $item);
            set_config($newname, $value, 'search_elastic');
            // Delete the original entry by setting null.
            set_config($item, null, 'search_elastic');
        }
    }
}

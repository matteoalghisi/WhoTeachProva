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
 * IMSCP export lib
 *
 * @package    recommendtool_exportimscp
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $node The node to add module settings to
 */
function recommendtool_exportimscp_extend_settings_navigation(settings_navigation $settings, navigation_node $node) {
    global $PAGE;

    if (has_capability('recommendtool/exportimscp:export', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/recommend/tool/exportimscp/index.php', array('id'=>$PAGE->cm->id));
        $icon = new pix_icon('generate', '', 'recommendtool_exportimscp', array('class'=>'icon'));
        $node->add(get_string('generateimscp', 'recommendtool_exportimscp'), $url, navigation_node::TYPE_SETTING, null, null, $icon);
    }
}

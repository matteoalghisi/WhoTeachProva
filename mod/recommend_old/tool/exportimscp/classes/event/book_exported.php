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
 * recommendtool_exportimscp recommend exported event.
 *
 * @package    recommendtool_exportimscp
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace recommendtool_exportimscp\event;
defined('MOODLE_INTERNAL') || die();

/**
 * recommendtool_exportimscp recommend exported event class.
 *
 * @package    recommendtool_exportimscp
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recommend_exported extends \core\event\base {
    /**
     * Create instance of event.
     *
     * @since Moodle 2.7
     *
     * @param \stdClass $recommend
     * @param \context_module $context
     * @return recommend_exported
     */
    public static function create_from_recommend(\stdClass $recommend, \context_module $context) {
        $data = array(
            'context' => $context,
            'objectid' => $recommend->id
        );
        /** @var recommend_exported $event */
        $event = self::create($data);
        $event->add_record_snapshot('recommend', $recommend);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' has exported the recommend with course module id '$this->contextinstanceid'.";
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'recommend', 'exportimscp', 'tool/exportimscp/index.php?id=' . $this->contextinstanceid,
            $this->objectid, $this->contextinstanceid);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventrecommendexported', 'recommendtool_exportimscp');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/recommend/tool/exportimscp/index.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'recommend';
    }

    public static function get_objectid_mapping() {
        return array('db' => 'recommend', 'restore' => 'recommend');
    }
}

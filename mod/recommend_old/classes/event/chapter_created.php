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
 * The mod_recommend chapter created event.
 *
 * @package    mod_recommend
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_recommend\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_recommend chapter created event class.
 *
 * @package    mod_recommend
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chapter_created extends \core\event\base {
    /**
     * Create instance of event.
     *
     * @since Moodle 2.7
     *
     * @param \stdClass $recommend
     * @param \context_module $context
     * @param \stdClass $chapter
     * @return chapter_created
     */
    public static function create_from_chapter(\stdClass $recommend, \context_module $context, \stdClass $chapter) {
        $data = array(
            'context' => $context,
            'objectid' => $chapter->id,
        );
        /** @var chapter_created $event */
        $event = self::create($data);
        $event->add_record_snapshot('recommend', $recommend);
        $event->add_record_snapshot('recommend_chapters', $chapter);
        return $event;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created the chapter with id '$this->objectid' for the recommend with " .
            "course module id '$this->contextinstanceid'.";
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'recommend', 'add chapter', 'view.php?id=' . $this->contextinstanceid . '&chapterid=' .
            $this->objectid, $this->objectid, $this->contextinstanceid);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventchaptercreated', 'mod_recommend');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/recommend/view.php', array(
            'id' => $this->contextinstanceid,
            'chapterid' => $this->objectid
        ));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'recommend_chapters';
    }

    public static function get_objectid_mapping() {
        return array('db' => 'recommend_chapters', 'restore' => 'recommend_chapter');
    }
}

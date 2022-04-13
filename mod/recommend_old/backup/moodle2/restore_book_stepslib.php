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
 * Define all the restore steps that will be used by the restore_recommend_activity_task
 *
 * @package    mod_recommend
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Structure step to restore one recommend activity
 */
class restore_recommend_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = array();

        $paths[] = new restore_path_element('recommend', '/activity/recommend');
        $paths[] = new restore_path_element('recommend_chapter', '/activity/recommend/chapters/chapter');
        $paths[] = new restore_path_element('recommend_chapter_tag', '/activity/recommend/chaptertags/tag');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process recommend tag information
     * @param array $data information
     */
    protected function process_recommend($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

        $newitemid = $DB->insert_record('recommend', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process chapter tag information
     * @param array $data information
     */
    protected function process_recommend_chapter($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->recommendid = $this->get_new_parentid('recommend');

        $newitemid = $DB->insert_record('recommend_chapters', $data);
        $this->set_mapping('recommend_chapter', $oldid, $newitemid, true);
    }

    protected function process_recommend_chapter_tag($data) {
        $data = (object)$data;

        if (!core_tag_tag::is_enabled('mod_recommend', 'recommend_chapters')) { // Tags disabled in server, nothing to process.
            return;
        }

        $tag = $data->rawname;

        if (!$itemid = $this->get_mappingid('recommend_chapter', $data->itemid)) {
            return;
        }

        $context = context_module::instance($this->task->get_moduleid());
        core_tag_tag::add_item_tag('mod_recommend', 'recommend_chapters', $itemid, $context, $tag);
    }

    protected function after_execute() {
        global $DB;

        // Add recommend related files
        $this->add_related_files('mod_recommend', 'intro', null);
        $this->add_related_files('mod_recommend', 'chapter', 'recommend_chapter');
    }
}

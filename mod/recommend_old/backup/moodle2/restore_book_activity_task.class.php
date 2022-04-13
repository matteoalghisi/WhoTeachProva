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
 * Description of recommend restore task
 *
 * @package    mod_recommend
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/recommend/backup/moodle2/restore_recommend_stepslib.php'); // Because it exists (must)

class restore_recommend_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     *
     * @return void
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_recommend_activity_structure_step('recommend_structure', 'recommend.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     *
     * @return array
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('recommend', array('intro'), 'recommend');
        $contents[] = new restore_decode_content('recommend_chapters', array('content'), 'recommend_chapter');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     *
     * @return array
     */
    static public function define_decode_rules() {
        $rules = array();

        // List of recommends in course
        $rules[] = new restore_decode_rule('recommendINDEX', '/mod/recommend/index.php?id=$1', 'course');

        // recommend by cm->id
        $rules[] = new restore_decode_rule('recommendVIEWBYID', '/mod/recommend/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('recommendVIEWBYIDCH', '/mod/recommend/view.php?id=$1&amp;chapterid=$2', array('course_module', 'recommend_chapter'));

        // recommend by recommend->id
        $rules[] = new restore_decode_rule('recommendVIEWBYB', '/mod/recommend/view.php?b=$1', 'recommend');
        $rules[] = new restore_decode_rule('recommendVIEWBYBCH', '/mod/recommend/view.php?b=$1&amp;chapterid=$2', array('recommend', 'recommend_chapter'));

        // Convert old recommend links MDL-33362 & MDL-35007
        $rules[] = new restore_decode_rule('recommendSTART', '/mod/recommend/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('recommendCHAPTER', '/mod/recommend/view.php?id=$1&amp;chapterid=$2', array('course_module', 'recommend_chapter'));

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * recommend logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * @return array
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('recommend', 'add', 'view.php?id={course_module}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'update', 'view.php?id={course_module}&chapterid={recommend_chapter}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'update', 'view.php?id={course_module}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'view', 'view.php?id={course_module}&chapterid={recommend_chapter}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'view', 'view.php?id={course_module}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'print', 'tool/print/index.php?id={course_module}&chapterid={recommend_chapter}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'print', 'tool/print/index.php?id={course_module}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'exportimscp', 'tool/exportimscp/index.php?id={course_module}', '{recommend}');
        // To convert old 'generateimscp' log entries
        $rules[] = new restore_log_rule('recommend', 'generateimscp', 'tool/generateimscp/index.php?id={course_module}', '{recommend}',
                'recommend', 'exportimscp', 'tool/exportimscp/index.php?id={course_module}', '{recommend}');
        $rules[] = new restore_log_rule('recommend', 'print chapter', 'tool/print/index.php?id={course_module}&chapterid={recommend_chapter}', '{recommend_chapter}');
        $rules[] = new restore_log_rule('recommend', 'update chapter', 'view.php?id={course_module}&chapterid={recommend_chapter}', '{recommend_chapter}');
        $rules[] = new restore_log_rule('recommend', 'add chapter', 'view.php?id={course_module}&chapterid={recommend_chapter}', '{recommend_chapter}');
        $rules[] = new restore_log_rule('recommend', 'view chapter', 'view.php?id={course_module}&chapterid={recommend_chapter}', '{recommend_chapter}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     *
     * @return array
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('recommend', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}

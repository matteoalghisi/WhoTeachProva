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
 * recommend external API
 *
 * @package    mod_recommend
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * recommend external functions
 *
 * @package    mod_recommend
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_recommend_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_recommend_parameters() {
        return new external_function_parameters(
            array(
                'recommendid' => new external_value(PARAM_INT, 'recommend instance id'),
                'chapterid' => new external_value(PARAM_INT, 'chapter id', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Simulate the recommend/view.php web interface page: trigger events, completion, etc...
     *
     * @param int $recommendid the recommend instance id
     * @param int $chapterid the recommend chapter id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_recommend($recommendid, $chapterid = 0) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/recommend/lib.php");
        require_once($CFG->dirroot . "/mod/recommend/locallib.php");

        $params = self::validate_parameters(self::view_recommend_parameters(),
                                            array(
                                                'recommendid' => $recommendid,
                                                'chapterid' => $chapterid
                                            ));
        $recommendid = $params['recommendid'];
        $chapterid = $params['chapterid'];

        $warnings = array();

        // Request and permission validation.
        $recommend = $DB->get_record('recommend', array('id' => $recommendid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($recommend, 'recommend');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/recommend:read', $context);

        $chapters = recommend_preload_chapters($recommend);
        $firstchapterid = 0;
        $lastchapterid = 0;

        foreach ($chapters as $ch) {
            if ($ch->hidden) {
                continue;
            }
            if (!$firstchapterid) {
                $firstchapterid = $ch->id;
            }
            $lastchapterid = $ch->id;
        }

        if (!$chapterid) {
            // Trigger the module viewed events since we are displaying the recommend.
            recommend_view($recommend, null, false, $course, $cm, $context);
            $chapterid = $firstchapterid;
        }

        // Check if recommend is empty (warning).
        if (!$chapterid) {
            $warnings[] = array(
                'item' => 'recommend',
                'itemid' => $recommend->id,
                'warningcode' => '1',
                'message' => get_string('nocontent', 'mod_recommend')
            );
        } else {
            $chapter = $DB->get_record('recommend_chapters', array('id' => $chapterid, 'recommendid' => $recommend->id));
            $viewhidden = has_capability('mod/recommend:viewhiddenchapters', $context);

            if (!$chapter or ($chapter->hidden and !$viewhidden)) {
                throw new moodle_exception('errorchapter', 'mod_recommend');
            }

            // Trigger the chapter viewed event.
            $islastchapter = ($chapter->id == $lastchapterid) ? true : false;
            recommend_view($recommend, $chapter, $islastchapter, $course, $cm, $context);
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_recommend_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_recommends_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_recommends_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of recommends in a provided list of courses,
     * if no list is provided all recommends that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array of recommends details
     * @since Moodle 3.0
     */
    public static function get_recommends_by_courses($courseids = array()) {
        global $CFG;

        $returnedrecommends = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_recommends_by_courses_parameters(), array('courseids' => $courseids));

        $courses = array();
        if (empty($params['courseids'])) {
            $courses = enrol_get_my_courses();
            $params['courseids'] = array_keys($courses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $courses);

            // Get the recommends in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $recommends = get_all_instances_in_courses("recommend", $courses);
            foreach ($recommends as $recommend) {
                $context = context_module::instance($recommend->coursemodule);
                // Entry to return.
                $recommenddetails = array();
                // First, we return information that any user can see in the web interface.
                $recommenddetails['id'] = $recommend->id;
                $recommenddetails['coursemodule']      = $recommend->coursemodule;
                $recommenddetails['course']            = $recommend->course;
                $recommenddetails['name']              = external_format_string($recommend->name, $context->id);
                // Format intro.
                $options = array('noclean' => true);
                list($recommenddetails['intro'], $recommenddetails['introformat']) =
                    external_format_text($recommend->intro, $recommend->introformat, $context->id, 'mod_recommend', 'intro', null, $options);
                $recommenddetails['introfiles'] = external_util::get_area_files($context->id, 'mod_recommend', 'intro', false, false);
                $recommenddetails['numbering']         = $recommend->numbering;
                $recommenddetails['navstyle']          = $recommend->navstyle;
                $recommenddetails['customtitles']      = $recommend->customtitles;

                if (has_capability('moodle/course:manageactivities', $context)) {
                    $recommenddetails['revision']      = $recommend->revision;
                    $recommenddetails['timecreated']   = $recommend->timecreated;
                    $recommenddetails['timemodified']  = $recommend->timemodified;
                    $recommenddetails['section']       = $recommend->section;
                    $recommenddetails['visible']       = $recommend->visible;
                    $recommenddetails['groupmode']     = $recommend->groupmode;
                    $recommenddetails['groupingid']    = $recommend->groupingid;
                }
                $returnedrecommends[] = $recommenddetails;
            }
        }
        $result = array();
        $result['recommends'] = $returnedrecommends;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_recommends_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_recommends_by_courses_returns() {
        return new external_single_structure(
            array(
                'recommends' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'recommend id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'recommend name'),
                            'intro' => new external_value(PARAM_RAW, 'The recommend intro'),
                            'introformat' => new external_format_value('intro'),
                            'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                            'numbering' => new external_value(PARAM_INT, 'recommend numbering configuration'),
                            'navstyle' => new external_value(PARAM_INT, 'recommend navigation style configuration'),
                            'customtitles' => new external_value(PARAM_INT, 'recommend custom titles type'),
                            'revision' => new external_value(PARAM_INT, 'recommend revision', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'Time of creation', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_BOOL, 'Visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL),
                        ), 'recommends'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

}

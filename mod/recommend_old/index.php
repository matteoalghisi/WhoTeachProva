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
 * This page lists all the instances of recommend in a particular course
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

unset($id);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

// Get all required strings
$strrecommends        = get_string('modulenameplural', 'mod_recommend');
$strrecommend         = get_string('modulename', 'mod_recommend');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/recommend/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strrecommends);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strrecommends);
echo $OUTPUT->header();

\mod_recommend\event\course_module_instance_list_viewed::create_from_course($course)->trigger();

// Get all the appropriate data
if (!$recommends = get_all_instances_in_course('recommend', $course)) {
    notice(get_string('thereareno', 'moodle', $strrecommends), "$CFG->wwwroot/course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($recommends as $recommend) {
    $cm = $modinfo->get_cm($recommend->coursemodule);
    if ($usesections) {
        $printsection = '';
        if ($recommend->section !== $currentsection) {
            if ($recommend->section) {
                $printsection = get_section_name($course, $recommend->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $recommend->section;
        }
    } else {
        $printsection = html_writer::tag('span', userdate($recommend->timemodified), array('class' => 'smallinfo'));
    }

    $class = $recommend->visible ? null : array('class' => 'dimmed'); // hidden modules are dimmed

    $table->data[] = array (
        $printsection,
        html_writer::link(new moodle_url('view.php', array('id' => $cm->id)), format_string($recommend->name), $class),
        format_module_intro('recommend', $recommend, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();

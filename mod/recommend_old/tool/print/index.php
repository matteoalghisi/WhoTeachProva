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
 * recommend printing
 *
 * @package    recommendtool_print
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../../config.php');
require_once(__DIR__.'/locallib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================

$cm = get_coursemodule_from_id('recommend', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$recommend = $DB->get_record('recommend', array('id' => $cm->instance), '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/recommend:read', $context);
require_capability('recommendtool/print:print', $context);

// Check all variables.
if ($chapterid) {
    // Single chapter printing - only visible!
    $chapter = $DB->get_record('recommend_chapters', array('id' => $chapterid, 'recommendid' => $recommend->id), '*',
            MUST_EXIST);
} else {
    // Complete recommend.
    $chapter = false;
}

$PAGE->set_url('/mod/recommend/print.php', array('id' => $id, 'chapterid' => $chapterid));

$PAGE->set_pagelayout("embedded");

unset($id);
unset($chapterid);

// Security checks END.

// read chapters
$chapters = recommend_preload_chapters($recommend);

$strrecommends = get_string('modulenameplural', 'mod_recommend');
$strrecommend  = get_string('modulename', 'mod_recommend');
$strtop   = get_string('top', 'mod_recommend');

// Page header.
$strtitle = format_string($recommend->name, true, array('context' => $context));
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->requires->css('/mod/recommend/tool/print/print.css');

$renderer = $PAGE->get_renderer('recommendtool_print');

// Begin page output.
echo $OUTPUT->header();

if ($chapter) {
    if ($chapter->hidden) {
        require_capability('mod/recommend:viewhiddenchapters', $context);
    }
    \recommendtool_print\event\chapter_printed::create_from_chapter($recommend, $context, $chapter)->trigger();
    $page = new recommendtool_print\output\print_recommend_chapter_page($recommend, $cm, $chapter);
} else {
    \recommendtool_print\event\recommend_printed::create_from_recommend($recommend, $context)->trigger();
    $page = new recommendtool_print\output\print_recommend_page($recommend, $cm);
}

echo $renderer->render($page);

// Finish page output.
echo $OUTPUT->footer();
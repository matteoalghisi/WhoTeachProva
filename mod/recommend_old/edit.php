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
 * Edit recommend chapter
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/edit_form.php');

$cmid       = required_param('cmid', PARAM_INT);  // recommend Course Module ID
$chapterid  = optional_param('id', 0, PARAM_INT); // Chapter ID
$pagenum    = optional_param('pagenum', 0, PARAM_INT);
$subchapter = optional_param('subchapter', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('recommend', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$recommend = $DB->get_record('recommend', array('id'=>$cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/recommend:edit', $context);

$PAGE->set_url('/mod/recommend/edit.php', array('cmid'=>$cmid, 'id'=>$chapterid, 'pagenum'=>$pagenum, 'subchapter'=>$subchapter));
$PAGE->set_pagelayout('admin'); // TODO: Something. This is a bloody hack!

if ($chapterid) {
    $chapter = $DB->get_record('recommend_chapters', array('id'=>$chapterid, 'recommendid'=>$recommend->id), '*', MUST_EXIST);
    $chapter->tags = core_tag_tag::get_item_tags_array('mod_recommend', 'recommend_chapters', $chapter->id);
} else {
    $chapter = new stdClass();
    $chapter->id         = null;
    $chapter->subchapter = $subchapter;
    $chapter->pagenum    = $pagenum + 1;
}
$chapter->cmid = $cm->id;

// Get the previous page number.
$prevpage = $chapter->pagenum - 1;
if ($prevpage) {
    $currentchapter = $DB->get_record('recommend_chapters', ['pagenum' => $prevpage, 'recommendid' => $recommend->id]);
    if ($currentchapter) {
        $chapter->currentchaptertitle = $currentchapter->title;
    }
}

$options = array('noclean'=>true, 'subdirs'=>true, 'maxfiles'=>-1, 'maxbytes'=>0, 'context'=>$context);
$chapter = file_prepare_standard_editor($chapter, 'content', $options, $context, 'mod_recommend', 'chapter', $chapter->id);

$mform = new recommend_chapter_edit_form(null, array('chapter'=>$chapter, 'options'=>$options));

// If data submitted, then process and store.
if ($mform->is_cancelled()) {
    // Make sure at least one chapter exists.
    $chapters = recommend_preload_chapters($recommend);
    if (!$chapters) {
        redirect(new moodle_url('/course/view.php', array('id' => $course->id))); // Back to course view.
    }

    if (empty($chapter->id)) {
        redirect("view.php?id=$cm->id");
    } else {
        redirect("view.php?id=$cm->id&chapterid=$chapter->id");
    }

} else if ($data = $mform->get_data()) {

    if ($data->id) {
        // store the files
        $data->timemodified = time();
        $data = file_postupdate_standard_editor($data, 'content', $options, $context, 'mod_recommend', 'chapter', $data->id);
        $DB->update_record('recommend_chapters', $data);
        $DB->set_field('recommend', 'revision', $recommend->revision+1, array('id'=>$recommend->id));
        $chapter = $DB->get_record('recommend_chapters', array('id' => $data->id));

        core_tag_tag::set_item_tags('mod_recommend', 'recommend_chapters', $chapter->id, $context, $data->tags);

        \mod_recommend\event\chapter_updated::create_from_chapter($recommend, $context, $chapter)->trigger();
    } else {
        // adding new chapter
        $data->recommendid        = $recommend->id;
        $data->hidden        = 0;
        $data->timecreated   = time();
        $data->timemodified  = time();
        $data->importsrc     = '';
        $data->content       = '';          // updated later
        $data->contentformat = FORMAT_HTML; // updated later

        // make room for new page
        $sql = "UPDATE {recommend_chapters}
                   SET pagenum = pagenum + 1
                 WHERE recommendid = ? AND pagenum >= ?";
        $DB->execute($sql, array($recommend->id, $data->pagenum));

        $data->id = $DB->insert_record('recommend_chapters', $data);

        // store the files
        $data = file_postupdate_standard_editor($data, 'content', $options, $context, 'mod_recommend', 'chapter', $data->id);
        $DB->update_record('recommend_chapters', $data);
        $DB->set_field('recommend', 'revision', $recommend->revision+1, array('id'=>$recommend->id));
        $chapter = $DB->get_record('recommend_chapters', array('id' => $data->id));

        core_tag_tag::set_item_tags('mod_recommend', 'recommend_chapters', $chapter->id, $context, $data->tags);

        \mod_recommend\event\chapter_created::create_from_chapter($recommend, $context, $chapter)->trigger();
    }

    recommend_preload_chapters($recommend); // fix structure
    redirect("view.php?id=$cm->id&chapterid=$data->id");
}

// Otherwise fill and print the form.
$PAGE->set_title($recommend->name);
$PAGE->set_heading($course->fullname);

if ($chapters = recommend_preload_chapters($recommend)) {
    recommend_add_fake_block($chapters, $chapter, $recommend, $cm);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($recommend->name));

$mform->display();

echo $OUTPUT->footer();

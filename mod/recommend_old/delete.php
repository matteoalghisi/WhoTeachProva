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
 * Delete recommend chapter
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

// Course Module ID.
$id        = required_param('id', PARAM_INT);

// Chapter ID.
$chapterid = required_param('chapterid', PARAM_INT);

$confirm   = optional_param('confirm', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('recommend', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$recommend = $DB->get_record('recommend', ['id' => $cm->instance], '*', MUST_EXIST);
$chapter = $DB->get_record('recommend_chapters', ['id' => $chapterid, 'recommendid' => $recommend->id], '*', MUST_EXIST);

require_login($course, false, $cm);
require_sesskey();

$context = context_module::instance($cm->id);
require_capability('mod/recommend:edit', $context);

$PAGE->set_url('/mod/recommend/delete.php', ['id' => $id, 'chapterid' => $chapterid]);

if ($confirm) {
    // The operation was confirmed.
    $fs = get_file_storage();

    $subchaptercount = 0;
    if (!$chapter->subchapter) {
        // This is a top-level chapter.
        // Make sure to remove any sub-chapters if there are any.
        $chapters = $DB->get_recordset_select('recommend_chapters', 'recommendid = :recommendid AND pagenum > :pagenum', [
                'recommendid' => $recommend->id,
                'pagenum' => $chapter->pagenum,
            ], 'pagenum');

        foreach ($chapters as $ch) {
            if (!$ch->subchapter) {
                // This is a new chapter. Any subsequent subchapters will be part of a different chapter.
                break;
            } else {
                // This is subchapter of the chapter being removed.
                core_tag_tag::remove_all_item_tags('mod_recommend', 'recommend_chapters', $ch->id);
                $fs->delete_area_files($context->id, 'mod_recommend', 'chapter', $ch->id);
                $DB->delete_records('recommend_chapters', ['id' => $ch->id]);
                \mod_recommend\event\chapter_deleted::create_from_chapter($recommend, $context, $ch)->trigger();

                $subchaptercount++;
            }
        }
        $chapters->close();
    }

    // Now delete the actual chapter.
    core_tag_tag::remove_all_item_tags('mod_recommend', 'recommend_chapters', $chapter->id);
    $fs->delete_area_files($context->id, 'mod_recommend', 'chapter', $chapter->id);
    $DB->delete_records('recommend_chapters', ['id' => $chapter->id]);

    \mod_recommend\event\chapter_deleted::create_from_chapter($recommend, $context, $chapter)->trigger();

    // Ensure that the recommend structure is correct.
    // recommend_preload_chapters will fix parts including the pagenum.
    $chapters = recommend_preload_chapters($recommend);

    recommend_add_fake_block($chapters, $chapter, $recommend, $cm);

    // Bump the recommend revision.
    $DB->set_field('recommend', 'revision', $recommend->revision + 1, ['id' => $recommend->id]);

    if ($subchaptercount) {
        $message = get_string('chapterandsubchaptersdeleted', 'mod_recommend', (object) [
            'title' => format_string($chapter->title),
            'subchapters' => $subchaptercount,
        ]);
    } else {
        $message = get_string('chapterdeleted', 'mod_recommend', (object) [
            'title' => format_string($chapter->title),
        ]);
    }

    redirect(new moodle_url('/mod/recommend/view.php', ['id' => $cm->id]), $message);
}

redirect(new moodle_url('/mod/recommend/view.php', ['id' => $cm->id]));

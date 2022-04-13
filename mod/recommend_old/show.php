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
 * Show/hide recommend chapter
 *
 * @package    mod_recommend
 * @copyright  2004-2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$id        = required_param('id', PARAM_INT);        // Course Module ID
$chapterid = required_param('chapterid', PARAM_INT); // Chapter ID

$cm = get_coursemodule_from_id('recommend', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$recommend = $DB->get_record('recommend', array('id'=>$cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
require_sesskey();

$context = context_module::instance($cm->id);
require_capability('mod/recommend:edit', $context);

$PAGE->set_url('/mod/recommend/show.php', array('id'=>$id, 'chapterid'=>$chapterid));

$chapter = $DB->get_record('recommend_chapters', array('id'=>$chapterid, 'recommendid'=>$recommend->id), '*', MUST_EXIST);

// Switch hidden state.
$chapter->hidden = $chapter->hidden ? 0 : 1;

// Update record.
$DB->update_record('recommend_chapters', $chapter);
\mod_recommend\event\chapter_updated::create_from_chapter($recommend, $context, $chapter)->trigger();

// Change visibility of subchapters too.
if (!$chapter->subchapter) {
    $chapters = $DB->get_recordset('recommend_chapters', array('recommendid'=>$recommend->id), 'pagenum ASC');
    $found = 0;
    foreach ($chapters as $ch) {
        if ($ch->id == $chapter->id) {
            $found = 1;

        } else if ($found and $ch->subchapter) {
            $ch->hidden = $chapter->hidden;
            $DB->update_record('recommend_chapters', $ch);
            \mod_recommend\event\chapter_updated::create_from_chapter($recommend, $context, $ch)->trigger();

        } else if ($found) {
            break;
        }
    }
    $chapters->close();
}

recommend_preload_chapters($recommend); // fix structure
$DB->set_field('recommend', 'revision', $recommend->revision+1, array('id'=>$recommend->id));

redirect('view.php?id='.$cm->id.'&chapterid='.$chapter->id);


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
 * recommend view page
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id        = optional_param('id', 0, PARAM_INT);        // Course Module ID
$bid       = optional_param('b', 0, PARAM_INT);         // recommend id
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);    // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if ($id) {
    $cm = get_coursemodule_from_id('recommend', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $recommend = $DB->get_record('recommend', array('id'=>$cm->instance), '*', MUST_EXIST);
} else {
    $recommend = $DB->get_record('recommend', array('id'=>$bid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('recommend', $recommend->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $id = $cm->id;
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/recommend:read', $context);

$allowedit  = has_capability('mod/recommend:edit', $context);
$viewhidden = has_capability('mod/recommend:viewhiddenchapters', $context);

if ($allowedit) {
    if ($edit != -1 and confirm_sesskey()) {
        $USER->editing = $edit;
    } else {
        if (isset($USER->editing)) {
            $edit = $USER->editing;
        } else {
            $edit = 0;
        }
    }
} else {
    $edit = 0;
}
/*
// read chapters
$chapters = recommend_preload_chapters($recommend);

if ($allowedit and !$chapters) {
    redirect('edit.php?cmid='.$cm->id); // No chapters - add new one.
}
// Check chapterid and read chapter data
if ($chapterid == '0') { // Go to first chapter if no given.
    // Trigger course module viewed event.
    recommend_view($recommend, null, false, $course, $cm, $context);

    foreach ($chapters as $ch) {
        if ($edit || ($ch->hidden && $viewhidden)) {
            $chapterid = $ch->id;
            break;
        }
        if (!$ch->hidden) {
            $chapterid = $ch->id;
            break;
        }
    }
}

$courseurl = new moodle_url('/course/view.php', array('id' => $course->id));

// No content in the recommend.
if (!$chapterid) {
    $PAGE->set_url('/mod/recommend/view.php', array('id' => $id));
    notice(get_string('nocontent', 'mod_recommend'), $courseurl->out(false));
}
// Chapter doesnt exist or it is hidden for students
if ((!$chapter = $DB->get_record('recommend_chapters', array('id' => $chapterid, 'recommendid' => $recommend->id))) or ($chapter->hidden and !$viewhidden)) {
    print_error('errorchapter', 'mod_recommend', $courseurl);
}

$PAGE->set_url('/mod/recommend/view.php', array('id'=>$id, 'chapterid'=>$chapterid));


// Unset all page parameters.
unset($id);
unset($bid);
unset($chapterid);

// Read standard strings.
$strrecommends = get_string('modulenameplural', 'mod_recommend');
$strrecommend  = get_string('modulename', 'mod_recommend');
$strtoc   = get_string('toc', 'mod_recommend');

// prepare header
$pagetitle = $recommend->name . ": " . $chapter->title;
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

recommend_add_fake_block($chapters, $chapter, $recommend, $cm, $edit);

// prepare chapter navigation icons
$previd = null;
$prevtitle = null;
$navprevtitle = null;
$nextid = null;
$nexttitle = null;
$navnexttitle = null;
$last = null;
foreach ($chapters as $ch) {
    if (!$edit and ($ch->hidden && !$viewhidden)) {
        continue;
    }
    if ($last == $chapter->id) {
        $nextid = $ch->id;
        $nexttitle = recommend_get_chapter_title($ch->id, $chapters, $recommend, $context);
        $navnexttitle = get_string('navnexttitle', 'mod_recommend', $nexttitle);
        break;
    }
    if ($ch->id != $chapter->id) {
        $previd = $ch->id;
        $prevtitle = recommend_get_chapter_title($ch->id, $chapters, $recommend, $context);
        $navprevtitle = get_string('navprevtitle', 'mod_recommend', $prevtitle);
    }
    $last = $ch->id;
}

if ($recommend->navstyle) {
    $navprevicon = right_to_left() ? 'nav_next' : 'nav_prev';
    $navnexticon = right_to_left() ? 'nav_prev' : 'nav_next';
    $navprevdisicon = right_to_left() ? 'nav_next_dis' : 'nav_prev_dis';

    $chnavigation = '';
    if ($previd) {
        $navprev = get_string('navprev', 'recommend');
        if ($recommend->navstyle == 1) {
            $chnavigation .= '<a title="' . $navprevtitle . '" class="recommendprev" href="view.php?id=' .
                $cm->id . '&amp;chapterid=' . $previd .  '">' .
                $OUTPUT->pix_icon($navprevicon, $navprevtitle, 'mod_recommend') . '</a>';
        } else {
            $chnavigation .= '<a title="' . $navprev . '" class="recommendprev" href="view.php?id=' .
                $cm->id . '&amp;chapterid=' . $previd . '">' .
                '<span class="chaptername"><span class="arrow">' . $OUTPUT->larrow() . '&nbsp;</span></span>' .
                $navprev . ':&nbsp;<span class="chaptername">' . $prevtitle . '</span></a>';
        }
    } else {
        if ($recommend->navstyle == 1) {
            $chnavigation .= $OUTPUT->pix_icon($navprevdisicon, '', 'mod_recommend');
        }
    }
    if ($nextid) {
        $navnext = get_string('navnext', 'recommend');
        if ($recommend->navstyle == 1) {
            $chnavigation .= '<a title="' . $navnexttitle . '" class="recommendnext" href="view.php?id=' .
                $cm->id . '&amp;chapterid='.$nextid.'">' .
                $OUTPUT->pix_icon($navnexticon, $navnexttitle, 'mod_recommend') . '</a>';
        } else {
            $chnavigation .= ' <a title="' . $navnext . '" class="recommendnext" href="view.php?id=' .
                $cm->id . '&amp;chapterid='.$nextid.'">' .
                $navnext . ':<span class="chaptername">&nbsp;' . $nexttitle.
                '<span class="arrow">&nbsp;' . $OUTPUT->rarrow() . '</span></span></a>';
        }
    } else {
        $navexit = get_string('navexit', 'recommend');
        $sec = $DB->get_field('course_sections', 'section', array('id' => $cm->section));
        $returnurl = course_get_url($course, $sec);
        if ($recommend->navstyle == 1) {
            $chnavigation .= '<a title="' . $navexit . '" class="recommendexit"  href="'.$returnurl.'">' .
                $OUTPUT->pix_icon('nav_exit', $navexit, 'mod_recommend') . '</a>';
        } else {
            $chnavigation .= ' <a title="' . $navexit . '" class="recommendexit"  href="'.$returnurl.'">' .
                '<span class="chaptername">' . $navexit . '&nbsp;' . $OUTPUT->uarrow() . '</span></a>';
        }
    }
}

// We need to discover if this is the last chapter to mark activity as completed.
$islastchapter = false;
if (!$nextid) {
    $islastchapter = true;
}

recommend_view($recommend, $chapter, $islastchapter, $course, $cm, $context);

*/

// =====================================================
// recommend display HTML code
// =====================================================

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($recommend->name));

// Info box.
if ($recommend->intro) {
    echo $OUTPUT->box(format_module_intro('recommend', $recommend, $cm->id), 'generalbox', 'intro');
}

$navclasses = recommend_get_nav_classes();

if ($recommend->navstyle) {
    // Upper navigation.
    echo '<div class="navtop clearfix ' . $navclasses[$recommend->navstyle] . '">' . $chnavigation . '</div>';
}
/*
// The chapter itself.
$hidden = $chapter->hidden ? ' dimmed_text' : null;
echo $OUTPUT->box_start('generalbox recommend_content' . $hidden);

if (!$recommend->customtitles) {
    if (!$chapter->subchapter) {
        $currtitle = recommend_get_chapter_title($chapter->id, $chapters, $recommend, $context);
        echo $OUTPUT->heading($currtitle, 3);
    } else {
        $currtitle = recommend_get_chapter_title($chapters[$chapter->id]->parent, $chapters, $recommend, $context);
        $currsubtitle = recommend_get_chapter_title($chapter->id, $chapters, $recommend, $context);
        echo $OUTPUT->heading($currtitle, 3);
        echo $OUTPUT->heading($currsubtitle, 4);
    }
}
$chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_recommend', 'chapter', $chapter->id);
echo format_text($chaptertext, $chapter->contentformat, array('noclean'=>true, 'overflowdiv'=>true, 'context'=>$context));
*/
echo $OUTPUT->box_end();

if (core_tag_tag::is_enabled('mod_recommend', 'recommend_chapters')) {
    echo $OUTPUT->tag_list(core_tag_tag::get_item_tags('mod_recommend', 'recommend_chapters', $chapter->id), null, 'recommend-tags');
}

if ($recommend->navstyle) {
    // Lower navigation.
    echo '<div class="navbottom clearfix ' . $navclasses[$recommend->navstyle] . '">' . $chnavigation . '</div>';
}

echo $OUTPUT->footer();

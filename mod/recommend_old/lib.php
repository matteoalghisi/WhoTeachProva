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
 * recommend module core interaction API
 *
 * @package    mod_recommend
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Returns list of available numbering types
 * @return array
 */
function recommend_get_numbering_types() {
    global $CFG; // required for the include

    require_once(__DIR__.'/locallib.php');

    return array (
        recommend_NUM_NONE       => get_string('numbering0', 'mod_recommend'),
        recommend_NUM_NUMBERS    => get_string('numbering1', 'mod_recommend'),
        recommend_NUM_BULLETS    => get_string('numbering2', 'mod_recommend'),
        recommend_NUM_INDENTED   => get_string('numbering3', 'mod_recommend')
    );
}

/**
 * Returns list of available navigation link types.
 * @return array
 */
function recommend_get_nav_types() {
    require_once(__DIR__.'/locallib.php');

    return array (
        recommend_LINK_TOCONLY   => get_string('navtoc', 'mod_recommend'),
        recommend_LINK_IMAGE     => get_string('navimages', 'mod_recommend'),
        recommend_LINK_TEXT      => get_string('navtext', 'mod_recommend'),
    );
}

/**
 * Returns list of available navigation link CSS classes.
 * @return array
 */
function recommend_get_nav_classes() {
    return array ('navtoc', 'navimages', 'navtext');
}

/**
 * Add recommend instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return int new recommend instance id
 */
function recommend_add_instance($data, $mform) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;
    if (!isset($data->customtitles)) {
        $data->customtitles = 0;
    }

    $id = $DB->insert_record('recommend', $data);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'recommend', $id, $completiontimeexpected);

    return $id;
}

/**
 * Update recommend instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return bool true
 */
function recommend_update_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;
    if (!isset($data->customtitles)) {
        $data->customtitles = 0;
    }

    $DB->update_record('recommend', $data);

    $recommend = $DB->get_record('recommend', array('id'=>$data->id));
    $DB->set_field('recommend', 'revision', $recommend->revision+1, array('id'=>$recommend->id));

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'recommend', $recommend->id, $completiontimeexpected);

    return true;
}

/**
 * Delete recommend instance by activity id
 *
 * @param int $id
 * @return bool success
 */
function recommend_delete_instance($id) {
    global $DB;

    if (!$recommend = $DB->get_record('recommend', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('recommend', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'recommend', $id, null);

    $DB->delete_records('recommend_chapters', array('recommendid'=>$recommend->id));
    $DB->delete_records('recommend', array('id'=>$recommend->id));

    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in recommend activities and print it out.
 *
 * @param stdClass $course
 * @param bool $viewfullnames
 * @param int $timestart
 * @return bool true if there was output, or false is there was none
 */
function recommend_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function recommend_reset_userdata($data) {
    global $DB;
    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    $status = [];

    if (!empty($data->reset_recommend_tags)) {
        // Loop through the recommends and remove the tags from the chapters.
        if ($recommends = $DB->get_records('recommend', array('course' => $data->courseid))) {
            foreach ($recommends as $recommend) {
                if (!$cm = get_coursemodule_from_instance('recommend', $recommend->id)) {
                    continue;
                }

                $context = context_module::instance($cm->id);
                core_tag_tag::delete_instances('mod_recommend', null, $context->id);
            }
        }


        $status[] = [
            'component' => get_string('modulenameplural', 'recommend'),
            'item' => get_string('tagsdeleted', 'recommend'),
            'error' => false
        ];
    }

    return $status;
}

/**
 * The elements to add the course reset form.
 *
 * @param moodleform $mform
 */
function recommend_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'recommendheader', get_string('modulenameplural', 'recommend'));
    $mform->addElement('checkbox', 'reset_recommend_tags', get_string('removeallrecommendtags', 'recommend'));
}

/**
 * No cron in recommend.
 *
 * @return bool
 */
function recommend_cron () {
    return true;
}

/**
 * No grading in recommend.
 *
 * @param int $recommendid
 * @return null
 */
function recommend_grades($recommendid) {
    return null;
}

/**
 * @deprecated since Moodle 3.8
 */
function recommend_scale_used() {
    throw new coding_exception('recommend_scale_used() can not be used anymore. Plugins can implement ' .
        '<modname>_scale_used_anywhere, all implementations of <modname>_scale_used are now ignored');
}

/**
 * Checks if scale is being used by any instance of recommend
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return bool true if the scale is used by any recommend
 */
function recommend_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Return read actions.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function recommend_get_view_actions() {
    global $CFG; // necessary for includes

    $return = array('view', 'view all');

    $plugins = core_component::get_plugin_list('recommendtool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'recommendtool_'.$plugin.'_get_view_actions';
        if (function_exists($function)) {
            if ($actions = $function()) {
                $return = array_merge($return, $actions);
            }
        }
    }

    return $return;
}

/**
 * Return write actions.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function recommend_get_post_actions() {
    global $CFG; // necessary for includes

    $return = array('update');

    $plugins = core_component::get_plugin_list('recommendtool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'recommendtool_'.$plugin.'_get_post_actions';
        if (function_exists($function)) {
            if ($actions = $function()) {
                $return = array_merge($return, $actions);
            }
        }
    }

    return $return;
}

/**
 * Supported features
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function recommend_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settingsnav The settings navigation object
 * @param navigation_node $recommendnode The node to add module settings to
 * @return void
 */
function recommend_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $recommendnode) {
    global $USER, $PAGE, $OUTPUT;

    if ($recommendnode->children->count() > 0) {
        $firstkey = $recommendnode->children->get_key_list()[0];
    } else {
        $firstkey = null;
    }

    $params = $PAGE->url->params();

    if ($PAGE->cm->modname === 'recommend' and !empty($params['id']) and !empty($params['chapterid'])
            and has_capability('mod/recommend:edit', $PAGE->cm->context)) {
        if (!empty($USER->editing)) {
            $string = get_string("turneditingoff");
            $edit = '0';
        } else {
            $string = get_string("turneditingon");
            $edit = '1';
        }
        $url = new moodle_url('/mod/recommend/view.php', array('id'=>$params['id'], 'chapterid'=>$params['chapterid'], 'edit'=>$edit, 'sesskey'=>sesskey()));
        $editnode = navigation_node::create($string, $url, navigation_node::TYPE_SETTING);
        $recommendnode->add_node($editnode, $firstkey);
        $PAGE->set_button($OUTPUT->single_button($url, $string));
    }

    $plugins = core_component::get_plugin_list('recommendtool');
    foreach ($plugins as $plugin => $dir) {
        if (file_exists("$dir/lib.php")) {
            require_once("$dir/lib.php");
        }
        $function = 'recommendtool_'.$plugin.'_extend_settings_navigation';
        if (function_exists($function)) {
            $function($settingsnav, $recommendnode);
        }
    }
}


/**
 * Lists all browsable file areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @return array
 */
function recommend_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['chapter'] = get_string('chapters', 'mod_recommend');
    return $areas;
}

/**
 * File browsing support for recommend module chapter area.
 * @param object $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function recommend_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    // note: 'intro' area is handled in file_browser automatically

    if (!has_capability('mod/recommend:read', $context)) {
        return null;
    }

    if ($filearea !== 'chapter') {
        return null;
    }

    require_once(__DIR__.'/locallib.php');

    if (is_null($itemid)) {
        return new recommend_file_info($browser, $course, $cm, $context, $areas, $filearea);
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$storedfile = $fs->get_file($context->id, 'mod_recommend', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }

    // modifications may be tricky - may cause caching problems
    $canwrite = has_capability('mod/recommend:edit', $context);

    $chaptername = $DB->get_field('recommend_chapters', 'title', array('recommendid'=>$cm->instance, 'id'=>$itemid));
    $chaptername = format_string($chaptername, true, array('context'=>$context));

    $urlbase = $CFG->wwwroot.'/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $chaptername, true, true, $canwrite, false);
}

/**
 * Serves the recommend attachments. Implements needed access control ;-)
 *
 * @param stdClass $course course object
 * @param cm_info $cm course module object
 * @param context $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function recommend_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea !== 'chapter') {
        return false;
    }

    if (!has_capability('mod/recommend:read', $context)) {
        return false;
    }

    $chid = (int)array_shift($args);

    if (!$recommend = $DB->get_record('recommend', array('id'=>$cm->instance))) {
        return false;
    }

    if (!$chapter = $DB->get_record('recommend_chapters', array('id'=>$chid, 'recommendid'=>$recommend->id))) {
        return false;
    }

    if ($chapter->hidden and !has_capability('mod/recommend:viewhiddenchapters', $context)) {
        return false;
    }

    // Download the contents of a chapter as an html file.
    if ($args[0] == 'index.html') {
        $filename = "index.html";

        // We need to rewrite the pluginfile URLs so the media filters can work.
        $content = file_rewrite_pluginfile_urls($chapter->content, 'webservice/pluginfile.php', $context->id, 'mod_recommend', 'chapter',
                                                $chapter->id);
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;

        $content = format_text($content, $chapter->contentformat, $formatoptions);

        // Remove @@PLUGINFILE@@/.
        $options = array('reverse' => true);
        $content = file_rewrite_pluginfile_urls($content, 'webservice/pluginfile.php', $context->id, 'mod_recommend', 'chapter',
                                                $chapter->id, $options);
        $content = str_replace('@@PLUGINFILE@@/', '', $content);

        $titles = "";
        // Format the chapter titles.
        if (!$recommend->customtitles) {
            require_once(__DIR__.'/locallib.php');
            $chapters = recommend_preload_chapters($recommend);

            if (!$chapter->subchapter) {
                $currtitle = recommend_get_chapter_title($chapter->id, $chapters, $recommend, $context);
                // Note that we can't use the $OUTPUT->heading() in WS_SERVER mode.
                $titles = "<h3>$currtitle</h3>";
            } else {
                $currtitle = recommend_get_chapter_title($chapters[$chapter->id]->parent, $chapters, $recommend, $context);
                $currsubtitle = recommend_get_chapter_title($chapter->id, $chapters, $recommend, $context);
                // Note that we can't use the $OUTPUT->heading() in WS_SERVER mode.
                $titles = "<h3>$currtitle</h3>";
                $titles .= "<h4>$currsubtitle</h4>";
            }
        }

        $content = $titles . $content;

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_recommend/chapter/$chid/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // Nasty hack because we do not have file revisions in recommend yet.
        $lifetime = $CFG->filelifetime;
        if ($lifetime > 60 * 10) {
            $lifetime = 60 * 10;
        }

        // Finally send the file.
        send_stored_file($file, $lifetime, 0, $forcedownload, $options);
    }
}

/**
 * Return a list of page types
 *
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function recommend_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-recommend-*'=>get_string('page-mod-recommend-x', 'mod_recommend'));
    return $module_pagetype;
}

/**
 * Export recommend resource contents
 *
 * @param  stdClass $cm     Course module object
 * @param  string $baseurl  Base URL for file downloads
 * @return array of file content
 */
function recommend_export_contents($cm, $baseurl) {
    global $DB;

    $contents = array();
    $context = context_module::instance($cm->id);

    $recommend = $DB->get_record('recommend', array('id' => $cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();

    $chapters = $DB->get_records('recommend_chapters', array('recommendid' => $recommend->id), 'pagenum');

    $structure = array();
    $currentchapter = 0;

    foreach ($chapters as $chapter) {
        if ($chapter->hidden && !has_capability('mod/recommend:viewhiddenchapters', $context)) {
            continue;
        }

        // Generate the recommend structure.
        $thischapter = array(
            "title"     => format_string($chapter->title, true, array('context' => $context)),
            "href"      => $chapter->id . "/index.html",
            "level"     => 0,
            "hidden"    => $chapter->hidden,
            "subitems"  => array()
        );

        // Main chapter.
        if (!$chapter->subchapter) {
            $currentchapter = $chapter->pagenum;
            $structure[$currentchapter] = $thischapter;
        } else {
            // Subchapter.
            $thischapter['level'] = 1;
            $structure[$currentchapter]["subitems"][] = $thischapter;
        }

        // Export the chapter contents.

        // Main content (html).
        $filename = 'index.html';
        $chapterindexfile = array();
        $chapterindexfile['type']         = 'file';
        $chapterindexfile['filename']     = $filename;
        // Each chapter in a subdirectory.
        $chapterindexfile['filepath']     = "/{$chapter->id}/";
        $chapterindexfile['filesize']     = 0;
        $chapterindexfile['fileurl']      = moodle_url::make_webservice_pluginfile_url(
                    $context->id, 'mod_recommend', 'chapter', $chapter->id, '/', 'index.html')->out(false);
        $chapterindexfile['timecreated']  = $chapter->timecreated;
        $chapterindexfile['timemodified'] = $chapter->timemodified;
        $chapterindexfile['content']      = format_string($chapter->title, true, array('context' => $context));
        $chapterindexfile['sortorder']    = 0;
        $chapterindexfile['userid']       = null;
        $chapterindexfile['author']       = null;
        $chapterindexfile['license']      = null;
        $chapterindexfile['tags']         = \core_tag\external\util::get_item_tags('mod_recommend', 'recommend_chapters', $chapter->id);
        $contents[] = $chapterindexfile;

        // Chapter files (images usually).
        $files = $fs->get_area_files($context->id, 'mod_recommend', 'chapter', $chapter->id, 'sortorder DESC, id ASC', false);
        foreach ($files as $fileinfo) {
            $file = array();
            $file['type']         = 'file';
            $file['filename']     = $fileinfo->get_filename();
            $file['filepath']     = "/{$chapter->id}" . $fileinfo->get_filepath();
            $file['filesize']     = $fileinfo->get_filesize();
            $file['fileurl']      = moodle_url::make_webservice_pluginfile_url(
                                        $context->id, 'mod_recommend', 'chapter', $chapter->id,
                                        $fileinfo->get_filepath(), $fileinfo->get_filename())->out(false);
            $file['timecreated']  = $fileinfo->get_timecreated();
            $file['timemodified'] = $fileinfo->get_timemodified();
            $file['sortorder']    = $fileinfo->get_sortorder();
            $file['userid']       = $fileinfo->get_userid();
            $file['author']       = $fileinfo->get_author();
            $file['license']      = $fileinfo->get_license();
            $file['mimetype']     = $fileinfo->get_mimetype();
            $file['isexternalfile'] = $fileinfo->is_external_file();
            if ($file['isexternalfile']) {
                $file['repositorytype'] = $fileinfo->get_repository_type();
            }
            $contents[] = $file;
        }
    }

    // First content is the structure in encoded JSON format.
    $structurefile = array();
    $structurefile['type']         = 'content';
    $structurefile['filename']     = 'structure';
    $structurefile['filepath']     = "/";
    $structurefile['filesize']     = 0;
    $structurefile['fileurl']      = null;
    $structurefile['timecreated']  = $recommend->timecreated;
    $structurefile['timemodified'] = $recommend->timemodified;
    $structurefile['content']      = json_encode(array_values($structure));
    $structurefile['sortorder']    = 0;
    $structurefile['userid']       = null;
    $structurefile['author']       = null;
    $structurefile['license']      = null;

    // Add it as first element.
    array_unshift($contents, $structurefile);

    return $contents;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $recommend       recommend object
 * @param  stdClass $chapter    chapter object
 * @param  bool $islaschapter   is the las chapter of the recommend?
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function recommend_view($recommend, $chapter, $islastchapter, $course, $cm, $context) {

    // First case, we are just opening the recommend.
    if (empty($chapter)) {
        \mod_recommend\event\course_module_viewed::create_from_recommend($recommend, $context)->trigger();

    } else {
        \mod_recommend\event\chapter_viewed::create_from_chapter($recommend, $context, $chapter)->trigger();

        if ($islastchapter) {
            // We cheat a bit here in assuming that viewing the last page means the user viewed the whole recommend.
            $completion = new completion_info($course);
            $completion->set_module_viewed($cm);
        }
    }
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function recommend_check_updates_since(cm_info $cm, $from, $filter = array()) {
    global $DB;

    $context = $cm->context;
    $updates = new stdClass();
    if (!has_capability('mod/recommend:read', $context)) {
        return $updates;
    }
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);

    $select = 'recommendid = :id AND (timecreated > :since1 OR timemodified > :since2)';
    $params = array('id' => $cm->instance, 'since1' => $from, 'since2' => $from);
    if (!has_capability('mod/recommend:viewhiddenchapters', $context)) {
        $select .= ' AND hidden = 0';
    }
    $updates->entries = (object) array('updated' => false);
    $entries = $DB->get_records_select('recommend_chapters', $select, $params, '', 'id');
    if (!empty($entries)) {
        $updates->entries->updated = true;
        $updates->entries->itemids = array_keys($entries);
    }

    return $updates;
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_recommend_get_fontawesome_icon_map() {
    return [
        'mod_recommend:chapter' => 'fa-recommendmark-o',
        'mod_recommend:nav_prev' => 'fa-arrow-left',
        'mod_recommend:nav_prev_dis' => 'fa-angle-left',
        'mod_recommend:nav_sep' => 'fa-minus',
        'mod_recommend:add' => 'fa-plus',
        'mod_recommend:nav_next' => 'fa-arrow-right',
        'mod_recommend:nav_next_dis' => 'fa-angle-right',
        'mod_recommend:nav_exit' => 'fa-arrow-up',
    ];
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
/* Not supported in old version , supported in 2019*******

function mod_recommend_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory,
                                                     int $userid = 0) {
    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['recommend'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $context = context_module::instance($cm->id);

    if (!has_capability('mod/recommend:read', $context, $userid)) {
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/recommend/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

*/


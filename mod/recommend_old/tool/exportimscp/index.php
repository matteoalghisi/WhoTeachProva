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
 * recommend IMSCP export plugin
 *
 * @package    recommendtool_exportimscp
 * @copyright  2001-3001 Antonio Vicent          {@link http://ludens.es}
 * @copyright  2001-3001 Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @copyright  2011 Petr Skoda                   {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../../config.php');
require_once(__DIR__.'/locallib.php');
require_once($CFG->dirroot.'/mod/recommend/locallib.php');
require_once($CFG->libdir.'/filelib.php');

$id = required_param('id', PARAM_INT);           // Course Module ID

$cm = get_coursemodule_from_id('recommend', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$recommend = $DB->get_record('recommend', array('id'=>$cm->instance), '*', MUST_EXIST);

$PAGE->set_url('/mod/recommend/tool/exportimscp/index.php', array('id'=>$id));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/recommend:read', $context);
require_capability('recommendtool/exportimscp:export', $context);

\recommendtool_exportimscp\event\recommend_exported::create_from_recommend($recommend, $context)->trigger();

$file = recommendtool_exportimscp_build_package($recommend, $context);

send_stored_file($file, 10, 0, true, array('filename' => clean_filename($recommend->name).'.zip'));

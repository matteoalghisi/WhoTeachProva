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
 * Configurable Reports
 * A Moodle block for creating Configurable Reports
 * @package blocks
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date: 2009
 */

require_once("../../config.php");
global $USER, $DB, $CFG;
require_once("forms/ajaxdemoform.php");

require_login();

$obj = new stdClass();

$PAGE->set_url('/blocks/testblockz/managereport.php', array('courseid' => $course->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$title = get_string('reports', 'block_testblockz');

$PAGE->navbar->add(get_string('addreports', 'block_testblockz'));

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

$currenttab = 'addreports';
include('tabs.php');

$mform = new ajaxdemo_form();
$mform->display();


$report = null;
if ($data = $mform->get_data()) {
	$report = $DB->get_record('course', ['id' => $data->groups]);
	$newreport = new stdclass();
	$newreport->groupid = $report->id;
	$newreport->name = $report->shortname;
	$newreport->ownerid = $USER->id; 
    $DB->insert_record('block_testblockz', $newreport);

	//print_r ($newreport);

	//$urltogo = new moodle_url('/blocks/testblockz/deletereports.php', array('delete' => $PAGE->course->id));
}

echo $OUTPUT->footer();
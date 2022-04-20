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

require_login();

$obj = new stdClass();

$PAGE->set_url('/blocks/testblockz/viewreport.php', array('courseid' => $course->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$title = get_string('reports', 'block_testblockz');

$PAGE->navbar->add(get_string('viewreports', 'block_testblockz'));

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();





//testdashboard
//$concatmid = array();

$concatmid = $DB->get_record('block_testblockz', ['groupid' => 111]); //devo mettere id parametro
$concatmid2 = json_encode($concatmid->name);
$filteredcourse = str_replace(' ', '%2520', $concatmid2);

//$concat_tot = "<iframe width=\"600\" height=\"450\" src=\"https://datastudio.google.com/reporting/4183c125-c091-4a29-a451-e3d21561504b/page/qxTqC?params=%7B%22df2%22:%22include%25EE%2580%25800%25EE%2580%2580IN%25EE%2580%2580" . $filteredcourse . "%22%7D\"></iframe>";
//echo $concat_tot;
//echo "{$concatstart}{$testingthisshit}{$concatend}{$concatend2}{$concatend3}";


echo '<iframe width="600" height="450" src="https://datastudio.google.com/reporting/4183c125-c091-4a29-a451-e3d21561504b/page/qxTqC?params=%7B%22df2%22:%22include%25EE%2580%25800%25EE%2580%2580IN%25EE%2580%2580ADHR%2520-%2520Operatore%2520di%2520Pelletteria%22%7D" frameborder="0" style="border:0" allowfullscreen></iframe>';





echo $OUTPUT->footer();
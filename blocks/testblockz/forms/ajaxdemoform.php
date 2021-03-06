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
 * Rates form
 *
 * @package    local_ajaxdemoform
 * @copyright  2020 Ricoshae Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once("$CFG->libdir/formslib.php");

 class ajaxdemo_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG,$DB;

        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('html', '<h3>Aggiungi report per Ragione Sociale</h3><br><br>');

        $groups = $DB->get_records('course', null, $sort='fullname'); //da cambiare per formaterziario
        $options= [];
        $options[-1]= 'None';
        foreach($groups AS $course) {
			$options[$course->id] = $course->fullname;
        }

        $mform->addElement('select', 'groups', 'Gruppi', $options); // Add elements to your form
        $mform->setType('groups', PARAM_INT);                   //Set type of element
        $mform->setDefault('groups', -1);        //Default value

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'Submit', 'Aggiungi');
        $mform->addgroup($buttonarray, 'buttonar', '', ' ', false);

    }

    //Custom validation should be added here
    public function validation($data, $files)
    {
        return array();
    }

  }
  
 class commesse_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG,$DB;

        $mform = $this->_form; // Don't forget the underscore!
        $mform->addElement('html', '<h3>Aggiungi report per Commesse</h3><br><br>');

        $courses = $DB->get_records('course',null); //da cambiare per formaterziario
        $options= [];
        $options[-1]= 'None';
        foreach($courses AS $course) {
			$options[$course->id] = $course->fullname;
        }

        $mform->addElement('select', 'courses', 'Courses', $options); 	// Add elements to your form
        $mform->setType('courses', PARAM_INT);                   		//Set type of element
        $mform->setDefault('courses', -1);       						//Default value

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'Submit', 'Aggiungi');
        $mform->addgroup($buttonarray, 'buttonar', '', ' ', false);

    }

    //Custom validation should be added here
    public function validation($data, $files)
    {
        return array();
    }

  }
  
 class delete_form extends moodleform
{
    //Delete elements
    public function definition()
    {
        global $CFG,$DB;

        $mform = $this->_form; // Don't forget the underscore!
		echo '<br>';
        $groups = $DB->get_records('block_testblockz',null); //da cambiare per formaterziario
        $options= [];
        $options[-1]= 'None';
        foreach($groups AS $course) {
			$options[$course->id] = $course->name;
        }

        $mform->addElement('select', 'groups', 'Gruppi', $options); // Add elements to your form
        $mform->setType('groups', PARAM_INT);                   	 //Set type of element
        $mform->setDefault('groups', -1);        					 //Default value

        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'Submit', 'Elimina gruppo');
        $mform->addgroup($buttonarray, 'buttonar', '', ' ', false);

    }

    //Custom validation should be added here
    public function validation($data, $files)
    {
        return array();
    }

  }
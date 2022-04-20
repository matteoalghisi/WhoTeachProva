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
 * Creates personalized views for Data Studio dashboards
 * @package block_testblock
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_testblockz extends block_list {

    public function init() {
        $this->title = get_string('pluginname', 'block_testblockz');
    }


    function has_config(){
        return true;
    }

    function get_content() {
        global $DB;


        if ($this->content !== NULL){
            return $this->content;
        }

        $content = '';
        $showcourses = get_config('block_testblockz', 'showcourse');

		$results = $DB->get_records_sql('SELECT name FROM {block_testblockz} ');
        // $results = $DB->get_records_sql('SELECT DISTINCT d.data
                 // FROM {user_info_data} as d JOIN {user_info_field} as f ON d.fieldid=f.id WHERE shortname = ? ORDER BY d.data',
				 // ['Settore']); 
		
        $this->content = new stdClass;
        $this->content->items  = array();
        $this->content->icons  = array();
		
		//footer del blocco, linka a X per aggiungere report
		$url = new \moodle_url('/blocks/testblockz/managereports.php');
        $linktext = get_string('managereports', 'block_testblockz');
        $this->content-> footer =  \html_writer::link($url, $linktext);
			
		foreach ($results as $key => $object) {
			$this->content->items[] = html_writer::tag('a', $key, array('href' => '/lms/blocks/testblockz/viewreport.php'));
		}
        //$this->content->icons[] = html_writer::empty_tag('img', array('src' => 'images/icons/1.gif', 'class' => 'icon'));


        return $this->content;
    }

}


//Eccezione - Call to a member function get_user() on null

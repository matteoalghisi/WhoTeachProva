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
 * Dedication block definition.
 *
 * @package    block
 * @subpackage dedication
 * @copyright  2008 CICEI http://http://www.cicei.com
 * @author     2008 Borja Rubio Reyes
 *             2011 Aday Talavera Hierro (update to Moodle 2.x)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_dedication extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_dedication');
    }

    public function specialization() {
        // Previous block versions didn't have config settings.
        if ($this->config === null) {
            $this->config = new stdClass();
        }
        // Set always show_dedication config settings to avoid errors.
        if (!isset($this->config->show_dedication)) {
            $this->config->show_dedication = 0;
        }
    }

    public function get_content() {
        global $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if ($this->config->show_dedication == 1) {
            require_once('dedication_lib.php');
            $mintime = $this->page->course->startdate;
            $maxtime = time();
            $dm = new block_dedication_manager($this->page->course, $mintime, $maxtime, $this->config->limit);
        $dedicationtime = $dm->get_user_dedication($USER, true);
		
		
       	
		$this->content->text .= html_writer::tag('script', '', array(src=>'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'));

		$this->content->text .= html_writer::tag('script', '', array(src=>'https://www.whoteach.it/lms/blocks/dedication/jquery.idle.js'));

        $this->content->text .= html_writer::tag('script', '
		
		
					dateObj = new Date(' . $dedicationtime . ' * 1000);
                    hours = dateObj.getUTCHours();
                    minutes = dateObj.getUTCMinutes();
                    seconds = dateObj.getSeconds();

                    var d = new Date();
                    d.setHours(hours);
                    d.setMinutes(minutes);
                    d.setSeconds(seconds);
					
					
					function startInterval(){
					  interval = setInterval(appendDateToBody, 1000);
					}
					
					function appendDateToBody() {

						document.getElementById("h").innerHTML = d.getHours();
						document.getElementById("m").innerHTML = d.getMinutes();
						document.getElementById("s").innerHTML = d.getSeconds();
						d.setTime(d.getTime() + 1000);
					 
					}
					
					function stopInterval() {
						clearInterval(interval);
					}
					
					startInterval();
					 
					
					$(document).idle({
						onIdle: function() {
							//$("#myModal").show();
							stopInterval();
						},
						onActive: function() {
							//$("#myModal").hide();							
							startInterval();
						},

						events: "mousemove keydown mousedown touchstart scroll",
						idle: 5000
					})
					
					
                    /*var tiempo = setInterval(function () {
                      document.getElementById("h").innerHTML = d.getHours();
                      document.getElementById("m").innerHTML = d.getMinutes();
                      document.getElementById("s").innerHTML = d.getSeconds();
                      d.setTime(d.getTime() + 1000);
                    }, 1000);*/
					
					
		
				


                ');

        $this->content->text .= html_writer::tag('p', get_string('dedication_estimation', 'block_dedication'));
        //$this->content->text .= html_writer::tag('p', block_dedication_utils::format_dedication($dedicationtime));
        $lang = current_language();

        if($lang == 'it')
            $this->content->text .= html_writer::tag('p', '<div style="font-size:16px;"><span id="h" style="font-weight: 600; font-size: 18px;"></span> ore <span id="m" style="font-weight: 600; font-size: 18px;"></span> minuti <span id="s" style="font-weight: 600; font-size: 18px;"></span> secondi</div>');
        else
            $this->content->text .= html_writer::tag('p', '<div style="font-size:16px;"><span id="h" style="font-weight: 600; font-size: 18px;"></span> hours <span id="m" style="font-weight: 600; font-size: 18px;"></span> mins <span id="s" style="font-weight: 600; font-size: 18px;"></span> sec</div>');

        //$this->content->text .= html_writer::tag('p', '<div style="font-size:16px;"><span id="h" style="font-weight: 600; font-size: 18px;"></span> hours <span id="m" style="font-weight: 600; font-size: 18px;"></span> mins');
        }

        if (has_capability('block/dedication:use', context_block::instance($this->instance->id))) {
            $this->content->footer .= html_writer::tag('hr', null);
            $this->content->footer .= html_writer::tag('p', get_string('access_info', 'block_dedication'));
            $url = new moodle_url('/blocks/dedication/dedication.php', array(
                'courseid' => $this->page->course->id,
                'instanceid' => $this->instance->id,
            ));
            $this->content->footer .= $OUTPUT->single_button($url, get_string('access_button', 'block_dedication'), 'get');
        }
		
		
        return $this->content;
    }

    public function applicable_formats() {
        return array('course' => true);
    }
	
	

}

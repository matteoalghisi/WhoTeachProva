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
 * Chapter edit form
 *
 * @package    mod_recommend
 * @copyright  2004-2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class recommend_chapter_edit_form extends moodleform {

    function definition() {
        global $CFG;

        $chapter = $this->_customdata['chapter'];
        $options = $this->_customdata['options'];

        // Disabled subchapter option when editing first node.
        $disabledmsg = null;
        if ($chapter->pagenum == 1) {
            $disabledmsg = get_string('subchapternotice', 'recommend');
        }

        $mform = $this->_form;

        if (!empty($chapter->id)) {
            $mform->addElement('header', 'general', get_string('editingchapter', 'mod_recommend'));
        } else {
            $mform->addElement('header', 'general', get_string('addafter', 'mod_recommend'));
        }

        if (isset($chapter->currentchaptertitle)) {
            $mform->addElement('static', 'details',
                get_string('previouschapter', 'mod_recommend'),
                $chapter->currentchaptertitle
            );
        }

        $mform->addElement('text', 'title', get_string('chaptertitle', 'mod_recommend'), array('size'=>'30'));
        $mform->setType('title', PARAM_RAW);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'subchapter', get_string('subchapter', 'mod_recommend'), $disabledmsg);

        $mform->addElement('editor', 'content_editor', get_string('content', 'mod_recommend'), null, $options);
        $mform->setType('content_editor', PARAM_RAW);
        $mform->addRule('content_editor', get_string('required'), 'required', null, 'client');

        if (core_tag_tag::is_enabled('mod_recommend', 'recommend_chapters')) {
            $mform->addElement('header', 'tagshdr', get_string('tags', 'tag'));
        }
        $mform->addElement('tags', 'tags', get_string('tags'),
            array('itemtype' => 'recommend_chapters', 'component' => 'mod_recommend'));

		//$mform->addElement('submit', 'add_resources', 'add_resources', 'style="display:none"')
		
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'pagenum');
        $mform->setType('pagenum', PARAM_INT);

        $this->add_action_buttons(true);

        // set the defaults
        $this->set_data($chapter);
    }

    function definition_after_data(){
        $mform = $this->_form;
        $pagenum = $mform->getElement('pagenum');
        if ($pagenum->getValue() == 1) {
            $mform->hardFreeze('subchapter');
        }
    }
}

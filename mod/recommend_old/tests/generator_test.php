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
 * Genarator tests.
 *
 * @package    mod_recommend
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Genarator tests class.
 *
 * @package    mod_recommend
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_recommend_generator_testcase extends advanced_testcase {

    public function test_create_instance() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse($DB->record_exists('recommend', array('course' => $course->id)));
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $this->assertEquals(1, $DB->count_records('recommend', array('course' => $course->id)));
        $this->assertTrue($DB->record_exists('recommend', array('course' => $course->id, 'id' => $recommend->id)));

        $params = array('course' => $course->id, 'name' => 'One more recommend');
        $recommend = $this->getDataGenerator()->create_module('recommend', $params);
        $this->assertEquals(2, $DB->count_records('recommend', array('course' => $course->id)));
        $this->assertEquals('One more recommend', $DB->get_field_select('recommend', 'name', 'id = :id', array('id' => $recommend->id)));
    }

    public function test_create_chapter() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');

        $this->assertFalse($DB->record_exists('recommend_chapters', array('recommendid' => $recommend->id)));
        $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));
        $this->assertTrue($DB->record_exists('recommend_chapters', array('recommendid' => $recommend->id)));

        $chapter = $recommendgenerator->create_chapter(
            array('recommendid' => $recommend->id, 'content' => 'Yay!', 'title' => 'Oops', 'tags' => array('Cats', 'mice')));
        $this->assertEquals(2, $DB->count_records('recommend_chapters', array('recommendid' => $recommend->id)));
        $this->assertEquals('Oops', $DB->get_field_select('recommend_chapters', 'title', 'id = :id', array('id' => $chapter->id)));
        $this->assertEquals('Yay!', $DB->get_field_select('recommend_chapters', 'content', 'id = :id', array('id' => $chapter->id)));
        $this->assertEquals(array('Cats', 'mice'),
            array_values(core_tag_tag::get_item_tags_array('mod_recommend', 'recommend_chapters', $chapter->id)));

        $chapter = $recommendgenerator->create_content($recommend);
        $this->assertEquals(3, $DB->count_records('recommend_chapters', array('recommendid' => $recommend->id)));
    }

}

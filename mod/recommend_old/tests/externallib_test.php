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
 * External mod_recommend functions unit tests
 *
 * @package    mod_recommend
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External mod_recommend functions unit tests
 *
 * @package    mod_recommend
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_recommend_external_testcase extends externallib_advanced_testcase {

    /**
     * Test view_recommend
     */
    public function test_view_recommend() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));
        $chapterhidden = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id, 'hidden' => 1));

        $context = context_module::instance($recommend->cmid);
        $cm = get_coursemodule_from_instance('recommend', $recommend->id);

        // Test invalid instance id.
        try {
            mod_recommend_external::view_recommend(0);
            $this->fail('Exception expected due to invalid mod_recommend instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            mod_recommend_external::view_recommend($recommend->id, 0);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_recommend_external::view_recommend($recommend->id, 0);
        $result = external_api::clean_returnvalue(mod_recommend_external::view_recommend_returns(), $result);

        $events = $sink->get_events();
        $this->assertCount(2, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/recommend/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        $event = array_shift($events);
        $this->assertInstanceOf('\mod_recommend\event\chapter_viewed', $event);
        $this->assertEquals($chapter->id, $event->objectid);

        $result = mod_recommend_external::view_recommend($recommend->id, $chapter->id);
        $result = external_api::clean_returnvalue(mod_recommend_external::view_recommend_returns(), $result);

        $events = $sink->get_events();
        // We expect a total of 3 events.
        $this->assertCount(3, $events);

        // Try to view a hidden chapter.
        try {
            mod_recommend_external::view_recommend($recommend->id, $chapterhidden->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('errorchapter', $e->errorcode);
        }

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/recommend:read', CAP_PROHIBIT, $studentrole->id, $context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_recommend_external::view_recommend($recommend->id, 0);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

    }

    /**
     * Test get_recommends_by_courses
     */
    public function test_get_recommends_by_courses() {
        global $DB, $USER;
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course1 = self::getDataGenerator()->create_course();
        $recommendoptions1 = array(
                              'course' => $course1->id,
                              'name' => 'First recommend'
                             );
        $recommend1 = self::getDataGenerator()->create_module('recommend', $recommendoptions1);
        $course2 = self::getDataGenerator()->create_course();
        $recommendoptions2 = array(
                              'course' => $course2->id,
                              'name' => 'Second recommend'
                             );
        $recommend2 = self::getDataGenerator()->create_module('recommend', $recommendoptions2);
        $student1 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enroll Student1 in Course1.
        self::getDataGenerator()->enrol_user($student1->id,  $course1->id, $studentrole->id);
        $this->setUser($student1);

        $recommends = mod_recommend_external::get_recommends_by_courses();
        // We need to execute the return values cleaning process to simulate the web service server.
        $recommends = external_api::clean_returnvalue(mod_recommend_external::get_recommends_by_courses_returns(), $recommends);
        $this->assertCount(1, $recommends['recommends']);
        $this->assertEquals('First recommend', $recommends['recommends'][0]['name']);
        // We see 10 fields.
        $this->assertCount(10, $recommends['recommends'][0]);

        // As Student you cannot see some recommend properties like 'section'.
        $this->assertFalse(isset($recommends['recommends'][0]['section']));

        // Student1 is not enrolled in course2. The webservice will return a warning!
        $recommends = mod_recommend_external::get_recommends_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $recommends = external_api::clean_returnvalue(mod_recommend_external::get_recommends_by_courses_returns(), $recommends);
        $this->assertCount(0, $recommends['recommends']);
        $this->assertEquals(1, $recommends['warnings'][0]['warningcode']);

        // Now as admin.
        $this->setAdminUser();
        // As Admin we can see this recommend.
        $recommends = mod_recommend_external::get_recommends_by_courses(array($course2->id));
        // We need to execute the return values cleaning process to simulate the web service server.
        $recommends = external_api::clean_returnvalue(mod_recommend_external::get_recommends_by_courses_returns(), $recommends);

        $this->assertCount(1, $recommends['recommends']);
        $this->assertEquals('Second recommend', $recommends['recommends'][0]['name']);
        // We see 17 fields.
        $this->assertCount(17, $recommends['recommends'][0]);
        // As an Admin you can see some recommend properties like 'section'.
        $this->assertEquals(0, $recommends['recommends'][0]['section']);

        // Enrol student in the second course.
        self::getDataGenerator()->enrol_user($student1->id,  $course2->id, $studentrole->id);
        $this->setUser($student1);
        $recommends = mod_recommend_external::get_recommends_by_courses();
        $recommends = external_api::clean_returnvalue(mod_recommend_external::get_recommends_by_courses_returns(), $recommends);
        $this->assertCount(2, $recommends['recommends']);

    }
}

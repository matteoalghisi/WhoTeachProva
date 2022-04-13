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
 * Events tests.
 *
 * @package    mod_recommend
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Events tests class.
 *
 * @package    mod_recommend
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_recommend_events_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_chapter_created() {
        // There is no proper API to call to generate chapters for a recommend, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $context = context_module::instance($recommend->cmid);

        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));

        $event = \mod_recommend\event\chapter_created::create_from_chapter($recommend, $context, $chapter);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\chapter_created', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $expected = array($course->id, 'recommend', 'add chapter', 'view.php?id='.$recommend->cmid.'&chapterid='.$chapter->id,
            $chapter->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_chapter_updated() {
        // There is no proper API to call to generate chapters for a recommend, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $context = context_module::instance($recommend->cmid);

        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));

        $event = \mod_recommend\event\chapter_updated::create_from_chapter($recommend, $context, $chapter);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\chapter_updated', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $expected = array($course->id, 'recommend', 'update chapter', 'view.php?id='.$recommend->cmid.'&chapterid='.$chapter->id,
            $chapter->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_chapter_deleted() {
        // There is no proper API to call to delete chapters for a recommend, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $context = context_module::instance($recommend->cmid);

        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));

        $event = \mod_recommend\event\chapter_deleted::create_from_chapter($recommend, $context, $chapter);
        $legacy = array($course->id, 'recommend', 'update', 'view.php?id='.$recommend->cmid, $recommend->id, $recommend->cmid);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\chapter_deleted', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $this->assertEquals($chapter, $event->get_record_snapshot('recommend_chapters', $chapter->id));
        $this->assertEventLegacyLogData($legacy, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_module_instance_list_viewed() {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $params = array(
            'context' => context_course::instance($course->id)
        );
        $event = \mod_recommend\event\course_module_instance_list_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\course_module_instance_list_viewed', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $expected = array($course->id, 'recommend', 'view all', 'index.php?id='.$course->id, '');
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_module_viewed() {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($recommend->cmid),
            'objectid' => $recommend->id
        );
        $event = \mod_recommend\event\course_module_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\course_module_viewed', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($recommend->id, $event->objectid);
        $expected = array($course->id, 'recommend', 'view', 'view.php?id=' . $recommend->cmid, $recommend->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    public function test_chapter_viewed() {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $context = context_module::instance($recommend->cmid);

        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));

        $event = \mod_recommend\event\chapter_viewed::create_from_chapter($recommend, $context, $chapter);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_recommend\event\chapter_viewed', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $expected = array($course->id, 'recommend', 'view chapter', 'view.php?id=' . $recommend->cmid . '&amp;chapterid=' .
            $chapter->id, $chapter->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

}

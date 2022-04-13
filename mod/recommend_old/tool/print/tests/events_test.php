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
 * @package    recommendtool_print
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Events tests class.
 *
 * @package    recommendtool_print
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recommendtool_print_events_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_recommend_printed() {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $context = context_module::instance($recommend->cmid);

        $event = \recommendtool_print\event\recommend_printed::create_from_recommend($recommend, $context);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\recommendtool_print\event\recommend_printed', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($recommend->id, $event->objectid);
        $expected = array($course->id, 'recommend',  'print', 'tool/print/index.php?id=' . $recommend->cmid, $recommend->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }


    public function test_chapter_printed() {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $recommendgenerator = $this->getDataGenerator()->get_plugin_generator('mod_recommend');
        $chapter = $recommendgenerator->create_chapter(array('recommendid' => $recommend->id));
        $context = context_module::instance($recommend->cmid);

        $event = \recommendtool_print\event\chapter_printed::create_from_chapter($recommend, $context, $chapter);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\recommendtool_print\event\chapter_printed', $event);
        $this->assertEquals(context_module::instance($recommend->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $expected = array($course->id, 'recommend', 'print chapter', 'tool/print/index.php?id=' . $recommend->cmid .
            '&chapterid=' . $chapter->id, $chapter->id, $recommend->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

}

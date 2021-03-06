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
 * recommendtool_importhtml tests.
 *
 * @package    recommendtool_importhtml
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot.'/mod/recommend/tool/importhtml/locallib.php');

/**
 * recommendtool_importhtml tests class.
 *
 * @package    recommendtool_importhtml
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recommendtool_importhtml_locallib_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_import_chapters_events() {
        $course = $this->getDataGenerator()->create_course();
        $recommend = $this->getDataGenerator()->create_module('recommend', array('course' => $course->id));
        $context = context_module::instance($recommend->cmid);

        $record = new stdClass();
        $record->contextid = $context->id;
        $record->component = 'phpunit';
        $record->filearea = 'test';
        $record->itemid = 0;
        $record->filepath = '/';
        $record->filename = 'chapters.zip';

        $fs = get_file_storage();
        $file = $fs->create_file_from_pathname($record, __DIR__ . '/fixtures/chapters.zip');

        // Importing the chapters.
        $sink = $this->redirectEvents();
        toolrecommend_importhtml_import_chapters($file, 2, $recommend, $context, false);
        $events = $sink->get_events();

        // Checking the results.
        $this->assertCount(5, $events);
        foreach ($events as $event) {
            $this->assertInstanceOf('\mod_recommend\event\chapter_created', $event);
            $this->assertEquals($context, $event->get_context());
            $chapter = $event->get_record_snapshot('recommend_chapters', $event->objectid);
            $this->assertNotEmpty($chapter);
            $this->assertEventContextNotUsed($event);
        }
    }

}

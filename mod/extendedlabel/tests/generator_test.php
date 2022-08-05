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
 * PHPUnit extendedlabel generator tests
 *
 * @package    mod_extendedlabel
 * @category   phpunit
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit extendedlabel generator testcase
 *
 * @package    mod_extendedlabel
 * @category   phpunit
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_extendedlabel_generator_testcase extends advanced_testcase {
    public function test_generator() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertEquals(0, $DB->count_records('extendedlabel'));

        $course = $this->getDataGenerator()->create_course();

        /** @var mod_extendedlabel_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_extendedlabel');
        $this->assertInstanceOf('mod_extendedlabel_generator', $generator);
        $this->assertEquals('extendedlabel', $generator->get_modulename());

        $generator->create_instance(array('course'=>$course->id));
        $generator->create_instance(array('course'=>$course->id));
        $extendedlabel = $generator->create_instance(array('course'=>$course->id));
        $this->assertEquals(3, $DB->count_records('extendedlabel'));

        $cm = get_coursemodule_from_instance('extendedlabel', $extendedlabel->id);
        $this->assertEquals($extendedlabel->id, $cm->instance);
        $this->assertEquals('extendedlabel', $cm->modname);
        $this->assertEquals($course->id, $cm->course);

        $context = context_module::instance($cm->id);
        $this->assertEquals($extendedlabel->cmid, $context->instanceid);
    }
}

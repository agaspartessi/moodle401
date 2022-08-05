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
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_extendedlabel_activity_task
 */

/**
 * Define the complete extendedlabel structure for backup, with file and id annotations
 */
class backup_gflacsoslider_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $gflacsoslider = new backup_nested_element('gflacsoslider', array('id'), array(
            'name','slidespeed','slideinterval','slidenumber','slidermode','sliderautoplay','timecreated', 'timemodified'));


        $gflacsoslider_slides = new backup_nested_element('gflacsoslider_slides');

        $gflacsoslider_slide = new backup_nested_element('gflacsoslider_slide', array('id'), array(
            'timecreated', 'timemodified', 'bannernumber', 'bannercolor',
            'bannercolorselect', 'enablebanner', 'bannertitle', 'bannertitlesize',
            'bannertitlecolor', 'bannertitleselect','bannertext','bannertextsize',
            'bannertextcolor','bannertextcolorselect','bannerlinkurl','bannerlinkurltext',
            'bannervideo','bannervideotype','bannervideoautoplay','bannerimage'));

        
        // Build the tree

        $gflacsoslider->add_child($gflacsoslider_slides);
        $gflacsoslider_slides->add_child($gflacsoslider_slide);

        // Define sources
        $gflacsoslider->set_source_table('gflacsoslider', array('id' => backup::VAR_ACTIVITYID));

        $gflacsoslider_slide->set_source_sql('
            SELECT *
              FROM {gflacsoslider_slides}
             WHERE gflacsoslider = ?',
            array(backup::VAR_PARENTID),'bannernumber ASC');

        // Define id annotations
        // (none)

        // Define file annotations
        $gflacsoslider->annotate_files('mod_gflacsoslider', 'bannerimage', 'id'); 

        // Return the root element (gflacsoslider), wrapped into standard activity structure
        return $this->prepare_activity_structure($gflacsoslider);
    }
}

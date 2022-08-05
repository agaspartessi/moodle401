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
 * Define all the restore steps that will be used by the restore_url_activity_task
 */

/**
 * Structure step to restore one extendedlabel activity
 */
class restore_gflacsoslider_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('gflacsoslider', '/activity/gflacsoslider'); 

        $paths[] = new restore_path_element('gflacsoslider_slides', '/activity/gflacsoslider/gflacsoslider_slides/gflacsoslider_slide');

       

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_gflacsoslider($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        // insert the gflacsoslider record
        $newitemid = $DB->insert_record('gflacsoslider', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);

    }

    protected function process_gflacsoslider_slides($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->gflacsoslider = $this->get_new_parentid('gflacsoslider');
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $newitemid = $DB->insert_record('gflacsoslider_slides', $data);
        $this->set_mapping('gflacsoslider_slides', $oldid, $newitemid);
    }

     protected function after_execute() {
        $this->add_related_files('mod_gflacsoslider', 'bannerimage1', 'gflacsoslider_slides_bannerimage');
        $this->add_related_files('mod_gflacsoslider', 'bannerimage1', 'gflacsoslider_slide_bannerimage');
        $this->add_related_files('mod_gflacsoslider', 'bannerimage', 'gflacsoslider_slide_bannerimage');
                // Add gflacsoslider_slides related files, matching by itemname = 'gflacsoslider_slides'
    
    }

    protected function after_restore() {
        global $DB;
        $gflacsosliderid = $this->task->get_activityid();
        $context = context_module::instance($context = $this->task->get_moduleid());
        
        for($i = 1 ; $i <= 10 ; $i++){
            $draftitemid = $DB->get_field('gflacsoslider_slides', 'bannerimage', array('gflacsoslider' => $gflacsosliderid,'bannernumber' => $i));
            if ( !empty($draftitemid) && $draftitemid != 0)
                file_save_draft_area_files($draftitemid, $context->id, 'mod_gflacsoslider', 'bannerimage'.$i, 0, 
                                        array('accepted_types' => array('.png', '.jpg', '.gif'), 'subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 1, 'context' => $context));
        }
    }

}

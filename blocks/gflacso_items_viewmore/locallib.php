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
 * Private page module utility functions
 *
 * @package     mod_gflacsotext
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

//require_once("$CFG->libdir/filelib.php");
//require_once("$CFG->libdir/resourcelib.php");


function gflacso_text_viewmore_get_image_options($context = null) {
    global $CFG;
    $filemanageropts = array('accepted_types' => array('.png', '.jpg', '.gif'), 'subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 1, 'context' => $context);
    return $filemanageropts;
}

function gflacso_text_viewmore_get_editor_options($context = null) {
    global $CFG;
    $filemanageropts = array('accepted_types' => array('.png', '.jpg', '.gif'),'subdirs' => 0,'maxfiles' => EDITOR_UNLIMITED_FILES, 'context'=>$context);
    return $filemanageropts;
}

/**
 * File browsing support class
 */
class gflacsotext_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

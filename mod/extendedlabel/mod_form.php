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
 * Add extendedlabel form
 *
 * @package mod_extendedlabel
 * @copyright  2015 Cooperativa GENEOS (www.geneos.com.ar) - FLACSO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->dirroot.'/mod/extendedlabel/locallib.php');

class mod_extendedlabel_mod_form extends moodleform_mod {

    function definition() {

        $mform = $this->_form;
	$config = get_config('extendedlabel');
	$maxchars = $config->maxcharacters;
	if ($maxchars == null)
		$maxchars = 1500;
        $mform->addElement('header', 'generalhdr', get_string('general'));

	$mform->addElement('text','title',"Titulo",' size="10"');
        //$mform->addRule('title', get_string('maximumchars', '', 50), 'maxlength', 50, 'client');

	$mform->addElement('checkbox', 'display_title', get_string('display_title', 'extendedlabel'));
	$mform->setDefault('display_title', 0);

        $this->standard_intro_elements(get_string('extendedlabeltext', 'extendedlabel'));
        $mform->addRule('introeditor', get_string('maximumchars', '', $maxchars), 'maxlength', $maxchars, 'client');


        $mform->addElement('editor', 'fulleditor', get_string('extendedlabelfull', 'extendedlabel'), null, extendedlabel_get_editor_options($this->context));
        $mform->setType('fulleditor', PARAM_RAW);

        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons(true, false, null);

    }

    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('fulleditor');
            $default_values['fulleditor']['format'] = $default_values['fullformat'];
            $default_values['fulleditor']['text']   = file_prepare_draft_area($draftitemid, $this->context->id, 'mod_extendedlabel', 'full', 0, extendedlabel_get_editor_options($this->context), $default_values['full']);
            $default_values['fulleditor']['itemid'] = $draftitemid;
	    if (!empty($this->current->name))
	    	$default_values['title'] = $this->current->name;
        }
    } 

}

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
 * Add gflacsoslider form
 *
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->dirroot.'/mod/gflacsoslider/locallib.php');

class mod_gflacsoslider_mod_form extends moodleform_mod {

    function definition() {
    global $DB, $COURSE;
    global $PAGE;
       
    $mform = $this->_form;

    $mform->addElement('header', 'generalhdr', get_string('general'));

	$mform->addElement('text','name',"Titulo",'maxlength="50"  size="10"');
    $mform->addRule('name', get_string('maximumchars', '', 50), 'maxlength', 50, 'client');
    $mform->setType('name', PARAM_NOTAGS);

    // Set Number of Slides.
    $name = 'slidenumber';
    $title = get_string('slidenumber' , 'gflacsoslider');
    $default = '10';
    $mform->addElement('text',$name, $title,'maxlength="2"  size="4"');
    $mform->addRule($name,  get_string('numeric','gflacsoslider'), 'numeric', '', 'server', false, false);
    $mform->addHelpButton($name, 'slidenumber', 'gflacsoslider');
    $mform->setDefault($name, $default);
    $mform->setType($name, PARAM_INT);

    // Set the Slide Speed.
    $name = 'slidespeed';
    $title = get_string('slidespeed' , 'gflacsoslider');
    $default = '2000';
    $mform->addElement('text',$name, $title);
    $mform->addHelpButton($name, $name, 'gflacsoslider');
    $mform->setDefault($name, $default);
    $mform->setType($name, PARAM_INT);

    // Set the Slide interval.
    $name = 'slideinterval';
    $title = get_string('slideinterval' , 'gflacsoslider');
    $default = '4000';
    $mform->addElement('text',$name, $title);
    $mform->addHelpButton($name, $name, 'gflacsoslider');
    $mform->setDefault($name, $default);
    $mform->setType($name, PARAM_INT);

    // Set autoplay
    $name = 'sliderautoplay';
    $title = get_string('sliderautoplay', 'gflacsoslider');
    $default = true;
    $mform->addElement('checkbox', $name,  $title);
    $mform->addHelpButton($name, $name, 'gflacsoslider');
    $mform->setDefault($name, $default);
    $mform->setType($name, PARAM_INT);

    // Transition mode.
    $name = 'slidermode';
    $title = get_string('slidermode', 'gflacsoslider');
    $default = '2';
    $choices = array(
            '1'=>'vertical',
            '2'=>'horizontal',
            '3'=>'fade');
    $mform->addElement('select', $name, $title, $choices);
    $mform->addHelpButton($name, $name, 'gflacsoslider');
    $mform->setDefault($name, $default);
    $mform->setType($name, PARAM_INT);

    $slidesnumber = 0;

    if ( !empty($this->_instance) && 
            ( $result = $DB->get_records('gflacsoslider',array('id'=>$this->_instance)) ) ) {
            $slidesnumber = array_shift($result)->slidenumber;
    }
    for ($i = 1 ; $i<=$slidesnumber ; $i++) {

        $mform->addElement('header', 'slider'.$i, get_string('slide','gflacsoslider').$i);
         
        $mform->addElement('hidden', 'bannerid'.$i, 0);
        $mform->setType('bannerid'.$i, PARAM_INT);

        // Enables the slide.
        $name = 'enablebanner'.$i;
        $title = get_string('enablebanner', 'gflacsoslider');
        $default = false;
        $mform->addElement('checkbox', $name,  $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'enablebanner', 'gflacsoslider');

        // Slide Image.
        $name = 'bannerimage'.$i;
        $title = get_string('bannerimage', 'gflacsoslider');
        $mform->addElement('filemanager', $name, $title, null, gflacsoslider_get_editor_options());
        $mform->addHelpButton($name, 'bannerimage', 'gflacsoslider');
        
        // Slide Title Color.
        $name = 'bannercolorselect'.$i;
        $title = get_string('bannercolorselect' , 'gflacsoslider');
        $default = '0';
        $choices = array(
            '0'=>'Manual',
            '1'=>'Azul',
            '2'=>'Magenta',
            '3'=>'Naranja',
            '4'=>'Verde',
            '6'=>'Gris');
        $mform->addElement('select', $name, $title, $choices, array(''));
        $mform->addHelpButton($name, 'bannercolorselect', 'gflacsoslider');
        $mform->setDefault($name, $default);

        $name = 'bannercolor' . $i;
        $title = get_string('bannercolor', 'gflacsoslider');
        $default = '#000000';
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannercolor', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);
        
        // Slide Title.
        $name = 'bannertitle'.$i;
        $title = get_string('bannertitle', 'gflacsoslider');
        $default = $title.' '.$i;
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannertitle', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);

        // Slide Title Size.
        $name = 'bannertitlesize'.$i;
        $title = get_string('bannertitlesize', 'gflacsoslider');
        $default = 40;
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_INT);
        $mform->addHelpButton($name, 'bannertitlesize', 'gflacsoslider');

        // Slide Title Color.
        $name = 'bannertitlecolorselect'.$i;
        $title = get_string('bannertitlecolorselect' , 'gflacsoslider');
        $default = '0';
        $choices = array(
            '0'=>'Manual',
            '1'=>'Azul',
            '2'=>'Magenta',
            '3'=>'Naranja',
            '4'=>'Verde',
            '6'=>'Gris');
        $mform->addElement('select', $name, $title, $choices, array(''));
        $mform->addHelpButton($name, 'bannertitlecolorselect', 'gflacsoslider');
        $mform->setDefault($name, $default);

        $name = 'bannertitlecolor' . $i;
        $title = get_string('bannertitlecolor', 'gflacsoslider');
        $default = '#FFFFFF';
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannertitlecolor', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);
        
        // Slide text.
        $name = 'bannertext'.$i;
        $title = get_string('bannertext', 'gflacsoslider');
        $default = 'Bacon ipsum dolor sit amet turducken jerky beef ribeye boudin t-bone shank fatback pork loin pork. ';
        $mform->addElement('textarea',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannertext', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);

        // Slide Text Size.
        $name = 'bannertextsize'.$i;
        $title = get_string('bannertextsize', 'gflacsoslider');
        $default = 18;
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_INT);
        $mform->addHelpButton($name, 'bannertextsize', 'gflacsoslider');

        // Slide Text Color.
        $name = 'bannertextcolorselect'.$i;
        $title = get_string('bannertextcolorselect' , 'gflacsoslider');
        $default = '0';
        $choices = array(
            '0'=>'Manual',
            '1'=>'Azul',
            '2'=>'Magenta',
            '3'=>'Naranja',
            '4'=>'Verde',
            '6'=>'Gris');
        $mform->addElement('select', $name, $title, $choices, array(''));
        $mform->addHelpButton($name, 'bannertextcolorselect', 'gflacsoslider');
        $mform->setDefault($name, $default);

        $name = 'bannertextcolor' .$i;
        $title = get_string('bannertextcolor', 'gflacsoslider');
        $default = '#FFFFFF';
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name,'bannertextcolor', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);
   
        // Destination URL for Slide Link
        $name = 'bannerlinkurl'.$i;
        $title = get_string('bannerlinkurl', 'gflacsoslider');
        $default = '#';
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannerlinkurl', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);

        //Altenarte text for URL Button
        $name = 'bannerlinkurltext'.$i;
        $title = get_string('bannerlinkurltext', 'gflacsoslider');
        $default = 'Ver más';
        $mform->addElement('text',$name, $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannerlinkurltext', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);


        // Video type.
        $name = 'bannervideotype'.$i;
        $title = get_string('bannervideotype', 'gflacsoslider');
        $default = '1';
        $choices = array(
                '1'=>'Vimeo',
                '2'=>'Youtube');
        $mform->addElement('select', $name, $title, $choices);
        $mform->addHelpButton($name, 'bannervideotype', 'gflacsoslider');
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_INT);

        // Video ID
        $name = 'bannervideo'.$i;
        $title = get_string('bannervideo', 'gflacsoslider');
        $mform->addElement('text',$name, $title);
        $mform->addHelpButton($name, 'bannervideo', 'gflacsoslider');
        $mform->setType($name, PARAM_NOTAGS);

        // Enables the videoautoplay.
        $name = 'bannervideoautoplay'.$i;
        $title = get_string('bannervideoautoplay', 'gflacsoslider');
        $default = true;
        $mform->addElement('checkbox', $name,  $title);
        $mform->setDefault($name, $default);
        $mform->addHelpButton($name, 'bannervideoautoplay', 'gflacsoslider');


    }

    // buttons
    $this->standard_coursemodule_elements();

    $this->add_action_buttons();

}

    function data_preprocessing(&$default_values) {
	    global $DB;
        if ( !empty($this->_instance) && 
                ( $result = $DB->get_records('gflacsoslider_slides',array('gflacsoslider'=>$this->_instance)) ) ) {
            $i = 1;
            foreach (array_keys($result) as $key){
                $default_values['bannercolor'.$i] = $result[$key]->bannercolor;
                $default_values['bannercolorselect'.$i] = $result[$key]->bannercolorselect;
                $default_values['bannertext'.$i] = $result[$key]->bannertext;
                $default_values['enablebanner'.$i] = $result[$key]->enablebanner;
                $default_values['bannertextsize'.$i] = $result[$key]->bannertextsize;
                $default_values['bannertextcolor'.$i] = $result[$key]->bannertextcolor;
                $default_values['bannertextcolorselect'.$i] = $result[$key]->bannertextcolorselect;
                $default_values['bannertitle'.$i] = $result[$key]->bannertitle;
                $default_values['bannertitlesize'.$i] = $result[$key]->bannertitlesize;
                $default_values['bannertitlecolor'.$i] = $result[$key]->bannertitlecolor;
                $default_values['bannertitlecolorselect'.$i] = $result[$key]->bannertitlecolorselect;
                $default_values['bannerlinkurl'.$i] = $result[$key]->bannerlinkurl;
                $default_values['bannerlinkurltext'.$i] = $result[$key]->bannerlinkurltext;
                $default_values['bannerid'.$i] = $result[$key]->id;
                $default_values['bannervideo'.$i] = $result[$key]->bannervideo;
                $default_values['bannervideotype'.$i] = $result[$key]->bannervideotype;
                $default_values['bannervideoautoplay'.$i] = $result[$key]->bannervideoautoplay;

                $draftitemid = file_get_submitted_draft_itemid('bannerimage'.$i);
                //$draftitemid = $result[$key]->bannerimage;
                // editing existing instance - copy existing files into draft area
                file_prepare_draft_area($draftitemid, $this->context->id, 'mod_gflacsoslider', 'bannerimage'.$i, 0, gflacsoslider_get_editor_options());
                $default_values['bannerimage'.$i] = $draftitemid;
                $i++;    
            }

        }
    } 

}

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
 * Library of functions and constants for module gflacsoslider
 *
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/*
    '1'=>'Azul'
    '2'=>'Magenta'
    '3'=>'Naranja'
    '4'=>'Verde'
    '5'=>'Gris'
 */
function gflacsoslider_getselectedcolor($number){
    switch ($number) {
    case 0:
        return false;
    case 1:
        return "#002e4d";
    case 2:
        return "#930033";
    case 3:
        return "#dc522a";
    case 4:
        return "#437226";
    case 5:
        return "#999999";
    }
    return false;

}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $extendedlabel
 * @return bool|int
 */
function gflacsoslider_add_instance($gflacsoslider, $mform = null) {
    global $DB;

    $gflacsoslider->timemodified = time(); 
    $gflacsoslider->timecreated = time();
    
    $cmid = $gflacsoslider->coursemodule;

    $global_config = get_config('mod_gflacsoslider');
    $slidesnumber = ($gflacsoslider->slidenumber);
    if (isset($global_config->maxslides) && (intval($slidesnumber) > intval($global_config->maxslides)) )
       $slidesnumber = $global_config->maxslides;


    $gflacsoslider->id = $DB->insert_record("gflacsoslider", $gflacsoslider);

    return $gflacsoslider->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $extendedlabel
 * @return bool
 */
function gflacsoslider_update_instance($gflacsoslider) {
    global $DB;
   

    $cmid        = $gflacsoslider->coursemodule;

    $gflacsoslider->timemodified = time();
    $gflacsoslider->id           = $gflacsoslider->instance;

    //Check if autoplay is defined
    $gflacsoslider->sliderautoplay = empty($gflacsoslider->sliderautoplay) ? 0 : 1;

    $firstSave=$DB->update_record("gflacsoslider", $gflacsoslider);


    $global_config = get_config('mod_gflacsoslider');
    $slidesnumber = ($gflacsoslider->slidenumber);
    if (isset($global_config->maxslides) && (intval($slidesnumber) > intval($global_config->maxslides)) )
       $slidesnumber = $global_config->maxslides;

    //Add slides
    for($i = 1 ; $i <= $slidesnumber ; $i++){
        $slide = new stdClass();
        $slide->id = $gflacsoslider->{'bannerid'.$i};
        $slide->bannercolor = $gflacsoslider->{'bannercolor'.$i};
	$slide->bannercolorselect = $gflacsoslider->{'bannercolorselect'.$i};
        $slide->bannertitle = $gflacsoslider->{'bannertitle'.$i};
        $slide->bannertitlesize = $gflacsoslider->{'bannertitlesize'.$i};
        $slide->bannertitlecolor = $gflacsoslider->{'bannertitlecolor'.$i};
	$slide->bannertitleselect = $gflacsoslider->{'bannertitlecolorselect'.$i};
        $slide->bannertext = $gflacsoslider->{'bannertext'.$i};
        $slide->bannertextsize = $gflacsoslider->{'bannertextsize'.$i};
        $slide->bannertextcolor = $gflacsoslider->{'bannertextcolor'.$i};
	$slide->bannertextcolorselect = $gflacsoslider->{'bannertextcolorselect'.$i};
        $slide->bannerlinkurl = $gflacsoslider->{'bannerlinkurl'.$i};
        $slide->bannerlinkurltext = $gflacsoslider->{'bannerlinkurltext'.$i};
        $slide->enablebanner = empty($gflacsoslider->{'enablebanner'.$i}) ? 0 : 1;
        $slide->bannervideo = $gflacsoslider->{'bannervideo'.$i};
        $slide->bannervideotype = $gflacsoslider->{'bannervideotype'.$i};
        $slide->bannervideoautoplay = empty($gflacsoslider->{'bannervideoautoplay'.$i}) ? 0 : 1;
        $slide->bannernumber = $i;
        $slide->timemodified =  time();

        //Manage file upload
        $config_slide = 'bannerimage'.$i;
        $draftitemid = $gflacsoslider->{$config_slide};
        $context = context_module::instance($cmid);
        $DB->set_field('course_modules', 'instance', $gflacsoslider->id, array('id'=>$cmid));
        file_save_draft_area_files($draftitemid, $context->id, 'mod_gflacsoslider', $config_slide, 0, gflacsoslider_get_editor_options($context));        
        $slide->bannerimage = $draftitemid;
        if (!empty ($slide->id)) {
            if ( !$DB->update_record('gflacsoslider_slides', $slide) )
                return false;
        }
        else {
            $slide->timecreated =  time();
            $slide->gflacsoslider = $gflacsoslider->id;
            if ( !($gflacsoslider->bannerid[$i] = $DB->insert_record('gflacsoslider_slides', $slide)) )
                return false;
        }
    }
    return $firstSave;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function gflacsoslider_delete_instance($id) {
    global $DB;
    if (! $gflacsoslider = $DB->get_record("gflacsoslider", array("id"=>$id))) {
        return false;
    }

    $result = true;
    if (! $DB->delete_records("gflacsoslider_slides", array("gflacsoslider"=>$gflacsoslider->id))) {
        $result = false;
    }


    if (! $DB->delete_records("gflacsoslider", array("id"=>$gflacsoslider->id))) {
        $result = false;
    }
    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return cached_cm_info|null
 */
function gflacsoslider_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($gflacsoslider = $DB->get_record('gflacsoslider', array('id'=>$coursemodule->instance), 'id, name')) {
        if (empty($gflacsoslider->name)) {
            // gflacsoslider name missing, fix it
            $gflacsoslider->name = "gflacsoslider{$gflacsoslider->id}";
            $DB->set_field('gflacsoslider', 'name', $gflacsoslider->name, array('id'=>$gflacsoslider->id));
        }
        $info = new cached_cm_info();
        // no filtering hre because this info is cached and filtered later
        $info->name  = $gflacsoslider->name;
        return $info;
    } else {
        return null;
    }
}




/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function gflacsoslider_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function gflacsoslider_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */
function gflacsoslider_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return false;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_NO_VIEW_LINK:            return true;

        default: return null;
    }
}


/**
 * Lists all browsable file areas
 *
 * @package  extendedlabel_page
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function gflacsoslider_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['bannerimage1'] = 'bannerimage1';
    $areas['bannerimage2'] = 'bannerimage2';
    $areas['bannerimage3'] = 'bannerimage3';
    $areas['bannerimage4'] = 'bannerimage4';
    $areas['bannerimage5'] = 'bannerimage5';
    $areas['bannerimage6'] = 'bannerimage6';
    $areas['bannerimage7'] = 'bannerimage7';
    $areas['bannerimage8'] = 'bannerimage8';
    $areas['bannerimage9'] = 'bannerimage9';
    $areas['bannerimage10'] = 'bannerimage10';

    return $areas;
}

/**
 * File browsing support for extendedlabel module full area.
 *
 * @package  extendedlabel_page
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function gflacsoslider_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();
    if (stristr($filearea, 'bannerimage')) {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_gflacsoslider', $filearea, 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_gflacsoslider', $filearea, 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/page/locallib.php");
        return new page_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: page_intro handled in file_browser automatically

    return null;
}

function mod_gflacsoslider_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }
    require_login();
    if (stristr($filearea, 'bannerimage') === FALSE) {
        return false;
    }

    $itemid = (int)array_shift($args);
    if ($itemid != 0) {
        return false;
    }
    $fs = get_file_storage();
    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }
    $file = $fs->get_file($context->id, 'mod_gflacsoslider', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    // finally send the file
    send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
}


    function slider_build_url($id,$type){
        if ($type == 'vimeo')
            return 'https://player.vimeo.com/video/'.$id;
        if ($type == 'youtube')
            return 'https://www.youtube.com/embed/'.$id.'?enablejsapi=1';
        return 'null';
        }

    /**
     * Adds information about unread messages, that is only required for the course view page (and
     * similar), to the course-module object.
     * @param cm_info $cm Course-module object
     */
    function gflacsoslider_cm_info_view(cm_info $cm) {
        global $CFG,$DB,$PAGE;
        

        $fs = get_file_storage();
	    $context = context_module::instance($cm->id);
        $id = $cm->instance;
        //Start builing the slider

        /* General settings */
        $gflacsoslider = $DB->get_record("gflacsoslider", array("id"=>$id));
        if (empty($gflacsoslider))
            $content = '';
        else {
            $slidemode = $gflacsoslider->slidermode == 1 ? 'vertical' :  $gflacsoslider->slidermode == 2 ? 'horizontal' : 'fade';
            $slidenumber = $gflacsoslider->slidenumber;
            $slidespeed = (empty($slidespeed->slidespeed)) ? 2000 : $slidespeed->slidespeed;
            $slideinterval = (empty($gflacsoslider->slideinterval)) ? 4000 : $gflacsoslider->slideinterval;
            $slideautoplay = (empty($gflacsoslider->sliderautoplay)) ? 0 : 1;


            $slides = $DB->get_records('gflacsoslider_slides',array('gflacsoslider'=>$id));
            $i = 1;
            foreach (array_keys($slides) as $key){
                $slidecolor[$i] = gflacsoslider_getselectedcolor($slides[$key]->bannercolorselect) ? gflacsoslider_getselectedcolor($slides[$key]->bannercolorselect) : $slides[$key]->bannercolor;

                $hasslide[$i] = (empty($slides[$key]->enablebanner) || ($slides[$key]->enablebanner == 0) ) ? false : true;
                $fileurl = null;
                if ($files = $fs->get_area_files($context->id, 'mod_gflacsoslider', 'bannerimage'.$i, '0', 'sortorder', false)) {
                    // Build the File URL. Long process! But extremely accurate.
                    
                    foreach ($files as $file) {

                        $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                    }
                }

                $hasslideimage[$i] = (!empty($fileurl));
                if ($hasslideimage[$i]) {
                    $slideimage[$i] = $fileurl;
                }
                $slidetitle[$i] = (empty($slides[$key]->bannertitle)) ? false : $slides[$key]->bannertitle;
                $slidetitlesize[$i] = (empty($slides[$key]->bannertitlesize)) ? 40 : $slides[$key]->bannertitlesize;
               //$slidetitlecolor[$i] = (empty($slides[$key]->bannertitlecolor)) ? "#FFFFF" : $slides[$key]->bannertitlecolor;
		$slidetitlecolor[$i] = gflacsoslider_getselectedcolor($slides[$key]->bannertitleselect) ? gflacsoslider_getselectedcolor($slides[$key]->bannertitleselect) : $slides[$key]->bannertitlecolor;


                $slidecaption[$i] = (empty($slides[$key]->bannertext)) ? false : $slides[$key]->bannertext;
                $slidecaptionsize[$i] = (empty($slides[$key]->bannertextsize)) ? 20 : $slides[$key]->bannertextsize;
                //$slidecaptioncolor[$i] = (empty($slides[$key]->bannertextcolor)) ? "#FFFFFF" : $slides[$key]->bannertextcolor;
		$slidecaptioncolor[$i] = gflacsoslider_getselectedcolor($slides[$key]->bannertextcolorselect) ? gflacsoslider_getselectedcolor($slides[$key]->bannertextcolorselect) : $slides[$key]->bannertextcolor;
                $slideurl[$i] = (empty($slides[$key]->bannerlinkurl)) ? false : $slides[$key]->bannerlinkurl;
                $slideurltext[$i] = (empty($slides[$key]->bannerlinkurltext)) ? 'Ver más' : $slides[$key]->bannerlinkurltext;
                
                $slidevideoautoplay[$i] = (!empty($slides[$key]->bannervideoautoplay)) ? true : false;

                $hasslidevideo[$i] = (!empty($slides[$key]->bannervideo));
                if ($hasslidevideo[$i]) {
                    $slidevideo[$i] = $slides[$key]->bannervideo;
                    $slidevideotype[$i] = $slides[$key]->bannervideotype == 1 ? 'vimeo' : 'youtube';
                }
                $i++;
            }
            $hasslideshow = ($hasslide[1] || $hasslide[2] || $hasslide[3] || $hasslide[4] || $hasslide[5] || $hasslide[6] || $hasslide[7] || $hasslide[8] || $hasslide[9] || $hasslide[10] ) ? true : false;

            $sliderCount = 0;

            ob_start();  
            require_once("$CFG->dirroot/mod/gflacsoslider/layout/scripts.php");
            require("$CFG->dirroot/mod/gflacsoslider/layout/slider.php");
            //Ends building the slider
            $content = ob_get_clean();
        }
        $cm->set_content($content);
    }


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
 * Library of functions and constants for module extendedlabel
 *
 * @package mod_extendedlabel
 * @copyright  2015 Cooperativa GENEOS (www.geneos.com.ar) - FLACSO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/** extendedlabel_MAX_NAME_LENGTH = 50 */
define("extendedlabel_MAX_NAME_LENGTH", 50);

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
function extendedlabel_add_instance($extendedlabel, $mform = null) {
    global $DB;

    $extendedlabel->timemodified = time(); 
    
    $cmid = $extendedlabel->coursemodule;

    if ($mform) {
        $extendedlabel->full       = $extendedlabel->fulleditor['text'];
        $extendedlabel->fullformat = $extendedlabel->fulleditor['format'];
    }

    $extendedlabel->id = $DB->insert_record("extendedlabel", $extendedlabel);

    if (empty($extendedlabel->title))
	$extendedlabel->name = get_string('defaulttitle', 'extendedlabel').'_'.$extendedlabel->id;
    else
        $extendedlabel->name = $extendedlabel->title;

    $DB->set_field('course_modules', 'instance', $extendedlabel->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);
    if ($mform and !empty($extendedlabel->fulleditor['itemid'])) {
        $draftitemid = $extendedlabel->fulleditor['itemid'];
        $extendedlabel->full = file_save_draft_area_files($draftitemid, $context->id, 'mod_extendedlabel', 'full',0, extendedlabel_get_editor_options($context), $extendedlabel->full);

        $DB->update_record('extendedlabel', $extendedlabel);
    }
    return $extendedlabel->id;
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
function extendedlabel_update_instance($extendedlabel) {
    global $DB;

    $cmid        = $extendedlabel->coursemodule;
    $draftitemid = $extendedlabel->fulleditor['itemid'];

    $extendedlabel->timemodified = time();
    $extendedlabel->id           = $extendedlabel->instance;

    $extendedlabel->full       = $extendedlabel->fulleditor['text'];
    $extendedlabel->fullformat = $extendedlabel->fulleditor['format'];
	
    if (empty($extendedlabel->title))
	$extendedlabel->name = get_string('defaulttitle', 'extendedlabel').'_'.$extendedlabel->id;
    else
        $extendedlabel->name = $extendedlabel->title;

    if(!isset($extendedlabel->display_title))
    	$extendedlabel->display_title = false;

    $firstSave=$DB->update_record("extendedlabel", $extendedlabel);

    if ($draftitemid) {
        $context = context_module::instance($cmid);
	$extendedlabel->full =file_save_draft_area_files($draftitemid, $context->id, 'mod_extendedlabel', 'full', 0, extendedlabel_get_editor_options($context), $extendedlabel->full);

        return $DB->update_record('extendedlabel', $extendedlabel);
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
function extendedlabel_delete_instance($id) {
    global $DB;

    if (! $extendedlabel = $DB->get_record("extendedlabel", array("id"=>$id))) {
        return false;
    }

    $result = true;

    if (! $DB->delete_records("extendedlabel", array("id"=>$extendedlabel->id))) {
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
function extendedlabel_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($extendedlabel = $DB->get_record('extendedlabel', array('id'=>$coursemodule->instance), 'id, name,full,fullformat, intro, introformat')) {
        if (empty($extendedlabel->name)) {
            // extendedlabel name missing, fix it
            $extendedlabel->name = "extendedlabel{$extendedlabel->id}";
            $DB->set_field('extendedlabel', 'name', $extendedlabel->name, array('id'=>$extendedlabel->id));
        }
        $info = new cached_cm_info();
        // no filtering hre because this info is cached and filtered later
        $info->content = format_module_intro('extendedlabel', $extendedlabel, $coursemodule->id, false);
        $info->name  = $extendedlabel->name;
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
function extendedlabel_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function extendedlabel_get_extra_capabilities() {
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
function extendedlabel_supports($feature) {
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
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function extendedlabel_dndupload_register() {
    $strdnd = get_string('dnduploadextendedlabel', 'mod_extendedlabel');
    if (get_config('extendedlabel', 'dndmedia')) {
        $mediaextensions = file_get_typegroup('extension', 'web_image');
        $files = array();
        foreach ($mediaextensions as $extn) {
            $extn = trim($extn, '.');
            $files[] = array('extension' => $extn, 'message' => $strdnd);
        }
        $ret = array('files' => $files);
    } else {
        $ret = array();
    }

    $strdndtext = get_string('dnduploadextendedlabeltext', 'mod_extendedlabel');
    return array_merge($ret, array('types' => array(
        array('identifier' => 'text/html', 'message' => $strdndtext, 'noname' => true),
        array('identifier' => 'text', 'message' => $strdndtext, 'noname' => true)
    )));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function extendedlabel_dndupload_handle($uploadinfo) {
    global $USER;
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;

    // Extract the first (and only) file from the file area and add it to the extendedlabel as an img tag.
    if (!empty($uploadinfo->draftitemid)) {
        $fs = get_file_storage();
        $draftcontext = context_user::instance($USER->id);
        $context = context_module::instance($uploadinfo->coursemodule);
        $files = $fs->get_area_files($draftcontext->id, 'user', 'draft', $uploadinfo->draftitemid, '', false);
        if ($file = reset($files)) {
            if (file_mimetype_in_typegroup($file->get_mimetype(), 'web_image')) {
                // It is an image - resize it, if too big, then insert the img tag.
                $config = get_config('extendedlabel');
                $data->intro = extendedlabel_generate_resized_image($file, $config->dndresizewidth, $config->dndresizeheight);
            } else {
                // We aren't supposed to be supporting non-image types here, but fallback to adding a link, just in case.
                $url = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
                $data->intro = html_writer::link($url, $file->get_filename());
            }
            $data->intro = file_save_draft_area_files($uploadinfo->draftitemid, $context->id, 'mod_extendedlabel', 'full', 0,
                                                      null, $data->intro);
        }
    } else if (!empty($uploadinfo->content)) {
        $data->intro = $uploadinfo->content;
        if ($uploadinfo->type != 'text/html') {
            $data->introformat = FORMAT_PLAIN;
        }
    }

    return extendedlabel_add_instance($data, null);
}

/**
 * Resize the image, if required, then generate an img tag and, if required, a link to the full-size image
 * @param stored_file $file the image file to process
 * @param int $maxwidth the maximum width allowed for the image
 * @param int $maxheight the maximum height allowed for the image
 * @return string HTML fragment to add to the extendedlabel
 */
function extendedlabel_generate_resized_image(stored_file $file, $maxwidth, $maxheight) {
    global $CFG;

    $fullurl = moodle_url::make_draftfile_url($file->get_itemid(), $file->get_filepath(), $file->get_filename());
    $link = null;
    $attrib = array('alt' => $file->get_filename(), 'src' => $fullurl);

    if ($imginfo = $file->get_imageinfo()) {
        // Work out the new width / height, bounded by maxwidth / maxheight
        $width = $imginfo['width'];
        $height = $imginfo['height'];
        if (!empty($maxwidth) && $width > $maxwidth) {
            $height *= (float)$maxwidth / $width;
            $width = $maxwidth;
        }
        if (!empty($maxheight) && $height > $maxheight) {
            $width *= (float)$maxheight / $height;
            $height = $maxheight;
        }

        $attrib['width'] = $width;
        $attrib['height'] = $height;

        // If the size has changed and the image is of a suitable mime type, generate a smaller version
        if ($width != $imginfo['width']) {
            $mimetype = $file->get_mimetype();
            if ($mimetype === 'image/gif' or $mimetype === 'image/jpeg' or $mimetype === 'image/png') {
                require_once($CFG->libdir.'/gdlib.php');
                $data = $file->generate_image_thumbnail($width, $height);

                if (!empty($data)) {
                    $fs = get_file_storage();
                    $record = array(
                        'contextid' => $file->get_contextid(),
                        'component' => $file->get_component(),
                        'filearea'  => $file->get_filearea(),
                        'itemid'    => $file->get_itemid(),
                        'filepath'  => '/',
                        'filename'  => 's_'.$file->get_filename(),
                    );
                    $smallfile = $fs->create_file_from_string($record, $data);
		    print_r('asd');
		    die();
                    // Replace the image 'src' with the resized file and link to the original
                    $attrib['src'] = moodle_url::make_draftfile_url($smallfile->get_itemid(), $smallfile->get_filepath(),
                                                                    $smallfile->get_filename());
                    $link = $fullurl;
                }
            }
        }

    } else {
        // Assume this is an image type that get_imageinfo cannot handle (e.g. SVG)
        $attrib['width'] = $maxwidth;
    }

    $img = html_writer::empty_tag('img', $attrib);
    if ($link) {
        return html_writer::link($link, $img);
    } else {
        return $img;
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
function extendedlabel_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['full'] = get_string('extendedlabelfull', 'extendedlabel');
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
function extendedlabel_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'full') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_extendedlabel', 'full', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_extendedlabel', 'full', 0);
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

/**
 * Serves the page files.
 *
 * @package  mod_page
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function extendedlabel_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    /*if (!has_capability('mood/page:view', $context)) {
        return false;
    }*/

    if ($filearea !== 'full') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    // $arg could be revision number or index.html
    $arg = array_shift($args);
    if ($arg == 'index.html' || $arg == 'index.htm') {
        // serve page content
        $filename = $arg;

        if (!$page = $DB->get_record('page', array('id'=>$cm->instance), '*', MUST_EXIST)) {
            return false;
        }

        // remove @@PLUGINFILE@@/
        $content = str_replace('@@PLUGINFILE@@/', '', $page->content);

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;
        $content = format_text($content, $page->contentformat, $formatoptions);

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_extendedlabel/$filearea/0/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
           /* $page = $DB->get_record('page', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($page->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }*/
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_extendedlabel', 'full', 0)) {
                return false;
            }
            //file migrate - update flag
            /*$page->legacyfileslast = time();
            $DB->update_record('page', $page);*/
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);
    }
}

    /**
     * Adds information about unread messages, that is only required for the course view page (and
     * similar), to the course-module object.
     * @param cm_info $cm Course-module object
     */
    function extendedlabel_cm_info_view(cm_info $cm) {
        global $CFG,$DB;
        //$id = $cm->__get('id');
        $content = $cm->content;
	$context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $id = $cm->instance;
        $extendedlabel = $DB->get_record("extendedlabel", array("id"=>$id));
        $full = file_rewrite_pluginfile_urls($extendedlabel->full, 'pluginfile.php', $context->id, 'mod_extendedlabel', 'full',0);
        $title = ($extendedlabel->display_title == 1) ? '<p class="title">'.$extendedlabel->name.'</p>' : '';
        if (!empty($full)) {
            $script = '
            <script> 
               function mostrarFull'.$id.'(){
                    var full = document.getElementById("full_extendedlabel_'.$id.'");
                    var resumen = document.getElementById("intro_extendedlabel_'.$id.'");
                    var seemoreless = document.getElementById("seemoreless_'.$id.'");
                    if (full.style.display == \'block\'){
                        full.style.display = \'none\';
                        resumen.style.display = \'block\';
                        seemoreless.innerHTML = \''.get_string('seemorelabel', 'extendedlabel').'\';
			seemoreless.className = \'seemoreless more\';
                    }
                    else {
                        full.style.display = \'block\';
                        resumen.style.display = \'none\';
                        seemoreless.innerHTML = \''.get_string('seelesslabel', 'extendedlabel').'\';
			seemoreless.className = \'seemoreless less\';
                    }
               }
            </script>';
            $cm->set_content($script.$title.'<div id="intro_extendedlabel_'.$id.'">'.$content.'</div>'.'<div class="no-overflow" style="display:none" id="full_extendedlabel_'.$id.'">'.$full.'</div><a class="seemoreless more" id="seemoreless_'.$id.'" onclick="mostrarFull'.$id.'()">'.get_string('seemorelabel', 'extendedlabel').'</a>');
        }

    }


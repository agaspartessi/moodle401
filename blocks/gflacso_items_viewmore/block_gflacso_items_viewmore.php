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
 * Newblock block caps.
 *
 * @package    block_text_viewmore
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_gflacso_items_viewmore extends block_base {

    function init() {
        $this->title = get_string('defaulttitle', 'block_gflacso_items_viewmore');
    }

    private function setFiles($config,$fieldName){
        $return = false;
        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        if ($this->content_is_trusted()) {
            // fancy html allowed only on course, category and system blocks.
            $filteropt->noclean = true;
        }
        if (isset($config->{$fieldName}) && !empty($config->{$fieldName}['text']) ){
            // rewrite url
            $config->{$fieldName}['text'] = file_rewrite_pluginfile_urls($config->{$fieldName}['text'], 'pluginfile.php', $this->context->id, 'block_gflacso_items_viewmore', $fieldName, NULL);
            // Default to FORMAT_HTML which is what will have been used before the
            // editor was properly implemented for the block.
            $format = FORMAT_HTML;
            // Check to see if the format has been properly set on the config
            if (isset($config->{$fieldName}['format'])) {
                $format = $config->{$fieldName}['format'];
            }
            $return = format_text($config->{$fieldName}['text'], $format, $filteropt);
        }
        return $return;
    }

    public function get_content() {
        global $CFG;
        global $PAGE;


        require_once($CFG->libdir . '/filelib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $fs = get_file_storage();

        $itemsnumber = isset($this->config->itemsnumber) ? $this->config->itemsnumber : 0;
        $qtyshow = isset($this->config->qtyshow) ? $this->config->qtyshow : 0;
        $global_config = get_config('block_gflacso_items_viewmore');
        $additionaltextitems = (isset($global_config->maxadditionaltextitems) && !empty($global_config->maxadditionaltextitems) ) ? intval($global_config->maxadditionaltextitems) : 1;


        for ($i = 1 ; $i<= $itemsnumber ; $i++) {
            $fieldName = 'description'.$i;
            $vars[$i]['descriptionfull'.$i] = self::setFiles($this->config,$fieldName);

            $fieldName = 'descriptionshort'.$i;
            $vars[$i][$fieldName] = self::setFiles($this->config,$fieldName);
            
            for ($j=1 ; $j <= $additionaltextitems ; $j++ ) {
                $fieldName = 'text'.$j.'short'.$i;
                $vars[$i][$fieldName] = self::setFiles($this->config,$fieldName);

                $fieldName = 'text'.$j.'full'.$i;
                $vars[$i][$fieldName] = self::setFiles($this->config,$fieldName);
            }

            $fieldName = 'image'.$i;
            $fileurl = null;
            if ($files = $fs->get_area_files($this->context->id, 'block_gflacso_items_viewmore', $fieldName, '0', 'sortorder', false)) {
                // Build the File URL. Long process! But extremely accurate.
                
                foreach ($files as $file) {
                    $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), null, $file->get_filepath(), $file->get_filename());
                }
            }
            $vars[$i][$fieldName] = false;
            $hasitemimage = (!empty($fileurl));
            if ($hasitemimage) {
                $vars[$i][$fieldName] = $fileurl;
            }

            $fieldName = 'link'.$i;
            $vars[$i][$fieldName] = false;

            if (isset($this->config->{$fieldName}) && !empty($this->config->{$fieldName}) ){
                $vars[$i][$fieldName] = $this->config->{$fieldName};

                $fieldName = 'linktext'.$i;
                $vars[$i][$fieldName] = get_string('linktextdefault','block_gflacso_items_viewmore');
                if (isset($this->config->{$fieldName}) && !empty($this->config->{$fieldName}) ){
                    $vars[$i][$fieldName] = $this->config->{$fieldName};
                }
            } 

            $fieldName = 'title'.$i;
            $vars[$i][$fieldName] = false;

            if (isset($this->config->{$fieldName}) && !empty($this->config->{$fieldName}) ){
                $vars[$i][$fieldName] = $this->config->{$fieldName};
            } 


        }

        ob_start();  
        require_once("$CFG->dirroot/blocks/gflacso_items_viewmore/layout/scripts.php");
        require("$CFG->dirroot/blocks/gflacso_items_viewmore/layout/item.php");
        //Ends building the view
        $content = ob_get_clean();
        $this->content->text = $content;

        unset($filteropt); // memory footprint

        return $this->content;
    }


    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_gflacso_items_viewmore');            
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function instance_allow_multiple() {
      return false;
    }

    function has_config() {
        return true;
    }

    public function gflacso_items_buildShortFullContainer($fieldName,$i,$vars) {
        $output = '';
        if ($vars[$i][$fieldName.'short'.$i]){
            $output.='<div class="'.$fieldName.' sml">';
            $output.='      <div class="short">';
            $output.=           $vars[$i][$fieldName.'short'.$i];
            $output.='      </div>';
            
            if ($vars[$i][$fieldName.'full'.$i]){
                $output.='          <div class="full" style="display:none">';
                $output.=               $vars[$i][$fieldName.'full'.$i];
                $output.='          </div>';
            }
            $output.='  </div>';
            
        }
        return $output;

    }


    private function moveEmbeddedFiles($config,$fieldName) {

        // Move embedded files into a proper filearea and adjust HTML links to match
        $config->{$fieldName}['text'] = file_save_draft_area_files($config->{$fieldName}['itemid'], $this->context->id, 'block_gflacso_items_viewmore', $fieldName, 0, gflacso_text_viewmore_get_editor_options($this->context), $config->{$fieldName}['text']);
    }

     /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB;

        $config = clone($data);

        $global_config = get_config('block_gflacso_items_viewmore');
        $maxnumitems = (isset($global_config->maxnumitems) && !empty($global_config->maxnumitems) ) ? intval($global_config->maxnumitems) : 50;
        $additionaltextitems = (isset($global_config->maxadditionaltextitems) && !empty($global_config->maxadditionaltextitems) ) ? intval($global_config->maxadditionaltextitems) : 1;

        if (intval($config->itemsnumber) > $maxnumitems )
            $config->itemsnumber = $global_config->maxnumitems;

        for ($i = 1 ; $i<= $data->itemsnumber ; $i++) {
            $fieldName = 'description'.$i;
            self::moveEmbeddedFiles($config,$fieldName);

            $fieldName = 'descriptionshort'.$i;
            self::moveEmbeddedFiles($config,$fieldName);
            
            for ($j=1 ; $j <= $additionaltextitems ; $j++ ) {
                $fieldName = 'text'.$j.'short'.$i;
                self::moveEmbeddedFiles($config,$fieldName);

                $fieldName = 'text'.$j.'full'.$i;
               self::moveEmbeddedFiles($config,$fieldName);
            }


            // Move image files into a proper filearea and adjust HTML links to match
            //Image
            $fieldName = 'image'.$i;
            $draftitemid = $config->{$fieldName};
            file_save_draft_area_files($draftitemid, $this->context->id, 'block_gflacso_items_viewmore', $fieldName, 0, gflacso_text_viewmore_get_image_options($this->context));
           $config->{$fieldName} = $draftitemid;


        }

        parent::instance_config_save($config, $nolongerused);
    }

    function instance_delete() {
        global $DB;
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_gflacso_items_viewmore');
        return true;
    }

    function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }

        return true;
    }
       
}

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
 * @package    block_gflacso_items_viewmore
 * @copyright  Cooperativa GENEOS <info@geneos.com.ar>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once ($CFG->dirroot.'/blocks/gflacso_items_viewmore/locallib.php');
class block_gflacso_items_viewmore_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_gflacso_text_viewmore'));
        $mform->setDefault('config_title', get_string('blocktitle_default', 'block_gflacso_text_viewmore'));
        $mform->setType('config_title', PARAM_TEXT);    


        // Set Number of items.
        $name = 'config_itemsnumber';
        $title = get_string('itemsnumber' , 'block_gflacso_items_viewmore');
        $default = '5';
        $mform->addElement('text',$name, $title,'maxlength="2"  size="4"');
        $mform->addRule($name,  get_string('numeric','block_gflacso_text_viewmore'), 'numeric', '', 'server', false, false);
        $mform->addHelpButton($name, 'itemsnumber', 'block_gflacso_items_viewmore');
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_INT);

        // Set initial show qty.
        $name = 'config_qtyshow';
        $title = get_string('qtyshow' , 'block_gflacso_items_viewmore');
        $default = '3';
        $mform->addElement('text',$name, $title,'maxlength="2"  size="4"');
        $mform->addRule($name,  get_string('numeric','block_gflacso_text_viewmore'), 'numeric', '', 'server', false, false);
        $mform->addHelpButton($name, 'qtyshow', 'block_gflacso_items_viewmore');
        $mform->setDefault($name, $default);
        $mform->setType($name, PARAM_INT);
        $itemsnumber = !empty($this->block->config->itemsnumber) ? $this->block->config->itemsnumber : 0 ;

        $global_config = get_config('block_gflacso_items_viewmore');
        $additionaltextitems = (isset($global_config->maxadditionaltextitems) && !empty($global_config->maxadditionaltextitems) ) ? intval($global_config->maxadditionaltextitems) : 1;


        $context = $this->block->context;
        $imageoptions = array('accepted_types' => array('.png', '.jpg', '.gif'), 'subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 1, 'context'=>$this->block->context);

        for ($i = 1 ; $i<=$itemsnumber ; $i++) {

            $mform->addElement('header', 'item'.$i, get_string('item','block_gflacso_items_viewmore').' '.$i);

            // Description short
            $name = 'config_title'.$i;
            $title = get_string('title', 'block_gflacso_items_viewmore');
            $mform->addElement('text', $name, $title, null,gflacso_text_viewmore_get_editor_options());
            $mform->setType($name, PARAM_RAW);

            // Description short
            $name = 'config_descriptionshort'.$i;
            $title = get_string('descriptionshort', 'block_gflacso_items_viewmore');
            $mform->addElement('editor', $name, $title, null,gflacso_text_viewmore_get_editor_options());
            $mform->setType($name, PARAM_RAW);

            // Description full.
            $name = 'config_description'.$i;
            $title = get_string('description', 'block_gflacso_items_viewmore');
            $mform->addElement('editor', $name, $title, null, gflacso_text_viewmore_get_editor_options());
            $mform->setType('config_description', PARAM_RAW);


            //Text
            //Iterates X times
            for ($j=1 ; $j <= $additionaltextitems ; $j++ ){

                // Short
                $title = 'textshort';
                $identifier = 'config_text'.$j.'short';
                $name = $identifier.$i;
                $title = get_string($title, 'block_gflacso_items_viewmore').' '.$j;
                $mform->addElement('editor', $name, $title, null, gflacso_text_viewmore_get_editor_options());
                $mform->setType($name, PARAM_RAW);

                // Full.
                $title = 'textfull';
                $identifier = 'config_text'.$j.'full';
                $name = $identifier.$i;
                $title = get_string($title, 'block_gflacso_items_viewmore').' '.$j;
                $mform->addElement('editor', $name, $title, null, gflacso_text_viewmore_get_editor_options());
                $mform->setType($name, PARAM_RAW);

            }

            // Item Image.
            $name = 'config_image'.$i;
            $title = get_string('image', 'block_gflacso_items_viewmore');
            $mform->addElement('filemanager', $name, $title, null, gflacso_text_viewmore_get_image_options($context));
            $mform->addHelpButton($name, 'image', 'block_gflacso_items_viewmore');
       
            // URL for item Link
            $name = 'config_link'.$i;
            $title = get_string('link', 'block_gflacso_items_viewmore');
            $default = '';
            $mform->addElement('text',$name, $title);
            $mform->setDefault($name, $default);
            $mform->addHelpButton($name, 'link', 'block_gflacso_items_viewmore');
            $mform->setType($name, PARAM_NOTAGS);

            //text for item link 
            $name = 'config_linktext'.$i;
            $title = get_string('linktext', 'block_gflacso_items_viewmore');
            $default = '';
            $mform->addElement('text',$name, $title);
            $mform->setDefault($name, $default);
            $mform->addHelpButton($name, 'linktext', 'block_gflacso_items_viewmore');
            $mform->setType($name, PARAM_NOTAGS);

        }

    }

    private function processDefaultEditor($defaults,$fieldName){
        $formFieldName = 'config_'.$fieldName;
        $actualValue = $this->block->config->{$fieldName}['text'];
        $draftid_editor = file_get_submitted_draft_itemid($formFieldName);

        if (empty($actualValue)) {
            $currenttext = '';
        } else {
            $currenttext = $actualValue;
        }
        $defaults->{$formFieldName}['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_gflacso_items_viewmore', $fieldName, 0, gflacso_text_viewmore_get_editor_options($this->block->context), $currenttext);
        $defaults->{$formFieldName}['itemid'] = $draftid_editor;
        $defaults->{$formFieldName}['format'] = $this->block->config->{$fieldName}['format'];
        unset($this->block->config->{$fieldName});

    }

    function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {

            $global_config = get_config('block_gflacso_items_viewmore');
            $additionaltextitems = (isset($global_config->maxadditionaltextitems) && !empty($global_config->maxadditionaltextitems) ) ? intval($global_config->maxadditionaltextitems) : 1;

            for ($i = 1 ; $i<=$this->block->config->itemsnumber ; $i++) {
                $fieldName = 'description'.$i;
                self::processDefaultEditor($defaults,$fieldName);

                $fieldName = 'descriptionshort'.$i;
                self::processDefaultEditor($defaults,$fieldName);
                
                for ($j=1 ; $j <= $additionaltextitems ; $j++ ) {
                    $fieldName = 'text'.$j.'short'.$i;
                    self::processDefaultEditor($defaults,$fieldName);

                    $fieldName = 'text'.$j.'full'.$i;
                    self::processDefaultEditor($defaults,$fieldName);
                }

                //Image
                $fieldName = 'image'.$i;
                $formFieldName = 'config_'.$fieldName;
                
                $draftitemid = file_get_submitted_draft_itemid($formFieldName);
                file_prepare_draft_area($draftitemid, $this->block->context->id, 'block_gflacso_items_viewmore', $fieldName, 0, gflacso_text_viewmore_get_image_options($this->block->context));
                $defaults->{$formFieldName} = $draftitemid;       
                unset($this->block->config->{$fieldName});       
            }
        } 

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }
        parent::set_data($defaults);

        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }

        if (isset($title)) {
            // Reset the preserved title
            $this->block->config->title = $title;
        }
    }

}

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
 * @package    block_gflacso_comunicacion
 * @copyright  Cooperativa GENEOS <info@geneos.com.ar>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_gflacso_comunicacion_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->createElement('header', 'configheader', get_string('blocksettings', 'block_gflacso_comunicacion'));

        // A sample string variable with a default value.
	    $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_gflacso_comunicacion'));
	    $mform->setDefault('config_title', get_string('blocktitle_default', 'block_gflacso_comunicacion'));
	    $mform->setType('config_title', PARAM_TEXT);    

        $mform->addElement('advcheckbox', 'config_separator', get_string('config_separator', 'block_gflacso_comunicacion'),'');
        $mform->addElement('advcheckbox', 'config_invertorder', get_string('config_invertorder', 'block_gflacso_comunicacion'),'');

        // Social links
        $mform->addElement('header', 'config_social', get_string('config_social', 'block_gflacso_comunicacion'));

        $fbGroup=array();
        $fbGroup[] =& $mform->createElement('text', 'config_social_fb', get_string('social_fb', 'block_gflacso_comunicacion'));
        $fbGroup[] =& $mform->createElement('text', 'config_social_fb_text', get_string('text', 'block_gflacso_comunicacion'));
        $mform->setType('config_social_fb', PARAM_TEXT); 
        $mform->setType('config_social_fb_text', PARAM_TEXT); 

        $mform->addGroup($fbGroup, 'fbGroup', get_string('facebook', 'block_gflacso_comunicacion'), ' ', false);


        $twGroup=array();
        $twGroup[] =& $mform->createElement('text', 'config_social_tw', get_string('social_tw', 'block_gflacso_comunicacion'));
        $twGroup[] =& $mform->createElement('text', 'config_social_tw_text', get_string('text', 'block_gflacso_comunicacion'));
        $mform->setType('config_social_tw', PARAM_TEXT);
        $mform->setType('config_social_tw_text', PARAM_TEXT);

        $mform->addGroup($twGroup, 'twGroup', get_string('twitter', 'block_gflacso_comunicacion'), ' ', false);

        $blGroup=array();
        $blGroup[] =& $mform->createElement('text', 'config_social_bl', get_string('social_bl', 'block_gflacso_comunicacion'));
        $blGroup[] =& $mform->createElement('text', 'config_social_bl_text', get_string('text', 'block_gflacso_comunicacion'));
        $mform->setType('config_social_bl', PARAM_TEXT);
        $mform->setType('config_social_bl_text', PARAM_TEXT);

        $mform->addGroup($blGroup, 'blGroup', get_string('blog', 'block_gflacso_comunicacion'), ' ', false);


        $skGroup=array();
        $skGroup[] =& $mform->createElement('text', 'config_social_sk', get_string('social_sk', 'block_gflacso_comunicacion'));
        $skGroup[] =& $mform->createElement('text', 'config_social_sk_text', get_string('text', 'block_gflacso_comunicacion'));
        $mform->setType('config_social_sk', PARAM_TEXT);
        $mform->setType('config_social_sk_text', PARAM_TEXT);

        $mform->addGroup($skGroup, 'skGroup', get_string('skype', 'block_gflacso_comunicacion'), ' ', false);

        $igGroup=array();
        $igGroup[] =& $mform->createElement('text', 'config_social_ig', get_string('social_ig', 'block_gflacso_comunicacion'));
        $igGroup[] =& $mform->createElement('text', 'config_social_ig_text', get_string('text', 'block_gflacso_comunicacion'));
        $mform->setType('config_social_ig', PARAM_TEXT); 
        $mform->setType('config_social_ig_text', PARAM_TEXT); 

        $mform->addGroup($igGroup, 'igGroup', get_string('instagram', 'block_gflacso_comunicacion'), ' ', false);


        $socialStyles = array(get_string('social_style_vertical', 'block_gflacso_comunicacion'),
                             get_string('social_style_horizontal_left', 'block_gflacso_comunicacion'), 
                             get_string('social_style_horizontal_right', 'block_gflacso_comunicacion'));
        $mform->addElement('select', 'config_social_style', get_string('social_style', 'block_gflacso_comunicacion'),  $socialStyles, null);

        // Links adicionales
        $linkStyles = array(get_string('config_additionallinks_video', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_conferencia', 'block_gflacso_comunicacion'), 
                             get_string('config_additionallinks_musica', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_audio', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_multimedia', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_encuesta', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_practica', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_profesores', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_cafe', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_foro_verde', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_foro_naranja', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_contacto', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_link_verde', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_link_naranja', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_link_azul', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_punto_naranja', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_punto_gris', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_punto_azul', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_mayor_naranja', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_mayor_gris', 'block_gflacso_comunicacion'),
                             get_string('config_additionallinks_mayor_azul', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_pregunta', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_tutorial', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_facebook', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_twitter', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_skype', 'block_gflacso_comunicacion'),
			     get_string('config_additionallinks_blog', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_google_naranja', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_google_verde', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_instagram', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_calendario', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_linkedin', 'block_gflacso_comunicacion'),
                 get_string('config_additionallinks_youtube', 'block_gflacso_comunicacion')
                             );



        $mform->addElement('header', 'config_first_4_links', get_string('config_first_4_links', 'block_gflacso_comunicacion'));
        $mform->setExpanded('config_first_4_links');

        $mform->addElement('text', 'config_additionallinks_description', get_string('config_additionallinks_description', 'block_gflacso_comunicacion'));

        $firstGroup=array();
        $firstGroup[] =& $mform->createElement('text', 'config_additionallinks_1', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $firstGroup[] =& $mform->createElement('text', 'config_additionallinks_1_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $firstGroup[] =& $mform->createElement('select', 'config_additionallinks_1_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_1', PARAM_TEXT);
        $mform->setType('config_additionallinks_1_text', PARAM_TEXT);  

        $mform->addGroup($firstGroup, 'firstGroup', get_string('config_additionallinks_1', 'block_gflacso_comunicacion'), ' ', false);

        $secondGroup=array();
        $secondGroup[] =& $mform->createElement('text', 'config_additionallinks_2', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $secondGroup[] =& $mform->createElement('text', 'config_additionallinks_2_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $secondGroup[] =& $mform->createElement('select', 'config_additionallinks_2_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_2', PARAM_TEXT);
        $mform->setType('config_additionallinks_2_text', PARAM_TEXT);  

        $mform->addGroup($secondGroup, 'secondGroup', get_string('config_additionallinks_2', 'block_gflacso_comunicacion'), ' ', false);
        
        $thirdGroup=array();
        $thirdGroup[] =& $mform->createElement('text', 'config_additionallinks_3', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $thirdGroup[] =& $mform->createElement('text', 'config_additionallinks_3_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $thirdGroup[] =& $mform->createElement('select', 'config_additionallinks_3_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_3', PARAM_TEXT);
        $mform->setType('config_additionallinks_3_text', PARAM_TEXT);  

        $mform->addGroup($thirdGroup, 'thirdGroup', get_string('config_additionallinks_3', 'block_gflacso_comunicacion'), ' ', false);
        
        $fourthGroup=array();
        $fourthGroup[] =& $mform->createElement('text', 'config_additionallinks_4', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $fourthGroup[] =& $mform->createElement('text', 'config_additionallinks_4_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $fourthGroup[] =& $mform->createElement('select', 'config_additionallinks_4_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_4', PARAM_TEXT);
        $mform->setType('config_additionallinks_4_text', PARAM_TEXT);  

        $mform->addGroup($fourthGroup, 'fourthGroup', get_string('config_additionallinks_3', 'block_gflacso_comunicacion'), ' ', false);

        $mform->addElement('header', 'config_last_4_links', get_string('config_last_4_links', 'block_gflacso_comunicacion'));

        $fifthGroup=array();
        $fifthGroup[] =& $mform->createElement('text', 'config_additionallinks_5', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $fifthGroup[] =& $mform->createElement('text', 'config_additionallinks_5_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $fifthGroup[] =& $mform->createElement('select', 'config_additionallinks_5_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_5', PARAM_TEXT);
        $mform->setType('config_additionallinks_5_text', PARAM_TEXT);  

        $mform->addGroup($fifthGroup, 'fifthGroup', get_string('config_additionallinks_5', 'block_gflacso_comunicacion'), ' ', false);


        $sixthGroup=array();
        $sixthGroup[] =& $mform->createElement('text', 'config_additionallinks_6', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $sixthGroup[] =& $mform->createElement('text', 'config_additionallinks_6_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $sixthGroup[] =& $mform->createElement('select', 'config_additionallinks_6_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_6', PARAM_TEXT);
        $mform->setType('config_additionallinks_6_text', PARAM_TEXT);  

        $mform->addGroup($sixthGroup, 'sixthGroup', get_string('config_additionallinks_6', 'block_gflacso_comunicacion'), ' ', false);
        
        $seventhGroup=array();
        $seventhGroup[] =& $mform->createElement('text', 'config_additionallinks_7', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $seventhGroup[] =& $mform->createElement('text', 'config_additionallinks_7_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $seventhGroup[] =& $mform->createElement('select', 'config_additionallinks_7_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_7', PARAM_TEXT);
        $mform->setType('config_additionallinks_7_text', PARAM_TEXT);  

        $mform->addGroup($seventhGroup, 'seventhGroup', get_string('config_additionallinks_7', 'block_gflacso_comunicacion'), ' ', false);
        
        $eigthGroup=array();
        $eigthGroup[] =& $mform->createElement('text', 'config_additionallinks_8', get_string('config_additionallinks', 'block_gflacso_comunicacion'));
        $eigthGroup[] =& $mform->createElement('text', 'config_additionallinks_8_text', get_string('config_additionallinks_text', 'block_gflacso_comunicacion'));
        $eigthGroup[] =& $mform->createElement('select', 'config_additionallinks_8_style', get_string('config_additionallinks_icon', 'block_gflacso_comunicacion'),  $linkStyles, null);
        $mform->setType('config_additionallinks_8', PARAM_TEXT);
        $mform->setType('config_additionallinks_8_text', PARAM_TEXT);  

        $mform->addGroup($eigthGroup, 'eigthGroup', get_string('config_additionallinks_8', 'block_gflacso_comunicacion'), ' ', false);
        

        /*$mform->addGroup($socialGroup, 'socialgroup_form', get_string('socialgroup_form', 'block_gflacso_comunicacion'), ' ', false);

        $mform->addElement('checkbox', 'display_title', get_string('display_title', 'block_simplehtml'));
        $mform->setDefault('display_title', 0);

/*
        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$this->block->context);
        $mform->addElement('editor', 'config_text', get_string('configcontent', 'block_simplehtml'), null, $editoroptions);
        $mform->addRule('config_text', null, 'required', null, 'client');
        $mform->setType('config_text', PARAM_RAW); // XSS is prevented when printing the block contents and serving files
 

        $mform->addElement('editor', 'config_textmore', get_string('configcontentmore', 'block_simplehtml'), null, $editoroptions);
        $mform->addRule('config_textmore', null, 'required', null, 'client');
        $mform->setType('config_textmore', PARAM_RAW);*/

    }

    /*function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->text;
            $draftid_editor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $defaults->config_text['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_html', 'content', 0, array('subdirs'=>true), $currenttext);
            $defaults->config_text['itemid'] = $draftid_editor;
            $defaults->config_text['format'] = $this->block->config->format;
        } else {
            $text = '';
        }

        if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
            // If a title has been set but the user cannot edit it format it nicely
            $title = $this->block->config->title;
            $defaults->config_title = format_string($title, true, $this->page->context);
            // Remove the title from the config so that parent::set_data doesn't set it.
            unset($this->block->config->title);
        }

        // have to delete text here, otherwise parent::set_data will empty content
        // of editor
        unset($this->block->config->text);
        parent::set_data($defaults);
        // restore $text
        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;
        if (isset($title)) {
            // Reset the preserved title
            $this->block->config->title = $title;
        }
    }*/

}

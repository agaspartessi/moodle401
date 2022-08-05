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

defined('MOODLE_INTERNAL') || die();

class block_gflacso_comunicacion extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_gflacso_comunicacion');
    }


    public function get_content() {
    	global $CFG,$PAGE;
        if ($this->content !== null) {
          return $this->content;
        }
     
        $this->content         =  new stdClass;

        if ($this->config->invertorder){
             $this->content->text   = $this->build_additional_links_section($this->config);
             if ($this->config->separator)
                $this->content->text  .= '<hr/>';
             $this->content->text  .= $this->build_social_section($this->config);
        }

        else {
            $this->content->text   = $this->build_social_section($this->config);
                if ($this->config->separator)
                $this->content->text  .= '<hr/>';
            $this->content->text  .= $this->build_additional_links_section($this->config);
        }

        ob_start();  
        require_once("$CFG->dirroot/blocks/gflacso_comunicacion/layout/scripts.php");
        //Ends building the view
        $content .= ob_get_clean();

        return $this->content;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_gflacso_comunicacion');            
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function instance_allow_multiple() {
      return true;
    }

    private function build_social_section($config) {

        $ret = "";

       
    $title_ig = ( !empty($config->social_ig_text) ) ? $config->social_ig_text : get_string('ig_defaulttitle', 'block_gflacso_comunicacion');
    $title_fb = ( !empty($config->social_fb_text) ) ? $config->social_fb_text : get_string('fb_defaulttitle', 'block_gflacso_comunicacion');
	$title_tw = ( !empty($config->social_tw_text) ) ? $config->social_tw_text : get_string('tw_defaulttitle', 'block_gflacso_comunicacion');
	$title_bl = ( !empty($config->social_bl_text) ) ? $config->social_bl_text : get_string('bl_defaulttitle', 'block_gflacso_comunicacion');
	$title_sk = ( !empty($config->social_sk_text) ) ? $config->social_sk_text : get_string('sk_defaulttitle', 'block_gflacso_comunicacion');

	$links = "";
        if (!empty($config->social_ig))
            $links .= '<a href="'.$this->buildUrl($config->social_ig).'" title="'.$title_ig.'" class="instagram" target="_blank"><span>'.$title_ig.'</span></a>';
        if (!empty($config->social_fb))
            $links .= '<a href="'.$this->buildUrl($config->social_fb).'" title="'.$title_fb.'" class="facebook" target="_blank"><span>'.$title_fb.'</span></a>';

        if (!empty($config->social_tw))
            $links .= '<a href="'.$this->buildUrl($config->social_tw).'" title="'.$title_tw.'" class="twitter" target="_blank"><span>'.$title_tw.'</span></a>';

        if (!empty($config->social_bl))
            $links .= '<a href="'.$this->buildUrl($config->social_bl).'" title="'.$title_bl.'" class="blog" target="_blank"><span>'.$title_bl.'</span></a>';

        if (!empty($config->social_sk))
            $links .= '<a href="'.$this->buildUrl($config->social_sk).'" title="'.$title_sk.'" class="skype" target="_blank"><span>'.$title_sk.'</span></a>';
            

        //default
        $socialStyle = 'vertical';
        //Vertical
        if ($config->social_style == 0)
            $socialStyle = 'vertical';
        //Horizontal izq 
        else if ($config->social_style == 1)
            $socialStyle = 'horizontal izq';
        //Horizontal der
        else if ($config->social_style == 2)
            $socialStyle = 'horizontal der';
        
        if ( !empty($config->social_fb) ||
             !empty($config->social_tw) ||
             !empty($config->social_bl) ||
             !empty($config->social_sk) )
        $ret = '<div class="social_links '.$socialStyle.'">'.$links.'</div>';

        return $ret;
    }

    private function build_additional_links_section($config) {

        $ret = "";

        if (!empty($config->additionallinks_description))
            $ret = '<span class="description">'.$config->additionallinks_description.'</span>';

        
        $links = "";

        if (!empty($config->additionallinks_1))
            $links .= $this->buildLink($config->additionallinks_1,$config->additionallinks_1_style,$config->additionallinks_1_text);
        if (!empty($config->additionallinks_2))
            $links .= $this->buildLink($config->additionallinks_2,$config->additionallinks_2_style,$config->additionallinks_2_text);
        if (!empty($config->additionallinks_3))
            $links .= $this->buildLink($config->additionallinks_3,$config->additionallinks_3_style,$config->additionallinks_3_text);
        if (!empty($config->additionallinks_4))
            $links .= $this->buildLink($config->additionallinks_4,$config->additionallinks_4_style,$config->additionallinks_4_text);
        if (!empty($config->additionallinks_5))
            $links .= $this->buildLink($config->additionallinks_5,$config->additionallinks_5_style,$config->additionallinks_5_text);
        if (!empty($config->additionallinks_6))
            $links .= $this->buildLink($config->additionallinks_6,$config->additionallinks_6_style,$config->additionallinks_6_text);
        if (!empty($config->additionallinks_7))
           $links .= $this->buildLink($config->additionallinks_7,$config->additionallinks_7_style,$config->additionallinks_7_text);
        if (!empty($config->additionallinks_8))
           $links .= $this->buildLink($config->additionallinks_8,$config->additionallinks_8_style,$config->additionallinks_8_text);

        if ( !empty($config->additionallinks_1) ||
             !empty($config->additionallinks_2) ||
             !empty($config->additionallinks_3) ||
             !empty($config->additionallinks_4) ||
             !empty($config->additionallinks_5) ||
             !empty($config->additionallinks_6) ||
             !empty($config->additionallinks_7) ||
             !empty($config->additionallinks_8) )
        $ret = '<div class="additional_links">'.$ret.$links.'</div>';

        
        //$this->config->social_tw.$this->config->social_fb.$this->config->social_sk.$this->config->social_bl;
     
        return $ret;
    }

    private function get_additional_class_by_value($value) {
        switch ($value) {
            case 0:
                 return "video";
            case 1:
                 return "conferencia";
            case 2:
                 return "musica";
            case 3:
                 return "audio";
            case 4:
                 return "multimedia";
            case 5:
                 return "encuesta";
            case 6:
                 return "practica";
            case 7:
                 return "profesores";
            case 8:
                 return "cafe";
            case 9:
                 return "foro_verde";
            case 10:
                 return "foro_naranja";
            case 11:
                 return "contacto";
            case 12:
                 return "link verde";
            case 13:
                 return "link naranja";
            case 14:
                 return "link azul";
            case 15:
                 return "punto naranja";
            case 16:
                 return "punto gris";
            case 17:
                 return "punto azul";
            case 18:
                 return "mayor naranja";
            case 19:
                 return "mayor gris";
            case 20:
                 return "mayor azul";
    	    case 21:
                     return "pregunta";
            case 22:
                     return "tutorial";
    	    case 23:
                     return "facebook";
            case 24:
                     return "twitter";
    	    case 25:
                     return "skype";
            case 26:
                     return "blog";
            case 27:
                     return "google_naranja";
            case 28:
                     return "google_verde";
            case 29:
                     return "instagram";
            case 30:
                     return "calendario fontawesome naranja";
            case 31:
                     return "linkedin fontawesome naranja";
            case 32:
                     return "youtube fontawesome";
            case 33:
                     return "jitsi";            
            
        }
    }


    private function buildLink($link,$style,$title) {
	$email = filter_var($link, FILTER_SANITIZE_EMAIL);
	// Validate e-mail
	if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		//Concateno boton para copiar al portapapeles
	    $copytoclipboard = '<span title="Copiar al portapapeles" class="copytoclipboard"></span>';
	    return $copytoclipboard.'<a href="mailto:'.$email.'" title="'.$title.'" class="'.$this->get_additional_class_by_value($style).' email"><span>'.$title.'</span></a>';
	    	
	
	}

	return '<a href="'.$this->buildUrl($link).'" title="'.$title.'" class="'.$this->get_additional_class_by_value($style).'" target="_blank"><span>'.$title.'</span></a>';
    }

    private function buildUrl($link) {
    $url = $link;
	// Validate url
	if ( !filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
	    $url = 'http://'.$link;
		
	    if ( !filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED) ) {
	    	$url = filter_var($url , FILTER_SANITIZE_URL); 
	     }
	}
	return $url;
    }

}
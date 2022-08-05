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
 * Displays the TinyMCE popup window to insert a Moodle Gflacso Separator
 *
 * @package   tinymce_gflacsoseparator
 * @copyright 2017 Cooperativa GENEOS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true); // Session not used here.

require(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/plugins/gflacsoseparator/dialog.php');

$stringmanager = get_string_manager();

$editor = get_texteditor('tinymce');
$plugin = $editor->get_plugin('gflacsoseparator');

$htmllang = get_html_lang();
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');
$coloroptions = array("0"=>'Gris',
                      "1"=>'Naranja',
                      "2"=>'Magenta',
                      "3"=>'Celeste',
                      "4"=>'Verde',
                      "5"=>'Azul');

?>
<!DOCTYPE html>
<html <?php echo $htmllang ?>
<head>
    <title><?php echo "Insertar línea horizontal de color";//print_string('gflacsoseparator:desc', 'tinymce_gflacsoseparator'); ?></title>
    <script type="text/javascript" src="<?php echo $editor->get_tinymce_base_url(); ?>/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/dialog.js'); ?>"></script>
</head>
<body>

<div>
    <fieldset>
        <legend><?php echo "Opciones";//print_string('gflacsoseparator:general_settings', 'tinymce_gflacsoseparator'); ?></legend>

        <table border="0" cellpadding="4" cellspacing="0" role="presentation">
            <tr>
                    <td><label id="textlabel" for="text"><?php echo "Seleccionar línea de color";//print_string('gflacsoseparator:colorlabel', 'tinymce_gflacsoseparator'); ?></label></td>
                    <td><select id="gflacsoseparator_color" name="text" type="text" class="mceFocus" value="" aria-required="true" style="width: 100px;">
                        <?php 
                        foreach($coloroptions as $colorvalue => $coloroption){ 
                            echo '<option value="'.$colorvalue.'">'.$coloroption.'</option>';
                        }
                        ?>
                    </select></td>
            </tr>
        </table>
    </fieldset>
</div>
    <div class="mceActionPanel">
        <input type="button" id="insert" name="insert" value="{#insert}" onclick='MoodleGFlacsoSeparatorDialog.insert(document.getElementById("gflacsoseparator_color").value);' />
        <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>

</body>
</html>

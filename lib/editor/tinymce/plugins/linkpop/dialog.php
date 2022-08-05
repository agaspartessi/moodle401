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
 * Displays the TinyMCE popup window to insert a Moodle linkpop
 *
 * @package   tinymce_linkpop
 * @copyright 2016 Cooperativa GENEOS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true); // Session not used here.

require(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/plugins/linkpop/dialog.php');

$stringmanager = get_string_manager();

$editor = get_texteditor('tinymce');
$plugin = $editor->get_plugin('linkpop');

$htmllang = get_html_lang();
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');
?>
<!DOCTYPE html>
<html <?php echo $htmllang ?>
<head>
    <title><?php print_string('linkpop:desc', 'tinymce_linkpop'); ?></title>
    <script type="text/javascript" src="<?php echo $editor->get_tinymce_base_url(); ?>/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/dialog.js'); ?>"></script>
</head>
<body>

<div>
    <fieldset>
        <legend><?php print_string('linkpop:general_settings', 'tinymce_linkpop'); ?></legend>

        <table border="0" cellpadding="4" cellspacing="0" role="presentation">
            <tr>
                    <td><label id="textlabel" for="text"><?php print_string('linkpop:textlabel', 'tinymce_linkpop'); ?></label></td>
                    <td><input id="linkpop_text" name="text" type="text" class="mceFocus" value="" aria-required="true" style="width: 260px;"></td>
            </tr>
            <tr>
                    <td><label id="tooltiplabel" for="text"><?php print_string('linkpop:tooltiplabel', 'tinymce_linkpop'); ?></label></td>
                    <td><input id="linkpop_tooltip" name="tooltip" type="text" class="mceFocus" value="" aria-required="true" style="width: 260px;"></td>
            </tr>
        </table>
    </fieldset>
</div>
    <div class="mceActionPanel">
        <input type="button" id="insert" name="insert" value="{#insert}" onclick='MoodleLinkpopDialog.insert(document.getElementById("linkpop_text").value,document.getElementById("linkpop_tooltip").value);' />
        <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>

</body>
</html>

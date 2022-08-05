 /*
 * @package   tinymce_videoresponsive
 * @copyright 2016 Cooperativa GENEOS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    tinymce.create('tinymce.plugins.MoodleVideoResponsive', {


        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceMoodleLinkpop');
            ed.addCommand('mceMoodleVideoResponsive', function() {
                lang = ed.getParam('language');
                ed.windowManager.open({
                    file : ed.getParam("moodle_plugin_base") + 'videoresponsive/dialog.php?lang=' + lang ,
                    width : 350,
                    height : 120,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            // Add an observer to the onInit event to convert emoticon texts to images.
            ed.onInit.add(function(ed) {
            });

            // Add an observer to the onPreProcess event to convert emoticon images to texts.
            ed.onPreProcess.add(function(ed, o) {
            });

            // Register moodleemoticon button.
            ed.addButton('videoresponsive', {
                title : 'videoresponsive.desc',
                cmd : 'mceMoodleVideoResponsive',
                image : url + '/img/insert_videoresponsive.png'
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Moodle Video Responsive plugin',
                author : 'Cooperativa GENEOS',
                authorurl : 'www.geneos.com.ar',
                infourl : 'http://moodle.org',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('videoresponsive', tinymce.plugins.MoodleVideoResponsive);
})();

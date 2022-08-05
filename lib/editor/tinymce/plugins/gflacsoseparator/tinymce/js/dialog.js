var MoodleGFlacsoSeparatorDialog = {

    init : function() {
    },

    insert : function(value) {
    	var colorTop = '#EEE';
    	var colorBottom = '#CCC';
    	if (value == 1)
    		colorTop = colorBottom = '#dc522a';
    	if (value == 2)
    		colorTop = colorBottom = '#930033';
    	if (value == 3)
    		colorTop = colorBottom = '#00bdf2';
    	if (value == 4)
    		colorTop = colorBottom = '#437226';
    	if (value == 5)
    		colorTop = colorBottom = '#002e4d';

        var gflacsoseparator = '<hr style="border-top: 1px solid '+colorTop+'; border-bottom: 1px solid '+colorBottom+'" class="gflacsoseparator'+value+'" />';
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, gflacsoseparator);
        tinyMCEPopup.close();
        return;
    },

};

tinyMCEPopup.onInit.add(MoodleGFlacsoSeparatorDialog.init, MoodleGFlacsoSeparatorDialog);
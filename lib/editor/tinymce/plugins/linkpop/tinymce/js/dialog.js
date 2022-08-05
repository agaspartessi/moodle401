var MoodleLinkpopDialog = {

    init : function() {
    },

    insert : function(text,tooltip) {
        var linkpop = '<a href="#" class="linkpop" onclick="return false;" title=\''+tooltip+'\' >'+text+'</a>';
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, linkpop);
        tinyMCEPopup.close();
        return;
    },

};

tinyMCEPopup.onInit.add(MoodleLinkpopDialog.init, MoodleLinkpopDialog);
var MoodleVideoResponsiveDialog = {

    init : function() {
    },

    insert : function(id,videotype) {
    	if (id == '')
    		return
    	

    	//Si es YOUTUBE le agrego el EMBED
    	if (videotype == 1)
    		id = 'https://www.youtube.com/embed/'+id;
    	//Si es YOUTUBE le agrego el EMBED
    	if (videotype == 2)
    		id = 'https://player.vimeo.com/video/'+id;

    	//Builds iframe
        if (videotype == 1)
        var videoresponsive = '<p></p><div class="videoresponsive" style="position: relative; padding-bottom: 68.5%; height: 0; overflow: show; max-width: 100%;"><iframe frameborder="0" style="border: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="'+id+'?rel=0&showinfo=0" allow="autoplay; encrypted-media allowfullscreen=""></iframe></div></p>';
        
        if (videotype == 2)
        var videoresponsive = '<p></p><div class="videoresponsive" style="position: relative; padding-bottom: 68.5%; height: 0; overflow: show; max-width: 100%;"><iframe frameborder="0" style="border: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="'+id+'" allowfullscreen=""></iframe></div></p>';
            
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, videoresponsive);
        tinyMCEPopup.close();
        return;
    },

};

tinyMCEPopup.onInit.add(MoodleVideoResponsiveDialog.init, MoodleVideoResponsiveDialog);
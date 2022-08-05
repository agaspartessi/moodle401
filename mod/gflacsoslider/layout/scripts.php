<?php 
/*
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
?>

<!-- jQuery -->
<!--script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script-->


<!-- Vimeo API -->
<script src="https://player.vimeo.com/api/player.js"></script>

<script>
var onYouTubeIframeAPIReady = null;
</script>

<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/mod/gflacsoslider/css/bxslider.css">
<?php
$PAGE->requires->js_amd_inline("require(['jquery','mod_gflacsoslider/jquery.bxslider'], function($) {
var bxsliderArray = [];
var playersArray = [];


var tag = document.createElement('script');

  tag.src = 'https://www.youtube.com/iframe_api';
  var firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


	$('.btn.nav-link.float-sm-left.mr-1.btn-secondary').click(function (e) {
	    setTimeout(function(){ $(window).trigger('sibarChanges') }, 600);
	  });


function getPlayer(name,sliderid) {
	console.log('Buscando player name: '+name+', con id: '+sliderid,playersArray);
	for (i=0; i<playersArray.length ; i++){
		if ( (playersArray[i].name == name) && (playersArray[i].sliderid == sliderid) )
			return i;
	}
	return null;
}

function getBXSlider(sliderid) {
	console.log('Buscando slider con id: '+sliderid,bxsliderArray);
	for (i=0; i<bxsliderArray.length ; i++){
		if ( bxsliderArray[i].sliderid == sliderid )
			return bxsliderArray[i];
	}
	return null;
}

function addPlayer(player) {
	aux = getPlayer(player.name , player.sliderid);
	if ( aux == null){
		playersArray.push(player);
	}
	else {
		playersArray[i]=player

	} 
}


/* Inicializo Slider */
$( document ).ready( function(){
	  $('.bxslider').each(function() {
	  bxslider = $(this).bxSlider({
	  mode: this.getAttribute(\"data-slidemode\"),
	  speed: this.getAttribute(\"data-slidespeed\"),
	  auto: parseInt(this.getAttribute(\"data-slideautoplay\")),
	  pause: this.getAttribute(\"data-slideinterval\"),
	  sliderid: this.getAttribute(\"data-sliderid\"),
	  onSlideAfter: function(\$slideElement, oldIndex, newIndex){ 
				//Freno ultimo slider si corresponde
				var ind1 = getPlayer('iframe'+(oldIndex+1),this.sliderid);
				if (ind1 != null){
					aux1 = playersArray[ind1];
					if (aux1.type == 'vimeo')
						aux1.pause();
					else
						aux1.pauseVideo();
				}

				//Reproduzco slider si corresponde
				var ind2 = getPlayer('iframe'+(newIndex+1),this.sliderid);
				
				if (ind2 != null){
					aux2 = playersArray[ind2];
					if (aux2.autoplay){
						if (aux2.type == 'vimeo')
						aux2.play();
					else
						aux2.playVideo();
					aux2.setVolume(0);
					}
				}
				
			},
	onSliderLoad: function(currentIndex){ 
				//Reproduzco slider si corresponde
				var ind = getPlayer('iframe'+(currentIndex+1),this.sliderid);
				if (ind != null){
					aux = playersArray[ind];
					console.log('Autoplay ON',aux);
					if (aux.type == 'vimeo')
						aux.play();
					else
						aux.playVideo();
					aux.setVolume(0);
				}
			}
	});
	console.log('Agregando slider',bxslider);
	bxsliderArray.push(bxslider);
  });
});

/* Inicializo videos de VIMEO */
$( document ).ready( function(){
	$('.sl-slide-inner iframe.iframe').each(function() {
		if (this.getAttribute(\"data-videotype\") == 'vimeo') {
			//Loads VIMEO Player
			var player = new Vimeo.Player($(this));
	
			player.on('play', function() {
				console.log('Parando slider');
				bxslider = getBXSlider(this.sliderid);
				if (bxslider != null){
					console.log('Success: Parando slider:'+this.sliderid)
					bxslider.stopAuto();
				}

			    });

			player.on('ended', function() {
				console.log('Reanudando slider');
				bxslider = getBXSlider(this.sliderid);
				if (bxslider != null){
					console.log('Success: Reanudando slider:'+this.sliderid)
					bxslider.startAuto();
				}

			    });

			player.name = this.id;
			player.type = this.getAttribute(\"data-videotype\");
			player.autoplay = this.getAttribute(\"data-autoplay\");
			player.sliderid = this.getAttribute(\"data-sliderid\");
			console.log('Agregando player a la cola',player)
			addPlayer(player);
		}
	});
	

	
  });


/* Inicializo videos de YOUTUBE */

	onYouTubeIframeAPIReady = function() {

	$('.sl-slide-inner iframe.iframe').each(function() {
		if ($(this).parent().parent().hasClass('bxclone'))
			return;
		if (this.getAttribute(\"data-videotype\") == 'youtube') {
			//Loads YOUTUBE Player
			var player = new YT.Player(this.id,{
				playerVars: {},
				events: {
				    'onStateChange': function(event) {
							if (event.data == YT.PlayerState.PLAYING) {
								console.log('Parando slider');
								bxslider = getBXSlider(event.target.sliderid);
								if (bxslider != null)
									bxslider.stopAuto();
							}
							if (event.data == YT.PlayerState.ENDED) {
								console.log('Reaunudando slider');
								bxslider = getBXSlider(event.target.sliderid);
								if (bxslider != null)
									bxslider.startAuto();
							}
						      }
				  }
			    });
			player.name = this.id;
			player.type = this.getAttribute(\"data-videotype\");
			player.autoplay = this.getAttribute(\"data-autoplay\");
			player.sliderid = this.getAttribute(\"data-sliderid\");
			console.log('Agregando player a la cola',player);
			addPlayer(player);
			
		}
	});
}

});");?>

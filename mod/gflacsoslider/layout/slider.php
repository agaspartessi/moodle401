<?php 
/*
 * @package     mod_gflacsoslider
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
?>

 <?php if ($hasslideshow) { ?>
	<ul class="bxslider" data-slideinterval="<?php echo $slideinterval?>" data-slideautoplay="<?php echo $slideautoplay?>" data-sliderid="<?php echo $id?>" data-slidespeed="<?php echo $slidespeed?>" data-slidemode="<?php echo $slidemode?>">
		<?php for ($i = 1; $i<= $slidenumber; $i++)  {?>
			<?php if ($hasslide[$i]) { $sliderCount++;?>
	
				<li  id="slide<?php echo $i ?>" class="sl-slide" >
					<div class="sl-slide-inner" style="background-color: <?php echo $slidecolor[$i] ?>;">
						<?php if ($hasslidevideo[$i]) { ?>
							<iframe data-sliderid="<?php echo $id?>" data-videotype="<?php echo $slidevideotype[$i] ?>" data-autoplay="<?php echo $slidevideoautoplay[$i] ?>" id="iframe<?php echo $i ?>" class="iframe" src="<?php echo slider_build_url($slidevideo[$i],$slidevideotype[$i])?>"></iframe>

						<?php } elseif ($hasslideimage[$i]){?>
							<img class="img" src="<?php echo $slideimage[$i] ?>"/>
						<?php } ?>
			
						<?php if (!$hasslidevideo[$i]) { ?>
							<h2 style="font-size:<?php echo $slidetitlesize[$i] ?>px; color:<?php echo $slidetitlecolor[$i] ?>;"><?php echo $slidetitle[$i] ?></h2>
							<blockquote><p style="font-size:<?php echo $slidecaptionsize[$i] ?>px;color:<?php echo $slidecaptioncolor[$i] ?>;"><?php echo $slidecaption[$i] ?></p></blockquote>
							<?php if ($slideurl[$i]) { ?>
								<a class="btn" target="_blank" href="http://<?php echo $slideurl[$i] ?>"><?php echo $slideurltext[$i]?></a>
							<?php } ?>
						<?php } ?>
					</div>
				</li>

			<?php } ?>
		<?php } ?>

	</ul>
<?php } ?>
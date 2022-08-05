<?php 
/*
 * @package     mod_gflacso_items_viewmore
 * @copyright   2016 FLACSO & Cooperativa de trabajo GENEOS (www.geneos.com.ar}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

?>

	<div class="gflacso_items_container">
		<?php for ($i = 1; $i<= $itemsnumber; $i++)  {?>

				<div class="gflacso_items_item" >



						<?php 

						//Title
						$fieldName = 'title'.$i; ?>
						<?php if ($vars[$i][$fieldName]){ ?>
							<h4 class="title"><?php echo $vars[$i][$fieldName] ?></h4>
						<?php } 

						//Image
						$fieldName = 'image'.$i; ?>
						<?php if ($vars[$i][$fieldName]){ ?>
							<img class="img" src="<?php echo $vars[$i][$fieldName] ?>"/>
						<?php } ?>

						<?php 
						//Description
						echo self::gflacso_items_buildShortFullContainer('description',$i,$vars);

						//Text
						for ($j=1 ; $j <= 3 ; $j++) {
							echo self::gflacso_items_buildShortFullContainer('text'.$j,$i,$vars);
						}

						//Link
						$fieldName = 'link'.$i;
						if ($vars[$i][$fieldName]) { ?>
							<a class="btn" target="_blank" href="http://<?php echo $vars[$i][$fieldName] ?>"><?php echo $vars[$i]['linktext'.$i]?></a>
						<?php } ?>

				</div>

		<?php } ?>

	</div>
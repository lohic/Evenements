<?php

// REF https://github.com/nilopc/NilPortugues_Javascript_Multiple_JCrop

$image_url = 'images/1490-image.jpg';
$dimensions = json_decode(file_get_contents('var-size.json'));

/**
 * This is part of https://github.com/nilopc/NilPortugues_Javascript_Multiple_JCrop
 *
 * (c) 2013 Nil Portugués Calderó <contact@nilportugues.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	/*
	 * SAVING THE SELECTIONS TO SEPARATE FILES
	 * (I'm aware of the naive code going on here...)
	 *
	 */
	include('image.class.php');
	$image = new imageClass();

	$index = 0;
	$src = array();

	foreach($dimensions as $dimension){

		$tempName = empty($dimension->suffix) ? 'temp/image' : 'temp/image-'.$dimension->suffix;
		$fileName = empty($dimension->suffix) ? 'image' : 'image-'.$dimension->suffix;

		file_put_contents($tempName, file_get_contents($_POST['jcrop-src'][0]) );

		$src[] = $image
				->setImage($tempName)
			    ->crop(	$_POST['jcrop-x'][$index],
	    				$_POST['jcrop-y'][$index],
	    				$_POST['jcrop-x2'][$index],
	    				$_POST['jcrop-y2'][$index])
			    ->resize($dimension->width,$dimension->height,'exact')
			    ->save('./generated' , $fileName , $image->getFileType());

		unlink($tempName);

		$index++;
	}

	//HTML OUTPUT
	echo '<h2>Crop Return</h2>';

	foreach($src as $image){
		echo '<img style="border:1px solid #ccc" src="'.$image.'"> <br>';
	}

	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Multiple JCrop with Real-time Preview</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<script src="js/jquery.min.js"></script>

	<!-- The original JCrop Plugin -->
	<script src="js/jquery.Jcrop.js"></script>
	<link href="css/jquery.Jcrop.css" type="text/css" rel="stylesheet" />

	<!-- The JCrop Multiple Plugin -->
	<script src="js/jquery.Jcrop.multiple.js"></script>
	<link href="css/jquery.Jcrop.custom.css" type="text/css" rel="stylesheet"/>

	<script>
		
		$(document).ready(function(){

			var widthLimit = 500;

			$('.jcrop-preview-container').each(function(){

				if( parseInt($(this).width()) >= widthLimit){

					var ratio = widthLimit/parseInt($(this).width());

					$(this).parent()
					.css('transform-origin','0 0')
					.css('transform','scale('+ratio+')');

					
				}

				var boxHeight = $(this).parent().parent().find('.jcrop-box').data('height');
				var newHeight = $(this)[0].getBoundingClientRect().height;

				var finalHeight = boxHeight > newHeight ? boxHeight + 20 : newHeight +20;
					
				$(this).parent().parent().parent()
				.css('padding-bottom',finalHeight+'px');
			});

		});


	</script>

	<style>
		#crop_form{
			margin:20px;
			padding:20px;
			border:1px solid #ddd;
		}

		.jcrop-item{
			border:solid 1px #F00;
			position: relative;
		}

		.jcrop-viewer{
			position: absolute;
			top:60px;
			left:20px;
		}

		.jcrop-preview-pane{
			position: absolute;
			height: 0;
		}
	</style>

</head>
<body>


<form method="post" id="crop_form">
	<input type="submit" value="Crop Images"/>	
	<hr/>

	<?php foreach($dimensions as $dimension){ ?>

		<div class="jcrop-item">
			<h4><?php echo $dimension->label ?></h4>

			<div class="jcrop-viewer">		
				<div class="jcrop-preview-pane" class="jcrop-transparent-bg">
					<div class="jcrop-preview-container">
						<img class="jcrop-preview"
							data-height="<?php echo $dimension->height ?>"
							data-width="<?php echo $dimension->width ?>" />
					</div>				
				</div>	
				<img class="jcrop-box" src="<?php echo $image_url; ?>"
				     data-height="300" data-width="300"
				     data-x='0'
				     data-y='0'
				     data-x2='<?php echo $dimension->width ?>'
				     data-y2='<?php echo $dimension->height ?>'
				/>
			</div>	
			<input type="hidden" class="jcrop-src" name="jcrop-src[]" />
			<input type="hidden" class="jcrop-x"   name="jcrop-x[]" />
			<input type="hidden" class="jcrop-y"   name="jcrop-y[]" />
			<input type="hidden" class="jcrop-x2"  name="jcrop-x2[]" />
			<input type="hidden" class="jcrop-y2"  name="jcrop-y2[]" />

			<div style="clear:left"></div>
		</div>
		

	<?php } ?>

</form>

</body>
</html>

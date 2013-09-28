<?php

if(isset($_POST['crop_image'])){

	$targ_w = 160*5;
	$targ_h = 90*5;
	$jpeg_quality = 90;
	
	$src = 'images/image.jpg';
	$img_r = imagecreatefromjpeg($src);

	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	$output_filename = 'images/image_big.jpg';
	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'], $targ_w,$targ_h,$_POST['w'],$_POST['h']);
	imagejpeg($dst_r, $output_filename, $jpeg_quality);

	$dst_r = ImageCreateTrueColor( $targ_w*2/5, $targ_h*2/5 );
	$output_filename = 'images/image_small.jpg';
	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'], $targ_w*2/5,$targ_h*2/5,$_POST['w'],$_POST['h']);
	imagejpeg($dst_r, $output_filename, $jpeg_quality);

	$dst_r = ImageCreateTrueColor( $targ_w/5, $targ_h/5 );
	$output_filename = 'images/image_mini.jpg';
	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'], $targ_w/5,$targ_h/5,$_POST['w'],$_POST['h']);
	imagejpeg($dst_r, $output_filename, $jpeg_quality);

	//header('Content-type: image/jpeg');
	//imagejpeg($dst_r, null, $jpeg_quality);
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Document sans titre</title>

<script src="js/jquery.min.js"></script>
<script src="js/jquery.Jcrop.js"></script>
<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />

<script language="Javascript">

	// Remember to invoke within jQuery(window).load(...)
	// If you don't, Jcrop may not initialize properly
	jQuery(window).load(function(){

		jQuery('#cropbox').Jcrop({
			//onChange: showCoords,
			setSelect: [ 0, 0, 160, 90],
			onChange: showCoords,
			onSelect: showCoords,
			aspectRatio: 16/9
		});

		function showCoords(c)
		{
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
			
			if(c.w<320 || c.h<180){
				$('#alerte').show();
				if(c.w<180 || c.h < 90){
					$('#alerte').text('Attention votre image sera très pixelisée').css('background-color','#FF0000');
				}else{
					$('#alerte').text('Attention votre image sera pixelisée').css('background-color','#FFFF00');
				}
			}else{
				$('#alerte').hide();
			}
		};


	});

</script>

<style>
#alerte{
	background:#FF0;
	color:#000;
	text-transform:uppercase;
	font-size:12px;
	padding:6px;
	display:inline-block;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
}

</style>

</head>

<body>

<img src="images/image.jpg" id="cropbox" />
<form action="test-crop.php" method="post" onsubmit="return checkCoords();">
    <input type="hidden" id="x" name="x" />
    <input type="hidden" id="y" name="y" />
    <input type="hidden" id="w" name="w" />
    <input type="hidden" id="h" name="h" />
    <div id="alerte">Attention votre image sera très pixelisée</div>
    <input type="hidden" id="crop_image" name="crop_image" />
    <input type="submit" value="Recadrer l'image" />
</form>


</body>
</html>
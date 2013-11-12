<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

//include_once('../vars/constantes_vars.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');


$core = new core();


/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */


/*if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if($_POST['evenement_texte_image']!=""){
		
		$sql ="UPDATE sp_evenements SET
					evenement_texte_image = '".addslashes($_POST["evenement_texte_image"])."'
				WHERE evenement_id = '".$_GET['id']."'";
		mysql_query($sql) or die(mysql_error());
		
		$targ_w = $_POST['w'];
		$targ_h = $_POST['h'];
		$quality = 90;

		$sql ="SELECT evenement_image FROM sp_evenements WHERE evenement_id = '".$_POST['id']."'";
		$res = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);

		$src = 'upload/photos/evenement_'.$_POST['id'].'/'.$row['evenement_image'];

		$extension_img = substr(strchr($src,'.'),1);
		
		$src = 'upload/photos/evenement_'.$_POST['id'].'/original.'.$extension_img;


		if(ereg('(jpeg|jpg|gif|png)$',$extension_img)){
			switch ($extension_img){
				case "gif": // GIF
					$img_r = imagecreatefromgif($src); break;
				case "jpg": //JPEG
					$img_r = imagecreatefromjpeg($src); break;
				case "jpeg": //JPEG
					$img_r = imagecreatefromjpeg($src); break;
				case "png": // PNG
					$img_r = imagecreatefrompng($src); 
					break;
			}
		}


		//$img_r = imagecreatefromjpeg($src);
		$dst_r = imagecreatetruecolor( $targ_w, $targ_h );

		imagealphablending($dst_r, false);

		imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],$targ_w,$targ_h,$_POST['w'],$_POST['h']);

		imagesavealpha($dst_r, true);


		$file_url = 'upload/photos/evenement_'.$_POST['id'].'/image.';

		if(ereg('(jpeg|jpg|gif|png)$',$extension_img)){
			$file_url=$file_url.$extension_img;

			switch ($extension_img){
				case "gif": // GIF
					imagegif($dst_r,$file_url); break;
				case "jpg": //JPEG
					imagejpeg($dst_r,$file_url,$quality); break;
				case "jpeg": //JPEG
					imagejpeg($dst_r,$file_url,$quality); break;
				case "png": // PNG
					imagepng($dst_r,$file_url,4); 
					break;
			}
		}
		$repertoire_destination="./upload/photos/evenement_".$_POST['id']."/";
		make_miniature($file_url, 480, 270, $repertoire_destination, "grande-");
		make_miniature($file_url, 320, 180, $repertoire_destination, "moyen-");
		make_miniature($file_url, 160, 90, $repertoire_destination, "mini-");

		header("Location:list.php?menu_actif=evenements");
	}
	else{
		$erreur = "le texte alternatif de l'image est obligatoire";
	}
}*/

$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);


// REF https://github.com/nilopc/NilPortugues_Javascript_Multiple_JCrop

$sql = sprintf("SELECT evenement_image, evenement_texte_image FROM ".TB."evenements WHERE evenement_id = %s",
										func::GetSQLValueString($_GET['id'],"int"));
$res = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($res);
$image_url = 'upload/photos/evenement_'.$_GET['id'].'/'.$row['evenement_image'];
$extension_img = substr(strchr($image_url,'.'),1);

$image_url = 'upload/photos/evenement_'.$_GET['id'].'/original.'.$extension_img;

//$image_url = 'crop/images/1490-image.jpg';
$dimensions = json_decode(file_get_contents('crop/var-size.json'));

$erreur="";
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
	if($_POST['evenement_texte_image']!=""){
		
		$sql =sprintf("UPDATE ".TB."evenements SET evenement_texte_image=%s WHERE evenement_id=%s",
											func::GetSQLValueString($_POST["evenement_texte_image"], "text"),
											func::GetSQLValueString($_GET['id'],"int"));
		mysql_query($sql) or die(mysql_error());
		/*
		 * SAVING THE SELECTIONS TO SEPARATE FILES
		 * (I'm aware of the naive code going on here...)
		 *
		 */
		// uncomment for ph php 5.1.3
		//include('crop/image.class.php');
		include('crop/image.class.5.1.2.php');
		$image = new imageClass();

		$index = 0;
		$src = array();

		foreach($dimensions as $dimension){

			$tempName = empty($dimension->suffix) ? 'crop/temp/image' : 'crop/temp/image-'.$dimension->suffix;
			$fileName = empty($dimension->suffix) ? 'image' : 'image-'.$dimension->suffix;

			file_put_contents($tempName, file_get_contents($_POST['jcrop-src'][0]) );

			$src[] = $image
					->setImage($tempName)
				    ->crop(	$_POST['jcrop-x'][$index],
		    				$_POST['jcrop-y'][$index],
		    				$_POST['jcrop-x2'][$index],
		    				$_POST['jcrop-y2'][$index])
				    ->resize($dimension->width,$dimension->height,'exact')
				    ->save('./upload/photos/evenement_'.$_GET['id'] , $fileName , $image->getFileType());

			if($dimension->id=="evenement"){
				$file_url = 'upload/photos/evenement_'.$_GET['id'].'/image.'.$extension_img;
				$repertoire_destination="./upload/photos/evenement_".$_GET['id']."/";
				make_miniature($file_url, 160, 90, $repertoire_destination, "mini-");
			}

			unlink($tempName);

			$index++;
		}

		//HTML OUTPUT
		/*echo '<h2>Crop Return</h2>';

		foreach($src as $image){
			echo '<img style="border:1px solid #ccc" src="'.$image.'"> <br>';
		}

		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		exit;*/

		header("Location:list.php?menu_actif=evenements");
	}
	else{
		$erreur = "le texte alternatif de l'image est obligatoire";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Sciences Po | Événements : administration</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="tools.js"></script>

	<script src="crop/js/jquery.min.js"></script>

	<!-- The original JCrop Plugin -->
	<script src="crop/js/jquery.Jcrop.js"></script>
	<link href="crop/css/jquery.Jcrop.css" type="text/css" rel="stylesheet" />

	<!-- The JCrop Multiple Plugin -->
	<script src="crop/js/jquery.Jcrop.multiple.js"></script>
	<link href="crop/css/jquery.Jcrop.custom.css" type="text/css" rel="stylesheet"/>

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
					
				//$(this).parent().parent().parent().css('padding-bottom',finalHeight+'px');
			});

		});


	</script>
</head>

<body>
	<div id="page">
		    <?php include("top.php"); ?>
	    <div id="menu">
			<?php include("menu.php"); ?>
	    </div>
	    <div id="content">
	    	<?php
    		if($erreur!=""){
				echo '<p class="erreur">'.$erreur.'</p>';
			}

			?>
			<div class="jcrop_container">
				<div class="article">
					<h1>Recadrage de l'image</h1>
					<form method="post" id="crop_form">
						
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

						<?php } 
						?>
						<p>
							<label for="evenement_texte_image">Texte alternatif de l'image* :</label>
							<input type="text" name="evenement_texte_image" value="<?php echo $row['evenement_texte_image'];?>" class="inputField" id="evenement_texte_image"/>
						</p>
						<p><input type="submit" value="Recadrer l'image" id="recadrer"/></p>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

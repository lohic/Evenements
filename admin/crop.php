<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');


/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */


if ($_SERVER['REQUEST_METHOD'] == 'POST')
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
}
include_once('../vars/constantes_vars.php');
include_once('../vars/statics_vars.php');

include_once('../classe/classe_core_event.php');
include_once('../classe/fonctions.php');

$core = new core();
$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Sciences Po | Événements : administration</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
	<link href="jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="tools.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
	<script src="Jcrop/js/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" href="Jcrop/css/jquery.Jcrop.css" type="text/css" />
	<link rel="stylesheet" href="Jcrop/demos/demo_files/demos.css" type="text/css" />

	<script language="Javascript">

		/*$(function(){
			$('#cropbox').Jcrop({
				onSelect: updateCoords,
				minSize: [ 16, 9 ],
				aspectRatio: 16/9
			});

		});

		function updateCoords(c){
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};

		function checkCoords(){
			if (parseInt($('#w').val())) return true;
			alert('Sélectionnez d\'abord une zone de l\'image.');
			return false;
		};*/
		
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
			
			<div id="outer">
				<div class="jcExample">
					<div class="article">

						<h1>Recadrage de l'image</h1>
		
						<?php
							$sql ="SELECT evenement_image, evenement_texte_image FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
							$res = mysql_query($sql) or die(mysql_error());
							$row = mysql_fetch_array($res);
							$src = 'upload/photos/evenement_'.$_GET['id'].'/'.$row['evenement_image'];
							$extension_img = substr(strchr($src,'.'),1);
							
							$src = 'upload/photos/evenement_'.$_GET['id'].'/original.'.$extension_img.'?cache='.time();
						?>
		
						<!-- This is the image we're attaching Jcrop to -->
						<img src="<?php echo $src; ?>" id="cropbox" />

						<!-- This is the form that our event handler fills -->
						<form action="crop.php?menu_actif=evenements&amp;id=<?php echo $_GET['id']; ?>" method="post" onsubmit="return checkCoords();">
							<input type="hidden" id="x" name="x" />
							<input type="hidden" id="y" name="y" />
							<input type="hidden" id="id" name="id" value="<?php echo $_GET['id']; ?>"/>
							<input type="hidden" id="w" name="w" />
							<input type="hidden" id="h" name="h" />
							<p>
								<label for="evenement_texte_image">Texte alternatif de l'image* :</label>
								<input type="text" name="evenement_texte_image" value="<?php echo $row['evenement_texte_image'];?>" class="inputField" id="evenement_texte_image"/>
							</p>
							<div id="alerte">Attention votre image sera très pixelisée</div>
						    <input type="hidden" id="crop_image" name="crop_image" />
							<p><input type="submit" value="Recadrer l'image" /></p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

<?php
// security 

include_once('../vars/config.php');
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

// feedcreator library
include('feedcreator.class.php');

include('variables.php');

//include_once('../vars/constantes_vars.php');
//include_once('../vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');


$core = new core();

//session_start();
$erreur=""; 


// if editing...
if( isset($_POST['evenement_id']) ){ 
	
	$erreur=testChamps($_POST['evenement_titre'], $_POST['evenement_texte'], $_POST['evenement_rubrique'], $_POST['session_date_debut'], $_POST['session_heure_debut']);
	
	if($erreur==""){		
		$debutEvenement = 0; 
		$finEvenement = 0;
        
		$tableauHeureDebut = explode(":",$_POST["session_heure_debut"]);
		$tableauDateDebut = explode("/",$_POST["session_date_debut"]);

		$debutEvenement = mktime($tableauHeureDebut[0], $tableauHeureDebut[1],0,$tableauDateDebut[1],$tableauDateDebut[0],$tableauDateDebut[2]);
		$finEvenement = retourneTimestamp($_POST["session_heure_fin"], $_POST["session_date_fin"], $_POST["session_date_debut"]);
        
		$photo = retournePhoto($_FILES['evenement_image']['name'], $_POST['image_cachee']);

		// query
		$sql ="UPDATE sp_evenements SET
					evenement_statut = '".$_POST["evenement_statut"]."', 
					evenement_titre = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_titre"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_titre_en = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_titre_en"], ENT_NOQUOTES, 'UTF-8' ))."',
					evenement_resume = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_resume"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_resume_en = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_resume_en"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_texte = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_texte"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_texte_en = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_texte_en"], ENT_NOQUOTES, 'UTF-8' ))."',
					evenement_organisateur = '".mysql_real_escape_string(html_entity_decode($_POST["evenement_organisateur"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_organisateur_en = '".mysql_real_escape_string(html_entity_decode($_POST["evenement_organisateur_en"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_coorganisateur = '".mysql_real_escape_string(html_entity_decode($_POST["evenement_coorganisateur"], ENT_NOQUOTES, 'UTF-8' ))."', 
					evenement_coorganisateur_en = '".mysql_real_escape_string(html_entity_decode($_POST["evenement_coorganisateur_en"], ENT_NOQUOTES, 'UTF-8' ))."',
					evenement_date = '".$debutEvenement."',
					evenement_datetime = FROM_UNIXTIME(".$debutEvenement."),
					evenement_groupe_id = '".$_POST['evenement_groupe']."', 
					evenement_rubrique = '".addslashes($_POST["evenement_rubrique"])."',
					evenement_image = '".$photo."',
					evenement_facebook = '".$_POST['evenement_facebook']."',
					evenement_editeur_id = '".$_SESSION['id']."',
					evenement_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."',
					evenement_externe = '".$_POST["evenement_externe"]."'
				WHERE evenement_id = '".$_POST['evenement_id']."'";
		mysql_query($sql) or die(mysql_error());
		

		$session_statut_inscription = !empty($_POST["session_statut_inscription"]) ? 1 : 0 ;
		$session_statut_vision = !empty($_POST["session_statut_vision"]) ? 1 : 0 ;


		$sql ="UPDATE sp_sessions SET
					session_nom = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_titre"], ENT_NOQUOTES, 'UTF-8' ))."', 
					session_nom_en = '".mysql_real_escape_string(html_entity_decode( $_POST["evenement_titre_en"], ENT_NOQUOTES, 'UTF-8' ))."',
					session_debut = '".$debutEvenement."',
					session_debut_datetime = FROM_UNIXTIME(".$debutEvenement."),
					session_fin = '".$finEvenement."',
					session_debut_datetime = FROM_UNIXTIME(".$finEvenement."),
					session_langue = '".$_POST["session_langue"]."',
					session_lieu = '".$_POST['session_lieu']."',
					session_code_batiment = '".$_POST['session_code_batiment']."',
					session_lien = '".$_POST["session_lien"]."',
					session_lien_en = '".$_POST["session_lien_en"]."',
					session_texte_lien = '".addslashes($_POST["session_texte_lien"])."',
					session_texte_lien_en = '".addslashes($_POST["session_texte_lien_en"])."',
					session_type_inscription = '".$_POST["session_type_inscription"]."',
					session_complement_type_inscription = '".addslashes($_POST["session_complement_type_inscription"])."',
					session_statut_inscription = '".$session_statut_inscription."',
					session_places_internes_totales = '".$_POST["session_places_internes_totales"]."',
					session_places_externes_totales = '".$_POST["session_places_externes_totales"]."',
					session_statut_visio = '".$session_statut_vision."',
					session_places_internes_totales_visio = '".$_POST["session_places_internes_totales_vision"]."',
					session_places_externes_totales_visio = '".$_POST["session_places_externes_totales_vision"]."',
					session_adresse1 = '".addslashes($_POST["session_adresse1"])."',
					session_adresse2 = '".addslashes($_POST["session_adresse2"])."',
					session_code_externe = '".$_POST["session_code_externe"]."',
					session_traduction = '".$_POST["session_traduction"]."',
					session_editeur_id = '".$_SESSION['id']."',
					session_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."'
				WHERE evenement_id = '".$_POST['evenement_id']."'";
		mysql_query($sql) or die(mysql_error());
		
		//enregistrement des liaisons avec les groupes partagés
		$sql="DELETE FROM sp_rel_evenement_groupe WHERE evenement_id = '".$_POST['evenement_id']."'";
		mysql_query($sql) or die(mysql_error());

		if(isset($_POST['groupes'])){
			for ($i = 0; $i < count($_POST['groupes']); $i++) {
				$sqlinsert ="INSERT INTO sp_rel_evenement_groupe VALUES ('', '".$_POST['evenement_id']."', '".$_POST['groupes'][$i]."')";
				mysql_query($sqlinsert) or die(mysql_error());
			}
		}

		//enregistrement des liaisons avec les mots-clés
		$sql="DELETE FROM sp_rel_evenement_keyword WHERE evenement_id = '".$_POST['evenement_id']."'";
		mysql_query($sql) or die(mysql_error());

		if(isset($_POST['keywords'])){
			for ($i = 0; $i < count($_POST['keywords']); $i++) {
				$sqlinsert ="INSERT INTO sp_rel_evenement_keyword VALUES ('', '".$_POST['evenement_id']."', '".$_POST['keywords'][$i]."')";
				mysql_query($sqlinsert) or die(mysql_error());
			}
		}
			
		if($_FILES['evenement_image']['name']!=""){
			mkdir("upload/photos/evenement_".$_POST['evenement_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/photos/evenement_'.$_POST['evenement_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['evenement_image']['name']);

			if(isExtAuthorized($extension)){
				$photo = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['evenement_image']['tmp_name'], $file_url.'/'.$photo)){
					@chmod("$file_url/$photo", 0777);
					$img="$file_url/$photo";
					
					$original = 'original'.$extension;
					$destination = "$file_url/$original"; 
					
					copy($img, $destination);
					
					$repertoire_destination="./".$file_url."/";
					make_miniature($img, 320, 180, $repertoire_destination, "moyen-");
					make_miniature($img, 160, 90, $repertoire_destination, "mini-");
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $photo";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}

			// reedirect
			header( "Location:crop.php?menu_actif=evenements&id=".$_POST['evenement_id']);
		}
		else{
			header("Location:edit_evenement_unique.php?menu_actif=evenements&id=".$_POST['evenement_id'] );
		}
	}		
}


if(isset($_POST["evenement_id_creation"])){
	
	$debutEvenement = 0; 
	$finEvenement = 0;

	$tableauHeureDebut = explode(":",$_POST["session_heure_debut_creation"]);
	$tableauDateDebut = explode("/",$_POST["session_date_debut_creation"]);

	$debutEvenement = mktime($tableauHeureDebut[0], $tableauHeureDebut[1],0,$tableauDateDebut[1],$tableauDateDebut[0],$tableauDateDebut[2]);
	$finEvenement = retourneTimestamp($_POST["session_heure_fin_creation"], $_POST["session_date_fin_creation"], $_POST["session_date_debut_creation"]);

	// query
	$sqlupdate ="INSERT INTO sp_sessions
		VALUES('',
			'".$_POST["evenement_id_creation"]."',
			'".addslashes($_POST["session_nom_creation"])."',
			'".addslashes($_POST["session_nom_en_creation"])."',
			'".$debutEvenement."',
			FROM_UNIXTIME(".$debutEvenement."),
			'".$finEvenement."',
			FROM_UNIXTIME(".$finEvenement."),
			'".$_POST["session_langue_creation"]."',
			'".$_POST['session_lieu_creation']."',
			'".$_POST['session_code_batiment_creation']."',
			'".$_POST["session_lien_creation"]."',
			'".$_POST["session_lien_en_creation"]."',
			'".addslashes($_POST["session_texte_lien_creation"])."',
			'".addslashes($_POST["session_texte_lien_en_creation"])."',
			'".$_POST["session_type_inscription_creation"]."',
			'".addslashes($_POST["session_complement_type_inscription_creation"])."',
			'".$_POST["session_statut_inscription_creation"]."',
			'".$_POST["session_places_internes_totales_creation"]."',
			'',
			'".$_POST["session_places_externes_totales_creation"]."',
			'',
			'".$_POST["session_statut_vision_creation"]."',
			'".$_POST["session_places_internes_totales_vision_creation"]."',
			'',
			'".$_POST["session_places_externes_totales_vision_creation"]."',
			'',
			'".mysql_real_escape_string(html_entity_decode( $_POST["session_adresse1_creation"], ENT_NOQUOTES, 'UTF-8' ))."',
			'".mysql_real_escape_string(html_entity_decode( $_POST["session_adresse2_creation"], ENT_NOQUOTES, 'UTF-8' ))."',
			'',
			'',
			'".$_POST["session_code_externe_creation"]."',
			'".$_POST["session_traduction_creation"]."', 
			'', 
			'".$_SESSION['id']."', 
			'".$_SERVER["REMOTE_ADDR"]."'
			)";
	mysql_query($sqlupdate) or die(mysql_error());
	
	$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$_POST["evenement_id_creation"]."'";
	$ressessions = mysql_query($sqlsessions) or die(mysql_error());
	$debutEvenement=1000000000000000;
	while($rowsession = mysql_fetch_array($ressessions)){
		if($rowsession['session_debut']<$debutEvenement){
			$debutEvenement = $rowsession['session_debut'];
		}
	}
	
	$sql ="UPDATE sp_evenements SET
				evenement_date = '".$debutEvenement."'
			WHERE evenement_id = '".$_POST["evenement_id_creation"]."'";
	mysql_query($sql) or die(mysql_error());
	
	header("Location:edit_evenement.php?menu_actif=evenements&id=".$_GET['id'] );
	
}

if(isset($_POST['btMedia']) && $_POST['btMedia'] == "Ajouter"){
	$extension = "";
	if($_FILES['evenement_media']['name']!=""){
		$extension = getExtension($_FILES['evenement_media']['name']);

		if(isExtAuthorizedMedia($extension)){ 
			/*if($extension==".jpeg" || $extension==".jpg" || $extension==".gif" || $extension==".png"){
				$document = 'image'.$extension;
			}
			else{
				$document = 'document'.$extension;
			}*/
			// query
			
			if($_POST['evenement_media_nom']!=""){
				$nomDuFichier = $_POST['evenement_media_nom'].$extension;
			}
			else{
				$nomDuFichier = $_FILES['evenement_media']['name'];
			}

			mkdir("upload/medias/evenement_".$_POST['media_evenement_id']);
			$file_url = 'upload/medias/evenement_'.$_POST['media_evenement_id'];
			
			// Upload fichier
			if (@move_uploaded_file($_FILES['evenement_media']['tmp_name'], $file_url.'/'.$nomDuFichier)){
				@chmod($file_url."/".$nomDuFichier, 0777);
				$sqlmedia ="INSERT INTO sp_medias
					VALUES('',
						'".$_POST["media_evenement_id"]."',
						'".$nomDuFichier."',
						'".$extension."'
						)";
				mysql_query($sqlmedia) or die(mysql_error());
			}
			else{
				echo "Erreur, impossible d'envoyer le fichier";
			} 
		}
	}
   
	header("Location:edit_evenement_unique.php?menu_actif=evenements&id=".$_GET['id'] );
}

if(isset($_GET['delete'])){
	$sql ="DELETE FROM sp_evenements WHERE evenement_id = '".$_GET['delete']."'";
	$res = mysql_query($sql) or die(mysql_error());
	$sql ="DELETE FROM sp_sessions WHERE evenement_id = '".$_GET['delete']."'";
	$res = mysql_query($sql) or die(mysql_error());
	header("Location:list.php?menu_actif=evenements");
}

$sql ="SELECT * FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
$res = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($res);


if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_image"){
	$sql ="UPDATE sp_evenements SET evenement_image = '' WHERE evenement_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_document"){ 
	$sql ="DELETE FROM sp_medias WHERE media_id = '".$_GET['idDoc']."'";
	$res = mysql_query($sql) or die(mysql_error());
}



if(($core->isAdmin && $core->userLevel<=1) || $row['evenement_groupe_id']==$_SESSION['id_actual_group']){
	$sqlGetOrganisme ="SELECT organisme_id, organisme_url_front FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
	$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
	$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);

	if($core->isAdmin && $core->userLevel<=1){
		$sqlGroupes ="SELECT * FROM sp_groupes ORDER BY groupe_libelle ASC";
		$resGroupes = mysql_query($sqlGroupes) or die(mysql_error());

		$sqlKeywords ="SELECT * FROM sp_keywords ORDER BY keyword_nom ASC";
		$sqlKeywords = mysql_query($sqlKeywords) or die(mysql_error());

		$sqllieux ="SELECT * FROM sp_lieux ORDER BY lieu_nom ASC";
		$reslieux = mysql_query($sqllieux) or die(mysql_error());

		$sqlcodes ="SELECT * FROM sp_codes_batiments ORDER BY code_batiment_nom ASC";
		$rescodes = mysql_query($sqlcodes) or die(mysql_error());

		$sqllieux2 ="SELECT * FROM sp_lieux ORDER BY lieu_nom ASC";
		$reslieux2 = mysql_query($sqllieux2) or die(mysql_error());

		$sqlcodes2 ="SELECT * FROM sp_codes_batiments ORDER BY code_batiment_nom ASC";
		$rescodes2 = mysql_query($sqlcodes2) or die(mysql_error());
	}
	else{
		$sqlGroupes ="SELECT * FROM sp_groupes WHERE groupe_organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY groupe_libelle ASC";
		$resGroupes = mysql_query($sqlGroupes) or die(mysql_error());

		$sqlKeywords ="SELECT * FROM sp_keywords WHERE keyword_organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY keyword_nom ASC";
		$sqlKeywords = mysql_query($sqlKeywords) or die(mysql_error());

		$sqllieux ="SELECT * FROM sp_lieux as spl, sp_rel_lieu_organisme as sprl WHERE spl.lieu_id=sprl.lieu_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY lieu_nom ASC";
		$reslieux = mysql_query($sqllieux) or die(mysql_error());

		$sqlcodes ="SELECT * FROM sp_codes_batiments, sp_rel_batiment_organisme WHERE batiment_id=code_batiment_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY code_batiment_nom ASC";
		$rescodes = mysql_query($sqlcodes) or die(mysql_error());

		$sqllieux2 ="SELECT * FROM sp_lieux as spl, sp_rel_lieu_organisme as sprl WHERE spl.lieu_id=sprl.lieu_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY lieu_nom ASC";
		$reslieux2 = mysql_query($sqllieux2) or die(mysql_error());

		$sqlcodes2 ="SELECT * FROM sp_codes_batiments, sp_rel_batiment_organisme WHERE batiment_id=code_batiment_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY code_batiment_nom ASC";
		$rescodes2 = mysql_query($sqlcodes2) or die(mysql_error());
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Sciences Po | Événements : administration</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
	<link href="jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	<script src="sample.js" type="text/javascript"></script>
	<script type="text/javascript" src="tools.js"></script>
	
	<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="tiny_mce/jquery.tinymce.js"></script>
</head>

<body>
<div id="page">
	    <?php include("top.php"); ?>
    <div id="menu">
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
		<p class="intro_modif">Modification de:</p>
		<?php
			$mois = retournerMoisToutesLettres(date("n", $row['evenement_date']));
			echo "<h3>".date("d", $row['evenement_date'])." ".$mois." ".date("Y", $row['evenement_date'])." : <em>".$row['evenement_titre']."</em></h3>";
			
			if($erreur!=""){
				echo '<p class="erreur">'.$erreur.'</p>';
			}
			
			$sql2 ="SELECT * FROM sp_sessions WHERE evenement_id = '".$_GET['id']."'";
			$res2 = mysql_query($sql2) or die(mysql_error());
			$row2 = mysql_fetch_array($res2);
		?>
		<div class="exports">
			<?php
				$sqlcountsessions = mysql_query("SELECT COUNT(*) AS nb FROM sp_sessions WHERE evenement_id='".$_GET['id']."'");
				$rescountsessions = mysql_fetch_array($sqlcountsessions);
				if($rescountsessions['nb']==1 && $row2['session_type_inscription']==2){
			?>
			
					<a href="exportcsv.php?session_id=<?php echo $row2['session_id'];?>&amp;export=2" target="_blank" title="XML : récupérer la liste des inscrits pour l'application ScanEvent"><img src="img/xml.png" alt="XML"/></a><a href="exportcsv.php?session_id=<?php echo $row2['session_id'];?>&amp;export=0" target="_blank" title="HTML : visualiser et modifier le listing des inscrits"><img src="img/html.png" alt="HTML"/></a><a href="exportcsv.php?session_id=<?php echo $row2['session_id'];?>&amp;export=1" target="_blank" title="CSV : récupérer la liste des inscrits pour l'ouvrir dans Excel"><img src="img/csv.png" alt="CSV"/></a>
			<?php
				}
			?>
			
			<div class="places">
				<?php
				
				if($rescountsessions['nb']==1 && $row2['session_type_inscription']==2){
					$totalInterne = $row2['session_places_internes_totales'];
					$totalInternePrises = $row2['session_places_internes_prises'];
					$totalExterne = $row2['session_places_externes_totales'];
					$totalExternePrises = $row2['session_places_externes_prises'];
			?>
					<p><span class="prises"><?php echo $totalInternePrises;?></span><span>/<?php echo $totalInterne;?> INT</span></p><br/>
					<p><span class="prises"><?php echo $totalExternePrises;?></span><span>/<?php echo $totalExterne;?> EXT</span></p>
				<?php
					}
					else{
						if(isset($rowSession['session_type_inscription']) && $rowSession['session_type_inscription']==2){
				?>
							<p><strong><?php echo $rescountsessions['nb'];?> SESSIONS</strong></p>
				<?php
						}
					}
				?>
			</div>
		</div>
     	<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']."?id=".$_GET['id']."&menu_actif=evenements"?>">
			<?php
				$sql3 ="SELECT * FROM sp_users WHERE user_id = '".$row['evenement_user_id']."'";
				$res3 = mysql_query($sql3) or die(mysql_error());
				$row3 = mysql_fetch_array($res3);
				
				if($row['evenement_statut']==4){
			?>
					<p class="createur">Créé par : <?php echo $row3['user_nom']." (<a href=\"mailto:".$row3['user_email']."\" class=\"createur\">".$row3['user_email']."</a>)";?></p>
			<?php		
				}
				else{
			?>
					<p class="createur">Créé par : <?php echo $row3['user_nom']." / ".$row3['user_login']?></p>
			<?php		
				}
			?>
			
			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer"/>
			<fieldset>
				<p>
					<label for="evenement_statut" class="inline">Statut :</label>
					<select name="evenement_statut" id="evenement_statut">
						<option value="1"<?php if($row['evenement_statut']==1){echo "selected=\"selected\"";} ?>>Brouillon</option>
						<option value="2"<?php if($row['evenement_statut']==2){echo "selected=\"selected\"";} ?>>Caché</option>
						<option value="3"<?php if($row['evenement_statut']==3){echo "selected=\"selected\"";} ?>>Publié</option>
						<option value="4"<?php if($row['evenement_statut']==4){echo "selected=\"selected\"";} ?>>Soumission</option>
					</select>
				</p>
			</fieldset>
			
			
			<fieldset>
				<p class="legend">informations sur l'événement</p>
					<?php
						if(isset($_GET['evenement_groupe']) && $_GET['evenement_groupe']!=""){
					?>
							<input type="hidden" name="evenement_groupe" id="evenement_groupe" value="<?php echo $_GET['evenement_groupe']; ?>"/>
					<?php
						} 
						else{
					?>
							<input type="hidden" name="evenement_groupe" id="evenement_groupe" value="<?php echo $row['evenement_groupe_id']; ?>"/>
					<?php		
						}
					?>
			   					
				<p>
					<label for="evenement_titre">Titre* :</label>
					<input name="evenement_titre" type="text" class="inputField french" id="evenement_titre" value="<?php echo htmlentities($row['evenement_titre'], ENT_QUOTES, "UTF-8");?>"/>
					<input name="evenement_titre_en" type="text" class="inputField english inputdroit" id="evenement_titre_en" value="<?php echo htmlentities($row['evenement_titre_en'], ENT_QUOTES, "UTF-8");?>"/>
				</p>
				
				<p>
					<label for="evenement_texte">Description* :</label>
					<textarea name="evenement_texte" cols="80" rows="4" class="inputField tinymce french" id="evenement_texte"><?php echo $row['evenement_texte'] ?></textarea>
					<textarea name="evenement_texte_en" cols="80" rows="4" class="inputField tinymce english inputdroit" id="evenement_texte_en"><?php echo $row['evenement_texte_en'] ?></textarea>
				</p>
				
				<p>
					<label for="evenement_resume">Texte Newsletter :</label>
					<textarea name="evenement_resume" rows="10" cols="20" class="inputField tinymce french" id="evenement_resume"><?php echo $row['evenement_resume'] ?></textarea>
					<textarea name="evenement_resume_en" rows="10" cols="20" class="inputField tinymce english inputdroit" id="evenement_resume_en"><?php echo $row['evenement_resume_en'] ?></textarea>
				</p>
				
				<p>
					<label for="evenement_organisateur">Organisateur : </label>
					<input type="text" name="evenement_organisateur" value="<?php echo $row['evenement_organisateur'] ?>" class="inputField french" id="evenement_organisateur"/>
					<input type="text" name="evenement_organisateur_en" value="<?php echo $row['evenement_organisateur_en'] ?>" class="inputField english inputdroit" id="evenement_organisateur_en"/>
				</p>
				
				<p>
					<label for="evenement_coorganisateur">Co-organisateur : </label>
					<input type="text" name="evenement_coorganisateur" value="<?php echo $row['evenement_coorganisateur'] ?>" class="inputField french" id="evenement_coorganisateur"/>
					<input type="text" name="evenement_coorganisateur_en" value="<?php echo $row['evenement_coorganisateur_en'] ?>" class="inputField english inputdroit" id="evenement_coorganisateur_en"/>
				</p>

				<p>
					<label for="evenement_rubrique" class="inline">Rubrique* :</label>
					<select name="evenement_rubrique" id="evenement_rubrique">
						<option value="-1">Choisir</option>
					<?php
						while($rowGroupe = mysql_fetch_array($resGroupes)){ 
					?>
							<optgroup label="<?php echo $rowGroupe['groupe_libelle'];?>">
					<?php
							
							$sqlrubriques ="SELECT * FROM sp_rubriques WHERE rubrique_groupe_id='".$rowGroupe['groupe_id']."' ORDER BY rubrique_titre ASC";
							$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
							while($rowrubrique = mysql_fetch_array($resrubriques)){
					?>
								<option value="<?php echo $rowrubrique['rubrique_id'];?>" <?php if($row['evenement_rubrique']==$rowrubrique['rubrique_id']){echo "selected=\"selected\"";} ?>><?php echo $rowrubrique['rubrique_titre'];?></option>
					<?php
							}
						}
					?>
					</select>
				</p>
				

				<p class="legend">Mots-clés :</p>
				<p> 
					<?php         
						/*$sqlGetOrganisme2 ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo, sp_evenements as spe WHERE spe.evenement_groupe_id=spg.groupe_id AND spg.groupe_organisme_id=spo.organisme_id AND evenement_id='".$_GET['id']."'";
						$resGetOrganisme2= mysql_query($sqlGetOrganisme2) or die(mysql_error());
						$rowGetOrganisme2 = mysql_fetch_array($resGetOrganisme2);*/
 
						while($rowKeyword = mysql_fetch_array($sqlKeywords)){ 
							$sqlRels ="SELECT * FROM sp_rel_evenement_keyword WHERE evenement_id='".$_GET['id']."'";
							$resRels= mysql_query($sqlRels) or die(mysql_error());
							$appartient=false;
							while($rowRel = mysql_fetch_array($resRels)){
								if($rowRel['keyword_id']==$rowKeyword['keyword_id']){
									$appartient=true;
								}
							}
					?>
							<input type="checkbox" name="keywords[]" value="<?php echo $rowKeyword['keyword_id'];?>" id="keyword_<?php echo $rowKeyword['keyword_id'];?>" <?php if($appartient){echo "checked";}?> /><label for="keyword_<?php echo $rowKeyword['keyword_id'];?>" class="checkbox" ><?php echo $rowKeyword['keyword_nom'];?></label>
					<?php
						}
					?>
				</p>

				<p>
					<label for="evenement_facebook" class="inline">Publier sur Facebook :</label>
					<select name="evenement_facebook" id="evenement_facebook">
						<option value="1"<?php if($row['evenement_facebook']==1){echo "selected=\"selected\"";} ?>>Oui</option>
						<option value="0"<?php if($row['evenement_facebook']==0){echo "selected=\"selected\"";} ?>>Non</option>
					</select>
				</p>
				<p>
					<label for="evenement_externe" class="inline">Inscription externe visible :</label>
					<select name="evenement_externe" id="evenement_externe">
						<option value="1"<?php if($row['evenement_externe']==1){echo "selected=\"selected\"";} ?>>Oui</option>
						<option value="0"<?php if($row['evenement_externe']==0){echo "selected=\"selected\"";} ?>>Non</option>
					</select>
				</p>
				<p class="legend">Partager avec les Groupes :</p>
				<p> 
					<?php         
						$rowGetOrganisme2['organisme_id'] = isset($rowGetOrganisme2['organisme_id']) ? $rowGetOrganisme2['organisme_id'] : NULL;

						$sqlGroupes ="SELECT * FROM sp_groupes WHERE groupe_organisme_id!='".$rowGetOrganisme2['organisme_id']."' ORDER BY groupe_libelle ASC";
						$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
						while($rowGroupe = mysql_fetch_array($resGroupes)){ 
							$sqlRels ="SELECT * FROM sp_rel_evenement_groupe WHERE evenement_id='".$_GET['id']."'";
							$resRels= mysql_query($sqlRels) or die(mysql_error());
							$appartient=false;
							while($rowRel = mysql_fetch_array($resRels)){
								if($rowRel['groupe_id']==$rowGroupe['groupe_id']){
									$appartient=true;
								}
							}
					?>
							<input type="checkbox" name="groupes[]" value="<?php echo $rowGroupe['groupe_id'];?>" id="groupe_<?php echo $rowGroupe['groupe_id'];?>" <?php if($appartient){echo "checked";}?> /><label for="groupe_<?php echo $rowGroupe['groupe_id'];?>" class="checkbox" ><?php echo $rowGroupe['groupe_libelle'];?></label>
					<?php
						}
					?>
				</p>
				
			</fieldset>
			
			<?php	
					$jour_debut = date("d/m/Y",$row2['session_debut']);
					$heure_debut = date("H:i",$row2['session_debut']);

					$jour_fin = date("d/m/Y",$row2['session_fin']);
					$heure_fin = date("H:i",$row2['session_fin']);

					if($heure_fin=="23:59"){
						$heure_fin="inconnue";
					}
			?>
			<fieldset>
				<p>
					<label for="session_date_debut" class="inline">Date de début* : </label>
					<input name="session_date_debut" type="text" class="inputFieldShort datepicker" id="session_date_debut" value="<?php echo $jour_debut; ?>"/>
					<input name="session_date_fin" type="text" class="inputFieldShort datepicker inputdroit" id="session_date_fin" value="<?php echo $jour_fin; ?>"/>
					<label for="session_date_fin" class="inline labeldroit">Date de fin : </label>
					
				</p>
				<p>
					<label for="session_heure_debut" class="inline">Horaire de début* :</label>
					<input name="session_heure_debut" type="text" id="session_heure_debut" class="inputFieldShort" value="<?php echo $heure_debut; ?>"/>
					<input name="session_heure_fin" type="text" class="inputFieldShort inputdroit" id="session_heure_fin" value="<?php echo $heure_fin; ?>"/>
					<label for="session_heure_fin" class="inline labeldroit">Horaire de fin : </label>
				</p>

				<p id="slider_heure_debut"></p>
				<p id="slider_heure_fin" class="inputdroit"></p>	
			</fieldset>
			
			<fieldset>
				<p>
					<label for="session_lien" class="inline">Adresse du lien :</label>
					<input name="session_lien" type="text" class="inputField french" id="session_lien" value="<?php echo $row2['session_lien']; ?>"/>
					<input name="session_lien_en" type="text" class="inputField inputdroit english" id="session_lien_en" value="<?php echo $row2['session_lien_en']; ?>"/>
				</p>
				
				<p>
					<label for="session_texte_lien">Texte du lien :</label>
					<input type="text" name="session_texte_lien" value="<?php echo $row2['session_texte_lien']; ?>" class="inputField french" id="session_texte_lien"/>
					<input type="text" name="session_texte_lien_en" value="<?php echo $row2['session_texte_lien_en']; ?>" class="inputField inputdroit english" id="session_texte_lien_en"/>
				</p>
				<p>
					<label>Image :</label>
					
					<?php
						$extension = getExtension($row['evenement_image']);
						$nom_image = "mini-image".$extension;
						$chemin = "upload/photos/evenement_".$_GET['id']."/".$nom_image;
					?>
					
					<input type="file" name="evenement_image" id="evenement_image"/>
					<a href="crop.php?id=<?php echo $_GET['id'];?>"><img src="<?php echo $chemin;?>?cache=<?php echo time(); ?>" alt=""/></a>
					<input type="hidden" name="image_cachee" id="image_cachee" value="<?php echo $row['evenement_image']; ?>"/>
					<a href="edit_evenement_unique.php?id=<?php echo $_GET['id'];?>&amp;menu_actif=evenements" onclick="confirmar('edit_evenement_unique.php?fonction=supprimer_image&amp;id=<?php echo $_GET['id'];?>', 'Etes-vous sûr de vouloir supprimer cette image?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>
					
				</p>	
					
			</fieldset>
			
			<!-- Bloc liste des médias-->
			<?php
			
			
			$sqlcountMedias = mysql_query("SELECT COUNT(*) AS nb FROM sp_medias WHERE evenement_id='".$_GET['id']."'");
			$rescountMedias = mysql_fetch_array($sqlcountMedias); 
			if($rescountMedias['nb']>0){  
			?>
				<fieldset>
					<p><label>Liste des médias</label></p>
					<?php
					$sqlMedias ="SELECT * FROM sp_medias WHERE evenement_id='".$_GET['id']."'";
					$resMedias = mysql_query($sqlMedias) or die(mysql_error());
					while($rowMedia = mysql_fetch_array($resMedias)){
                    ?>
                    	<p> 
							<a href="upload/medias/evenement_<?php echo $_GET['id']; ?>/<?php echo $rowMedia['media_fichier'];?>" target="_blank"><?php echo $rowMedia['media_fichier'];?></a>
							
							<a href="edit_evenement_unique.php?fonction=supprimer_document&amp;id=<?php echo $_GET['id'];?>&amp;menu_actif=evenements&amp;idDoc=<?php echo $rowMedia['media_id'];?>" onclick="confirmar('edit_evenement_unique.php?fonction=supprimer_document&amp;idDoc=<?php echo $rowMedia['media_id'];?>', 'Etes-vous sûr de vouloir supprimer ce document?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>
						</p>
					<?php
					}
					?>
				   		
				</fieldset>
			<?php
			}
			?>
 
			<input type="button" name="button" value="Ajouter un média" class="buttonenregistrer lien_creation_medias" />
			<p>&nbsp;</p>
			
			<!-- Bloc d'ajout d'un média-->
			<div id="bloc_creation_media">
					<fieldset>
						<p>
							<label for="evenement_media" class="inline">Fichier : </label>
							<input type="file" name="evenement_media" id="evenement_media"/>
						</p>
						<p> 
							<label for="evenement_media_nom" class="inline">Nom du fichier : </label>
							<input type="text" name="evenement_media_nom" id="evenement_media_nom" class="inputField"/>
						</p>
					</fieldset>
					<input name="media_evenement_id" type="hidden" id="media_evenement_id" value="<?php echo $row['evenement_id'];?>" />  
					<input type="submit" name="btMedia" value="Ajouter"  class="buttonenregistrer"/> 
			</div>
			<!-- fin du bloc d'ajout d'un média-->  
			
			<p>&nbsp;</p>
			<fieldset>
				<p>
					<label for="session_langue" class="inline">Langue :</label>
					<select name="session_langue" id="session_langue">
					<?php
						foreach($langues_evenement as $cle => $valeur){
							echo '<option value="'.$valeur.'"';
							if($row2['session_langue']==$valeur){echo "selected=\"selected\"";}
							echo '>'.$cle.'</option>';
						}
					?>
					</select>
				</p>
				<p>
					<label for="session_lieu">Lieu / Salle / Amphi :</label>
					<select name="session_lieu" id="session_lieu" style="width:250px;">
					<?php
						$estsalle = false;
						while($rowlieu = mysql_fetch_array($reslieux)){
							echo '<option value="'.$rowlieu['lieu_id'].'" ';
							if($row2['session_lieu']==$rowlieu['lieu_id']){
								echo "selected=\"selected\"";
								$estsalle =true;
							}
							echo '>'.$rowlieu['lieu_nom'].'</option>';
						}
					   
						if($estsalle){
							echo '<option value="-1">aucun</option>';
						}
						else{
							echo '<option value="-1" selected="selected">aucun</option>';
						}
					?>	
					</select> 
					
				</p>
				<p>
					<label for="session_code_batiment">Code du bâtiment :</label>
					<select name="session_code_batiment" id="session_code_batiment" style="width:300px;">
					<?php
						$estbatiment = false;
						while($rowcode = mysql_fetch_array($rescodes)){
							echo '<option value="'.$rowcode['code_batiment_id'].'" ';
							if($row2['session_code_batiment']==$rowcode['code_batiment_id']){
								echo "selected=\"selected\"";
								$estbatiment = true;
							}
							echo '>'.$rowcode['code_batiment_nom'].' => '.stripslashes($rowcode['code_batiment_adresse']).'</option>';
							
						}
					    
						if($estbatiment){
							echo '<option value="-1">aucun</option>';
						}
						else{
							echo '<option value="-1" selected="selected">aucun</option>';
						}
					?>
					</select>
				   
				</p>
			
				<p>
					<label for="session_adresse1" class="inline">Nom du lieu : </label>
					<input name="session_adresse1" type="text" class="inputField" id="session_adresse1" value="<?php echo $row2['session_adresse1'] ?>"/>
				</p>
				<p>
					<label for="session_adresse2" class="inline">Adresse : </label>
					<textarea name="session_adresse2" class="textareaField" cols="20" rows="2" id="session_adresse2"><?php echo stripslashes($row2['session_adresse2']); ?></textarea>
				</p>
			</fieldset>
			
			<fieldset>
				<p>
					<label for="session_type_inscription">Type d'inscription : </label>
					<select name="session_type_inscription" id="session_type_inscription">
						<option value="1" <?php if($row2['session_type_inscription']==1){echo "selected=\"selected\"";}?>>Entrée libre</option>
						<option value="2" <?php if($row2['session_type_inscription']==2){echo "selected=\"selected\"";}?>>Inscription obligatoire par la plateforme</option>
						<option value="3" <?php if($row2['session_type_inscription']==3){echo "selected=\"selected\"";}?>>Inscription obligatoire par mail ou autre</option>
					</select>
					
					<?php
						if($row2['session_type_inscription']==3){
					?>	
							<span id="champ_complement">
								<label for="session_complement_type_inscription">Complément : </label>
								<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value="<?php echo $row2['session_complement_type_inscription']; ?>"/>
							</span>
					<?php
						}else{
					?>
							<span id="champ_complement" style="display:none">
								<label for="session_complement_type_inscription">Complément : </label>
								<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value=""/>
							</span>
					<?php
						}
					?>
					
				</p>
				<p>
					<label for="session_code_externe" class="inline">Code d'inscription externe :</label>
					<input name="session_code_externe" type="text" class="inputFieldTiny chiffre" id="session_code_externe" value="<?php echo $row2['session_code_externe']; ?>"/>
				</p>
				<p>
					<label for="session_traduction" class="inline">Traduction simultanée :</label>
					<select name="session_traduction" id="session_traduction">
						<option value="1" <?php if($row2['session_traduction']==1){echo "selected=\"selected\"";}?>>Oui</option>
						<option value="0" <?php if($row2['session_traduction']==0){echo "selected=\"selected\"";}?>>Non</option>
					</select>
				</p>
				
				<?php
				if($row2['session_type_inscription']==2){
				?>
					<p>
						<label class="inline">Inscription interne :</label>
						<a href="<?php echo $rowGetOrganisme['organisme_url_front'];?>inscription/inscription.php?id=<?php echo $row2['session_id'];?>" class="lien_inscription" ><?php echo $rowGetOrganisme['organisme_url_front'];?>inscription/inscription.php?id=<?php echo $row2['session_id'];?></a>
					</p>


					<p>
						<label class="inline">Inscription externe :</label>
					<?php
						if($row2['session_code_externe']!=""){
					?>
						<a href="<?php echo $rowGetOrganisme['organisme_url_front'];?>inscription_externe.php?code=<?php echo $row2['session_code_externe'];?>&amp;session=<?php echo $row2['session_id'];?>&amp;evenement=<?php echo $row['evenement_id'];?>" class="lien_inscription" ><?php echo $rowGetOrganisme['organisme_url_front'];?>inscription_externe.php?code=<?php echo $row2['session_code_externe'];?>&amp;session=<?php echo $row2['session_id'];?>&amp;evenement=<?php echo $row['evenement_id'];?></a>
					<?php		
						}
					?>
					</p>
				<?php
				}
				?>

				<p>
					<span class="gauche">Ouvert/Fermé</span>
					<span class="centre">Nbr de places internes</span>
					<span class="droite">Nbr de places externes</span>
				</p>
				
				<p>
					<label for="session_statut_inscription" class="inline">Amphithéâtre : </label>
					<input name="session_statut_inscription" type="checkbox" id="session_statut_inscription" value="1" <?php if($row2['session_statut_inscription']==1){echo "checked";}?>/>
					<input name="session_places_internes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales" value="<?php echo $row2['session_places_internes_totales']; ?>"/>
					<input name="session_places_externes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales" value="<?php echo $row2['session_places_externes_totales']; ?>"/>
				</p>
				
				<p>
					<label for="session_statut_vision" class="inline">Retransmission :</label>
					<input name="session_statut_vision" type="checkbox" id="session_statut_vision" value="1" <?php if($row2['session_statut_visio']==1){echo "checked";}?>/>
					<input name="session_places_internes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_vision" value="<?php echo $row2['session_places_internes_totales_visio']; ?>"/>
					<input name="session_places_externes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_vision" value="<?php echo $row2['session_places_externes_totales_visio']; ?>"/>	
				
				</p>
			</fieldset>
			<input type="button" name="button" value="Supprimer" class="buttonsupprimer" onclick="confirmar('edit_evenement.php?delete=<?php echo $_GET['id'];?>&amp;id=<?php echo $_GET['id'];?>', 'Etes-vous sûr de vouloir effacer cet événement? Les sessions qui lui sont associées seront aussi supprimées.')"/>
			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer" />
			<input type="button" name="button" value="Créer une session" class="buttonenregistrer lien_creation_session" />
			<input name="evenement_id" type="hidden" id="evenement_id" value="<?php echo $row['evenement_id'];?>" />
		</form>
		
		
		<div id="bloc_creation">
		
			<form id="form" name="form" method="post" action="#">
				<fieldset>
					<p>
						<label for="session_nom_creation" class="inline">Titre de la session : </label>
						<input name="session_nom_creation" type="text" class="inputField french" id="session_nom_creation" value=""/>
						<input name="session_nom_en_creation" type="text" class="inputField english inputdroit" id="session_nom_en_creation" value=""/>
					</p>	
				
					<p>
						<label for="session_date_debut_creation" class="inline">Date de début : </label>
						<input name="session_date_debut_creation" type="text" class="inputFieldShort datepicker" id="session_date_debut_creation" value="<?php echo date('d/m/Y');?>"/>
						<input name="session_date_fin_creation" type="text" class="inputFieldShort datepicker inputdroit" id="session_date_fin_creation" value="<?php echo date('d/m/Y');?>"/>
						<label for="session_date_fin_creation" class="inline labeldroit">Date de fin : </label>

					</p>
					<p>
						<label for="session_heure_debut_creation" class="inline">Horaire de début :</label>
						<input name="session_heure_debut_creation" type="text" id="session_heure_debut_creation" class="inputFieldShort" value="<?php echo $heure_debut; ?>"/>
						<input name="session_heure_fin_creation" type="text" class="inputFieldShort inputdroit" id="session_heure_fin_creation" value="<?php echo $heure_fin; ?>"/>
						<label for="session_heure_fin_creation" class="inline labeldroit">Horaire de fin : </label>
					</p>

					<p id="slider_heure_debut_creation"></p>
					<p id="slider_heure_fin_creation" class="inputdroit"></p>
					
					<div class="separateur">&nbsp;</div>
					<p>
						<label for="session_lien_creation" class="inline">Adresse du lien :</label>
						<input name="session_lien_creation" type="text" class="inputField french" id="session_lien_creation" value=""/>
						<input name="session_lien_en_creation" type="text" class="inputField inputdroit english" id="session_lien_en_creation" value=""/>
					</p>

					<p>
						<label for="session_texte_lien_creation">Texte du lien :</label>
						<input type="text" name="session_texte_lien_creation" value="" class="inputField french" id="session_texte_lien_creation"/>
						<input type="text" name="session_texte_lien_en_creation" value="" class="inputField inputdroit english" id="session_texte_lien_en_creation"/>
					</p>
					<div class="separateur">&nbsp;</div>
					<p>
						<label for="session_langue_creation" class="inline">Langue :</label>
						<select name="session_langue_creation" id="session_langue_creation">
						<?php
							foreach($langues_evenement as $cle => $valeur){
								echo '<option value="'.$valeur.'">'.$cle.'</option>';
							}
						?>
						</select>
					</p>
					<p>
						<label for="session_lieu_creation">Lieu / Salle / Amphi :</label>
						<select name="session_lieu_creation" id="session_lieu_creation"  style="width:250px;">
						<?php
							while($rowlieu = mysql_fetch_array($reslieux2)){
								echo '<option value="'.$rowlieu['lieu_id'].'">'.$rowlieu['lieu_nom'].'</option>';
							}
						?>
							<option value="-1" selected="selected">aucun</option>
						</select>
						
					</p>
					<p>
						<label for="session_code_batiment_creation">Code du bâtiment :</label>
						<select name="session_code_batiment_creation" id="session_code_batiment_creation"  style="width:300px;">
						<?php
							while($rowcode = mysql_fetch_array($rescodes2)){
								echo '<option value="'.$rowcode['code_batiment_id'].'">'.$rowcode['code_batiment_nom'].' => '.stripslashes($rowcode['code_batiment_adresse']).'</option>';
							}
						    
						?>
							<option value="-1" selected="selected">aucun</option>
						</select>
					   
					</p>
					
					<p>
						<label for="session_adresse1_creation" class="inline">Nom du lieu : </label>
						<input name="session_adresse1_creation" type="text" class="inputField" id="session_adresse1_creation" value=""/>
					</p>
					<p>
						<label for="session_adresse2_creation" class="inline">Adresse : </label>
						<textarea name="session_adresse2_creation" class="textareaField" cols="20" rows="2" id="session_adresse2_creation"></textarea>
					</p>
					
					<div class="separateur">&nbsp;</div>
					
					<p>
						<label for="session_type_inscription_creation">Type d'inscription : </label>
						<select name="session_type_inscription_creation" id="session_type_inscription_creation">
							<option value="1">Entrée libre</option>
							<option value="2">Inscription obligatoire par la plateforme</option>
							<option value="3">Inscription obligatoire par mail ou autre</option>
						</select>
						<span id="champ_complement_creation" style="display:none">
							<label for="session_complement_type_inscription_creation">Complément : </label>
							<input type="text" id="session_complement_type_inscription_creation" name="session_complement_type_inscription_creation" class="inputField" value="" />
						</span>
					</p>
					<p>
						<label for="session_code_externe_creation" class="inline">Code d'inscription externe :</label>
						<input name="session_code_externe_creation" type="text" class="inputFieldTiny chiffre" id="session_code_externe_creation" value="<?php genereCode();?>"/>
					</p>
					
					<p>
						<label for="session_traduction_creation" class="inline">Traduction simultanée :</label>
						<select name="session_traduction_creation" id="session_traduction_creation">
							<option value="1">Oui</option>
							<option value="0" selected="selected">Non</option>
						</select>
					</p>
					
					<p>
						<span class="gauche">Ouvert/Fermé</span>
						<span class="centre">Nbr de places internes</span>
						<span class="droite">Nbr de places externes</span>
					</p>

					<p>
						<label for="session_statut_inscription_creation" class="inline">Amphithéâtre : </label>
						<input name="session_statut_inscription_creation" type="checkbox" id="session_statut_inscription_creation" value="1"/>
						<input name="session_places_internes_totales_creation" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_creation" value=""/>
						<input name="session_places_externes_totales_creation" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_creation" value=""/>
					</p>

					<p>
						<label for="session_statut_vision_creation" class="inline">Retransmission :</label>
						<input name="session_statut_vision_creation" type="checkbox" id="session_statut_vision_creation" value="1"/>
						<input name="session_places_internes_totales_vision_creation" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_vision_creation" value=""/>
						<input name="session_places_externes_totales_vision_creation" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_vision_creation" value=""/>	

					</p>

					<input name="type_saisie_creation" type="hidden" id="type_saisie_creation" value="creation" />
					<input name="evenement_id_creation" type="hidden" id="evenement_id_creation" value="<?php echo $_GET['id']; ?>" />
				</fieldset>
				<input type="submit" name="button" value="Enregistrer"  class="buttonenregistrer"  />
			</form>
		</div>	
	</div>
</div>
<script type="text/javascript">
	$(window).load(function(){
	
		$( "#slider_heure_debut_creation" ).slider({
					value:47,
					min: 0,
					max: 95,
					slide: function( event, ui ) {
						var totalMinutes = ui.value;
						var heures = Math.floor(totalMinutes / 4);
						if(heures<10){
							heures="0"+heures;
						}
						var minutes = (totalMinutes % 4)*15;
						if(minutes==0){
							minutes="00";
						}
						$( "#session_heure_debut_creation" ).val( heures+":"+minutes );
					}
				});
		$( "#session_heure_debut_creation" ).val( "12:00" );
				
		$( "#slider_heure_fin_creation" ).slider({
					value:47,
					min: 0,
					max: 96,
					slide: function( event, ui ) {
						if(ui.value!=96){
							var totalMinutes = ui.value;
							var heures = Math.floor(totalMinutes / 4);
							if(heures<10){
								heures="0"+heures;
							}
							var minutes = (totalMinutes % 4)*15;
							if(minutes==0){
								minutes="00";
							}
							$( "#session_heure_fin_creation" ).val( heures+":"+minutes );
						}
						else{
							$( "#session_heure_fin_creation" ).val("inconnue");
						}
					}
				});
		$( "#session_heure_fin_creation" ).val( "12:00" );
		
		
		$.datepicker.setDefaults($.datepicker.regional['fr']);
		//$('.datepicker').datepicker($.datepicker.regional['fr'] );
		var dateDuJour = new Date();
		
		$('.datepicker_creation').datepicker({
			onSelect:function(dateText, inst){
				if($('#session_date_debut_creation').val()!=""){
					var tableauDateDebut=$('#session_date_debut_creation').val().split("/");
					var dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
					$('#session_date_fin_creation').datepicker( "option", "minDate", dateBorneBasse );
				}
				else{
					$('#session_date_fin_creation').datepicker( "option", "minDate", dateDuJour );
				}

				if($('#session_date_fin_creation').val()!=""){
					var tableauDateFin=$('#session_date_fin_creation').val().split("/");
					var dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
					$('#session_date_debut_creation').datepicker( "option", "maxDate", dateBorneHaute );
				}
				else{
					$('#session_date_debut_creation').datepicker( "option", "minDate", dateDuJour );
				}
			}
		,minDate: dateDuJour
		});
		
		
		$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : 'tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "pastetext,|,bold,italic,underline,forecolor,|,bullist,numlist,|,link,unlink,|,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			
			// Example content CSS (should be your site CSS)
			content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			template_external_list_url : "lists/template_list.js",
			external_link_list_url : "lists/link_list.js",
			external_image_list_url : "lists/image_list.js",
			media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}
		});
					
		$( "#slider_heure_debut" ).slider({
					value:47,
					min: 0,
					max: 95,
					slide: function( event, ui ) {
						var totalMinutes = ui.value;
						var heures = Math.floor(totalMinutes / 4);
						if(heures<10){
							heures="0"+heures;
						}
						var minutes = (totalMinutes % 4)*15;
						if(minutes==0){
							minutes="00";
						}
						$( "#session_heure_debut" ).val( heures+":"+minutes );
					}
				});

		$( "#slider_heure_fin" ).slider({
					value:47,
					min: 0,
					max: 96,
					slide: function( event, ui ) {
						if(ui.value!=96){
							var totalMinutes = ui.value;
							var heures = Math.floor(totalMinutes / 4);
							if(heures<10){
								heures="0"+heures;
							}
							var minutes = (totalMinutes % 4)*15;
							if(minutes==0){
								minutes="00";
							}
							$( "#session_heure_fin" ).val( heures+":"+minutes );
						}
						else{
							$( "#session_heure_fin" ).val("inconnue");
						}
					}
				});

		$.datepicker.setDefaults($.datepicker.regional['fr']);
		//$('.datepicker').datepicker($.datepicker.regional['fr'] );
		var dateDuJour = new Date();
		
		
		$('.datepicker').datepicker({
			onSelect:function(dateText, inst){
				if($('#session_date_debut').val()!=""){
					tableauDateDebut=$('#session_date_debut').val().split("/");
					dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
					$('#session_date_fin').datepicker( "option", "minDate", dateBorneBasse );
				}
				else{
					$('#session_date_fin').datepicker( "option", "minDate", dateDuJour );
				}

				if($('#session_date_fin').val()!=""){
					tableauDateFin=$('#session_date_fin').val().split("/");
					dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
					$('#session_date_debut').datepicker( "option", "maxDate", dateBorneHaute );
				}
				else{
					$('#session_date_debut').datepicker( "option", "minDate", dateDuJour );
				}
			}
		,minDate: dateDuJour
		});
		
		var tableauDateDebut=$('#session_date_debut').val().split("/");
		var dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
		var tableauDateFin=$('#session_date_fin').val().split("/");
		var dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
		$('#session_date_fin').datepicker( "option", "minDate", dateBorneBasse );
		$('#session_date_debut').datepicker( "option", "maxDate", dateBorneHaute );
		
		$("#session_type_inscription").change(function(e){
			if($("#session_type_inscription").val()==3){
				document.getElementById("champ_complement").style.display="block";
			}
			else{
				document.getElementById("champ_complement").style.display="none";
			}
		});
		
		$("#session_code_batiment").change(function(){
			var selection = document.getElementById('session_code_batiment'); 
		   	var adresse = selection.options[selection.selectedIndex].innerHTML;
			var tableauAdresse=adresse.split("&gt;");
			adresse = tableauAdresse[1];
			document.getElementById("session_adresse2").value = adresse;
		});
        
		//ajout d'une session
		document.getElementById("bloc_creation").style.display="none";
		
		$("input.lien_creation_session").click(function(e){
			e.preventDefault();	
			document.getElementById("bloc_creation").style.display="block";
		});
		
		//ajout de média
		document.getElementById("bloc_creation_media").style.display="none";
		$("input.lien_creation_medias").click(function(e){
			e.preventDefault();	
			document.getElementById("bloc_creation_media").style.display="block";
		});
		
		
		
		
		$("#session_type_inscription_creation").change(function(e){
			if($("#session_type_inscription_creation").val()==3){
				document.getElementById("champ_complement_creation").style.display="block";
			}
			else{
				document.getElementById("champ_complement_creation").style.display="none";
			}
		});
	   
		
		$("#session_code_batiment_creation").change(function(){
			var selection = document.getElementById('session_code_batiment_creation'); 
		   	var adresse = selection.options[selection.selectedIndex].innerHTML;
			var tableauAdresse=adresse.split("&gt;");
			adresse = tableauAdresse[1];
			document.getElementById("session_adresse2_creation").value = adresse;
		});
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
	});
</script>
</body>
</html>
<?php
}
else{
	header('Location:index.php?error=1');
}

?>
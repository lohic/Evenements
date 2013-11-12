<?php
include_once('../vars/config.php');
// security
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
//session_start();
$core = new core();

$erreur="";
//Création d'un événement
if( isset($_POST['evenement_titre']) ){
	$erreur=testChamps($_POST['evenement_titre'], $_POST['evenement_texte'], $_POST['evenement_rubrique'], $_POST['session_date_debut'], $_POST['session_heure_debut']);
	
	if($erreur==""){
		$debutEvenement = 0; 
		$finEvenement = 0;
        
		$tableauHeureDebut = explode(":",$_POST["session_heure_debut"]);
		$tableauDateDebut = explode("/",$_POST["session_date_debut"]);

		$debutEvenement = mktime($tableauHeureDebut[0], $tableauHeureDebut[1],0,$tableauDateDebut[1],$tableauDateDebut[0],$tableauDateDebut[2]);
		$finEvenement = retourneTimestamp($_POST["session_heure_fin"], $_POST["session_date_fin"], $_POST["session_date_debut"]);

		$extension = getExtension($_FILES['evenement_image']['name']);

		if(isExtAuthorized($extension)){
			$photo = 'image'.$extension;
		}
      
		$sql ="INSERT INTO sp_evenements
			VALUES(
				'',
				'".$_POST["evenement_statut"]."',
				'".addslashes($_POST["evenement_organisateur"])."',
				'".addslashes($_POST["evenement_organisateur_en"])."',
				'".addslashes($_POST["evenement_coorganisateur"])."',
				'".addslashes($_POST["evenement_coorganisateur_en"])."',
				'".addslashes($_POST["evenement_rubrique"])."',
				'".addslashes($_POST["evenement_titre"])."',
				'".addslashes($_POST["evenement_titre_en"])."',
				'".mysql_real_escape_string(html_entity_decode( $_POST["evenement_resume"], ENT_NOQUOTES, 'UTF-8' ))."',
				'".mysql_real_escape_string(html_entity_decode( $_POST["evenement_resume_en"], ENT_NOQUOTES, 'UTF-8' ))."',
				'".mysql_real_escape_string(html_entity_decode( $_POST["evenement_texte"], ENT_NOQUOTES, 'UTF-8' ))."',
				'".mysql_real_escape_string(html_entity_decode( $_POST["evenement_texte_en"], ENT_NOQUOTES, 'UTF-8' ))."',
				'".$photo."',
				'',
				'".$debutEvenement."',
				FROM_UNIXTIME(".$debutEvenement."),
				'".$_SESSION['id']."',
				'".$_SESSION['id_actual_group']."',
				'".$_POST["evenement_facebook"]."', 
				'', 
				'".$_SESSION['id']."', 
				'".$_SERVER["REMOTE_ADDR"]."',
				'".$_POST["evenement_externe"]."'
				)";

		mysql_query($sql) or die(mysql_error());
		$lastIdInsert = mysql_insert_id(); 
        
        //créations des liaisons avec les groupes partagés
		for ($i = 0; $i < count($_POST['groupes']); $i++) {
			$sqlinsert ="INSERT INTO sp_rel_evenement_groupe VALUES ('', '".$lastIdInsert."', '".$_POST['groupes'][$i]."')";
			mysql_query($sqlinsert) or die(mysql_error());
		}

		//créations des liaisons avec les mots-clés choisis
		for ($i = 0; $i < count($_POST['keywords']); $i++) {
			$sqlinsert ="INSERT INTO sp_rel_evenement_keyword VALUES ('', '".$lastIdInsert."', '".$_POST['keywords'][$i]."')";
			mysql_query($sqlinsert) or die(mysql_error());
		}

		$sql2 ="INSERT INTO sp_sessions
			VALUES('',
				'".$lastIdInsert."',
				'".addslashes($_POST["evenement_titre"])."',
				'".addslashes($_POST["evenement_titre_en"])."',
				'".$debutEvenement."',
				FROM_UNIXTIME(".$debutEvenement."),
				'".$finEvenement."',
				FROM_UNIXTIME(".$finEvenement."),
				'".$_POST["session_langue"]."',
				'".$_POST['session_lieu']."',
				'".$_POST['session_code_batiment']."',
				'".$_POST["session_lien"]."',
				'".$_POST["session_lien_en"]."',
				'".addslashes($_POST["session_texte_lien"])."',
				'".addslashes($_POST["session_texte_lien_en"])."',
				'".$_POST["session_type_inscription"]."',
				'".addslashes($_POST["session_complement_type_inscription"])."',
				'".$_POST["session_statut_inscription"]."',
				'".$_POST["session_places_internes_totales"]."',
				'',
				'".$_POST["session_places_externes_totales"]."',
				'',
				'".$_POST["session_statut_vision"]."',
				'".$_POST["session_places_internes_totales_vision"]."',
				'',
				'".$_POST["session_places_externes_totales_vision"]."',
				'',
				'".mysql_real_escape_string(html_entity_decode( $_POST["session_adresse1"], ENT_NOQUOTES, 'UTF-8' ))."',
				'".mysql_real_escape_string(html_entity_decode( $_POST["session_adresse2"], ENT_NOQUOTES, 'UTF-8' ))."',
				'',
				'',
				'".$_POST["session_code_externe"]."',
				'".$_POST["session_traduction"]."', 
				'', 
				'".$_SESSION['id']."', 
				'".$_SERVER["REMOTE_ADDR"]."'
				)";

		mysql_query($sql2) or die(mysql_error());

		if($_FILES['evenement_image']['name']!=""){
			mkdir("upload/photos/evenement_".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/photos/evenement_'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['evenement_image']['name']);

			if(isExtAuthorized($extension)){
				$photo = 'image'.$extension;
				$original = 'original'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['evenement_image']['tmp_name'], $file_url.'/'.$photo)){
					@chmod("$file_url/$photo", 0777);
					
					$img="$file_url/$photo";
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
			header( "Location:crop.php?menu_actif=nouvelevenement&id=".$lastIdInsert);
		}
		else{
			header( "Location:list.php?menu_actif=evenements");
		}
	}
}
$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
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
		<?php
		if($erreur!=""){
			echo '<p class="erreur">'.$erreur.'</p>';
		}
		
		?>
		
		<form id="formcreer" name="formcreer" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']."?menu_actif=nouvelevenement"?>">
			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer"/>
			<fieldset>
				<p>
					<label for="evenement_statut" class="inline">Statut :</label>
					<select name="evenement_statut" id="evenement_statut">
						<option value="1"<?php if(isset($_POST['evenement_statut']) && $_POST['evenement_statut']==1){echo "selected=\"selected\"";} ?>>Brouillon</option>
						<option value="2"<?php if(isset($_POST['evenement_statut']) && $_POST['evenement_statut']==2){echo "selected=\"selected\"";} ?>>Caché</option>
						<option value="3"<?php if(isset($_POST['evenement_statut']) && $_POST['evenement_statut']==3){echo "selected=\"selected\"";} ?>>Publié</option>
						<option value="4"<?php if(isset($_POST['evenement_statut']) && $_POST['evenement_statut']==4){echo "selected=\"selected\"";} ?>>Soummission</option>
					</select>
				</p>
			</fieldset>
			
			
			<fieldset>
				<p class="legend">informations sur l'événement</p>
				<p>
					<label for="evenement_titre">Titre* : </label>
					<input name="evenement_titre" type="text" class="inputField french" id="evenement_titre" value="<?php echo isset($_POST["evenement_titre"]) ? $_POST["evenement_titre"] : '' ;?>"/>
					<input name="evenement_titre_en" type="text" class="inputField english inputdroit" id="evenement_titre_en" value="<?php echo isset ($_POST["evenement_titre_en"])? $_POST["evenement_titre_en"]:'' ;?>"/>
				</p>
				
				<p>
					<label for="evenement_texte">Description* :</label>
					<textarea name="evenement_texte" cols="80" rows="4" class="inputField tinymce french" id="evenement_texte"><?php echo isset ($_POST["evenement_texte"])? $_POST["evenement_texte"] : '' ;?></textarea>
					<textarea name="evenement_texte_en" cols="80" rows="4" class="inputField tinymce english inputdroit" id="evenement_texte_en"><?php echo isset($_POST["evenement_texte_en"])?$_POST["evenement_texte_en"]:'' ;?></textarea>
				</p>
				
				<p>
					<label for="evenement_resume">Texte Newsletter :</label>
					<textarea name="evenement_resume" rows="10" cols="20" class="inputField tinymce french" id="evenement_resume"><?php echo isset($_POST["evenement_resume"]) ? $_POST["evenement_resume"]:'' ;?></textarea>
					<textarea name="evenement_resume_en" rows="10" cols="20" class="inputField tinymce english inputdroit" id="evenement_resume_en"><?php echo isset($_POST["evenement_resume_en"]) ? $_POST["evenement_resume_en"]:'' ;?></textarea>
				</p>
				
				<p>
					<label for="evenement_organisateur">Organisateur : </label>
					<input type="text" name="evenement_organisateur" value="<?php echo isset($_POST["evenement_organisateur"]) ? $_POST["evenement_organisateur"]:'' ;?>" class="inputField french" id="evenement_organisateur"/>
					<input type="text" name="evenement_organisateur_en" value="<?php echo isset($_POST["evenement_organisateur_en"]) ? $_POST["evenement_organisateur_en"]:'' ;?>" class="inputField english inputdroit" id="evenement_organisateur_en"/>
				</p>
				
				<p>
					<label for="evenement_coorganisateur">Co-organisateur : </label>
					<input type="text" name="evenement_coorganisateur" value="<?php echo isset($_POST["evenement_coorganisateur"]) ? $_POST["evenement_coorganisateur"] :'' ;?>" class="inputField french" id="evenement_coorganisateur"/>
					<input type="text" name="evenement_coorganisateur_en" value="<?php echo isset($_POST["evenement_coorganisateur_en"])? $_POST["evenement_coorganisateur_en"]:'' ;?>" class="inputField english inputdroit" id="evenement_coorganisateur_en"/>
				</p>



				<p>
					<label for="evenement_rubrique" class="inline">Rubrique* :</label>
					<select name="evenement_rubrique" id="evenement_rubrique">
						<option value="-1" selected="selected">Choisir</option>
					<?php
						while($rowGroupe = mysql_fetch_array($resGroupes)){ 
					?>
							<optgroup label="<?php echo $rowGroupe['groupe_libelle'];?>">
					<?php
							$sqlrubriques ="SELECT * FROM sp_rubriques WHERE rubrique_groupe_id='".$rowGroupe['groupe_id']."' ORDER BY rubrique_titre ASC";
							$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
							while($rowrubrique = mysql_fetch_array($resrubriques)){
					?>
								<option value="<?php echo $rowrubrique['rubrique_id'];?>" <?php if(isset($_POST['evenement_rubrique']) && $_POST['evenement_rubrique']==$rowrubrique['rubrique_id']){echo "selected=\"selected\"";} ?>><?php echo utf8_encode($rowrubrique['rubrique_titre']);?></option>
					<?php
							}
						}  
					?> 
					</select>
				</p>

				<p class="legend">Mots-clés :</p>
				<p> 
					<?php 
						while($rowKeyword = mysql_fetch_array($sqlKeywords)){ 
					?>
							<input type="checkbox" name="keywords[]" value="<?php echo $rowKeyword['keyword_id'];?>" id="keyword_<?php echo $rowKeyword['keyword_id'];?>"/><label for="keyword_<?php echo $rowKeyword['keyword_id'];?>" class="checkbox" ><?php echo $rowKeyword['keyword_nom'];?></label>
					<?php
						}
					?>
				</p>

				<p>
					<label for="evenement_facebook" class="inline">Publier sur Facebook :</label>
					<select name="evenement_facebook" id="evenement_facebook">
						<option value="0" <?php if(isset($_POST['evenement_facebook']) && $_POST['evenement_facebook']==0){echo "selected=\"selected\"";} ?>>Non</option>
						<option value="1" <?php if(isset($_POST['evenement_facebook']) && $_POST['evenement_facebook']==1){echo "selected=\"selected\"";} ?>>Oui</option>
					</select>
				</p>

				<p>
					<label for="evenement_externe" class="inline">Inscription externe visible :</label>
					<select name="evenement_externe" id="evenement_externe">
						<option value="0" <?php if(isset($_POST['evenement_externe']) && $_POST['evenement_externe']==0){echo "selected=\"selected\"";} ?>>Non</option>
						<option value="1" <?php if(isset($_POST['evenement_externe']) && $_POST['evenement_externe']==1){echo "selected=\"selected\"";} ?>>Oui</option>
					</select>
				</p>
				
				<p class="legend">Partager avec les Groupes :</p>
				<p> 
					<?php         
						$sqlGroupes ="SELECT * FROM sp_groupes ORDER BY groupe_libelle ASC";
						$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
						while($rowGroupe = mysql_fetch_array($resGroupes)){ 
					?>
							<input type="checkbox" name="groupes[]" value="<?php echo $rowGroupe['groupe_id'];?>" id="groupe_<?php echo $rowGroupe['groupe_id'];?>"/><label for="groupe_<?php echo $rowGroupe['groupe_id'];?>" class="checkbox" ><?php echo $rowGroupe['groupe_libelle'];?></label>
					<?php
						}
					?>
				</p>
				
				
			</fieldset>
			
			<?php
					if(isset($row2)){

						$jour_debut = date("d/m/Y",$row2['session_debut']);
						$heure_debut = date("H:i",$row2['session_debut']);

						$jour_fin = date("d/m/Y",$row2['session_fin']);
						$heure_fin = date("H:i",$row2['session_fin']);

					}else{
						$jour_debut = date("d/m/Y");
						$heure_debut = date("H:i");

						$jour_fin = date("d/m/Y");
						$heure_fin = date("H:i");
					}

					if($heure_fin=="23:59"){
						$heure_fin="inconnue";
					}
			?>
			<fieldset>
				<p>
					<label for="session_date_debut" class="inline">Date de début* : </label>
					<input name="session_date_debut" type="text" class="inputFieldShort datepicker" id="session_date_debut" value="<?php echo isset($_POST["session_date_debut"]) ? $_POST["session_date_debut"]:date("d/m/Y") ;?>"/>
					<input name="session_date_fin" type="text" class="inputFieldShort datepicker inputdroit" id="session_date_fin" value="<?php if(isset($_POST["session_date_fin"]) && $_POST["session_date_fin"]!=""){echo $_POST["session_date_fin"];}else{echo date("d/m/Y");}?>"/>
					<label for="session_date_fin" class="inline labeldroit">Date de fin : </label>
					
				</p>
				<p>
					<label for="session_heure_debut" class="inline">Horaire de début* :</label>
					<input name="session_heure_debut" type="text" id="session_heure_debut" class="inputFieldShort" value="<?php echo isset($_POST["session_heure_debut"])?$_POST["session_heure_debut"]:'' ;?>"/>
					<input name="session_heure_fin" type="text" class="inputFieldShort inputdroit" id="session_heure_fin" value="<?php echo isset($_POST["session_heure_fin"])?$_POST["session_heure_fin"]:'' ;?>"/>
					<label for="session_heure_fin" class="inline labeldroit">Horaire de fin : </label>
				</p>

				<p id="slider_heure_debut"></p>
				<p id="slider_heure_fin" class="inputdroit"></p>	
			</fieldset>
			
			<fieldset>
				<p>
					<label for="session_lien" class="inline">Adresse du lien :</label>
					<input name="session_lien" type="text" class="inputField french" id="session_lien" value="<?php echo isset($_POST["session_lien"]) ?$_POST["session_lien"]:'' ;?>"/>
					<input name="session_lien_en" type="text" class="inputField inputdroit english" id="session_lien_en" value="<?php echo isset($_POST["session_lien_en"]) ? $_POST["session_lien_en"]:'' ;?>"/>
				</p>
				
				<p>
					<label for="session_texte_lien">Texte du lien :</label>
					<input type="text" name="session_texte_lien" value="<?php echo isset($_POST["session_texte_lien"]) ? $_POST["session_texte_lien"]:'' ;?>" class="inputField french" id="session_texte_lien"/>
					<input type="text" name="session_texte_lien_en" value="<?php echo isset($_POST["session_texte_lien_en"]) ? $_POST["session_texte_lien_en"]:'' ;?>" class="inputField inputdroit english" id="session_texte_lien_en"/>
				</p>
				<p>
					<label for="evenement_image">Image :</label><br /><input type="file" name="evenement_image" id="evenement_image"/><span class="image">L'image doit être en png, jpg ou gif</span>
				</p>	
			   
			</fieldset>

			<fieldset>
				<p>
					<label for="session_langue" class="inline">Langue :</label>
					<select name="session_langue" id="session_langue">
					<?php
						foreach($langues_evenement as $cle => $valeur){
							echo '<option value="'.$valeur.'"';
							if(isset($_POST['session_langue']) && $_POST['session_langue']==$valeur){echo "selected=\"selected\"";}
							echo '>'.$cle.'</option>';
						}
					?>
					</select>
				</p>
				<p>
					<label for="session_lieu">Lieu / Salle / Amphi :</label>
					<select name="session_lieu" id="session_lieu" style="width:250px;">
					<?php
						echo '<option value="-1">aucun</option>';
						while($rowlieu = mysql_fetch_array($reslieux)){
					?>
							<option value="<?php echo $rowlieu['lieu_id'];?>" <?php if(isset($_POST['session_lieu']) && $_POST['session_lieu']==$rowlieu['lieu_id']){echo "selected=\"selected\"";} ?>><?php echo $rowlieu['lieu_nom'];?></option>
					<?php
						}
					?>	
					</select> 
				   
				</p>
				<p>
					<label for="session_code_batiment">Code du bâtiment :</label>
					<select name="session_code_batiment" id="session_code_batiment" style="width:300px;">
					<?php
						echo '<option value="-1" selected="selected">aucun</option>';
						while($rowcode = mysql_fetch_array($rescodes)){
					?>
							<option value="<?php echo $rowcode['code_batiment_id'];?>" <?php if(isset($_POST['session_code_batiment']) && $_POST['session_code_batiment']==$rowcode['code_batiment_id']){echo "selected=\"selected\"";} ?>><?php echo $rowcode['code_batiment_nom']." => ".$rowcode['code_batiment_adresse'];?></option>
					<?php
						}
					?>
					</select>
				   
				</p>
			
				<p>
					<label for="session_adresse1" class="inline">Nom du lieu : </label>
					<input name="session_adresse1" type="text" class="inputField" id="session_adresse1" value="<?php echo isset($_POST["session_adresse1"]) ? $_POST["session_adresse1"]:'' ;?>"/>
				</p>
				<p>
					<label for="session_adresse2" class="inline">Adresse : </label>
					<textarea name="session_adresse2" class="textareaField" cols="20" rows="2" id="session_adresse2"><?php echo isset( $_POST["session_adresse2"])? $_POST["session_adresse2"]:'' ;?></textarea>
				</p>
			</fieldset>
			
			<fieldset>
				<p>
					<label for="session_type_inscription">Type d'inscription : </label>
					<select name="session_type_inscription" id="session_type_inscription">
						<option value="1" <?php if(isset($_POST['session_type_inscription']) && $_POST['session_type_inscription']==1){echo "selected=\"selected\"";} ?>>Entrée libre</option>
						<option value="2" <?php if(isset($_POST['session_type_inscription']) && $_POST['session_type_inscription']==2){echo "selected=\"selected\"";} ?>>Inscription obligatoire par la plateforme</option>
						<option value="3" <?php if(isset($_POST['session_type_inscription']) && $_POST['session_type_inscription']==3){echo "selected=\"selected\"";} ?>>Inscription obligatoire par mail ou autre</option>
					</select>
					
					<?php
						if(isset($_POST['session_type_inscription']) && $_POST['session_type_inscription']==3){
					?>	
							<span id="champ_complement">
								<label for="session_complement_type_inscription">Complément : </label>
								<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value="<?php echo $_POST["session_complement_type_inscription"] ;?>"/>
							</span>
					<?php
						}else{
					?>
							<span id="champ_complement" style="display:none">
								<label for="session_complement_type_inscription">Complément : </label>
								<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value="<?php echo isset($_POST["session_complement_type_inscription"]) ? $_POST["session_complement_type_inscription"] : '' ;?>"/>
							</span>
					<?php
						}
					?>
					
				</p>
				<p>
					<label for="session_code_externe" class="inline">Code d'inscription externe :</label>
					<input name="session_code_externe" type="text" class="inputFieldTiny chiffre" id="session_code_externe" value="<?php genereCode();?>"/>
				</p>
				
				<p>
					<label for="session_traduction" class="inline">Traduction simultanée :</label>
					<select name="session_traduction" id="session_traduction">
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
					<label for="session_statut_inscription" class="inline">Amphithéâtre : </label>
					<input name="session_statut_inscription" type="checkbox" id="session_statut_inscription" value="1"/>
					<input name="session_places_internes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales" value="<?php echo isset($_POST["session_places_internes_totales"]) ? $_POST["session_places_internes_totales"]:'' ;?>"/>
					<input name="session_places_externes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales" value="<?php echo isset($_POST["session_places_externes_totales"]) ? $_POST["session_places_externes_totales"]:'' ;?>"/>
				</p>
				
				<p>
					<label for="session_statut_vision" class="inline">Retransmission :</label>
					<input name="session_statut_vision" type="checkbox" id="session_statut_vision" value="1"/>
					<input name="session_places_internes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_vision" value="<?php echo isset($_POST["session_places_internes_totales_vision"]) ? $_POST["session_places_internes_totales_vision"]:'' ;?>"/>
					<input name="session_places_externes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_vision" value="<?php echo isset($_POST["session_places_externes_totales"]) ? $_POST["session_places_externes_totales"] : '' ;?>"/>	
				
				</p>
			</fieldset>
			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer" />
			<input name="evenement_id" type="hidden" id="evenement_id" value="<?php echo isset($row['evenement_id']) ? $row['evenement_id']:''?>" />
		</form>
	</div>
</div>

<script type="text/javascript">
	$(window).load(function(){
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
		
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
		//$( "#session_heure_debut" ).val( "" );
		
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
		//$( "#session_heure_fin" ).val( "inconnue" );
		
		
		$.datepicker.setDefaults($.datepicker.regional['fr']);
		var dateDuJour = new Date();
	
		$('.datepicker').datepicker({
			onSelect:function(dateText, inst){
				if($('#session_date_debut').val()!=""){
					var tableauDateDebut=$('#session_date_debut').val().split("/");
					var dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
					$('#session_date_fin').datepicker( "option", "minDate", dateBorneBasse );
				}
				else{
					$('#session_date_fin').datepicker( "option", "minDate", dateDuJour );
				}

				if($('#session_date_fin').val()!=""){
					var tableauDateFin=$('#session_date_fin').val().split("/");
					var dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
					$('#session_date_debut').datepicker( "option", "maxDate", dateBorneHaute );
				}
				else{
					$('#session_date_debut').datepicker( "option", "minDate", dateDuJour );
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
	});
	
</script>
</body>
</html>

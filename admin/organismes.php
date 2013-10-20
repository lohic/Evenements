<?php
include_once('../vars/config.php');
// security
include('cookie.php');
// connection to data base
include('connect.php');
// functions library
include('functions.php');

//édition ou ajout d'un organisme
if( isset($_POST['type_saisie_organisme'])){
	if($_POST['type_saisie_organisme']=="modification"){
		$banniere = retournePhoto($_FILES['organisme_banniere_chemin']['name'], $_POST['banniere_cachee']);
		$logo = retournePhoto($_FILES['organisme_logo_chemin']['name'], $_POST['logo_cache']);
		$banniere_facebook = retournePhoto($_FILES['organisme_banniere_facebook_chemin']['name'], $_POST['banniere_facebook_cache']);
		$footer_facebook = retournePhoto($_FILES['organisme_footer_facebook_chemin']['name'], $_POST['footer_facebook_cache']);
		$image_billet = retournePhoto($_FILES['organisme_image_billet']['name'], $_POST['image_billet_cache']);

		$sql ="UPDATE sp_organismes SET
					organisme_nom = '".addslashes($_POST["organisme_nom"])."',
					organisme_banniere_chemin = '".$banniere."',
					organisme_banniere_lien = '".$_POST["organisme_banniere_lien"]."',
					organisme_logo_chemin = '".$logo."',
					organisme_banniere_facebook_chemin = '".$banniere_facebook."',
					organisme_footer_facebook_chemin = '".$footer_facebook."',
					organisme_google_analytics_id = '".$_POST["organisme_google_analytics_id"]."',
					organisme_couleur = '".$_POST["organisme_couleur"]."',
					organisme_editeur_id = '".$_SESSION['id']."',
					organisme_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."',
					organisme_mentions = '".$_POST["organisme_mentions"]."',
					organisme_url_front = '".$_POST["organisme_url_front"]."',
					organisme_image_billet = '".$image_billet."',
					organisme_url_image = '".$_POST["organisme_url_image"]."'
				WHERE organisme_id = '".$_POST['organisme_id']."'";
		mysql_query($sql) or die(mysql_error());
		
		if($_FILES['organisme_banniere_chemin']['name']!=""){
			mkdir("upload/banniere/".$_POST['organisme_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/banniere/'.$_POST['organisme_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_banniere_chemin']['name']);

			if(isExtAuthorized($extension)){
				$banniere = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_banniere_chemin']['tmp_name'], $file_url.'/'.$banniere)){
					@chmod("$file_url/$banniere", 0777);
					$img="$file_url/$banniere";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $banniere";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}
		
		if($_FILES['organisme_logo_chemin']['name']!=""){
			mkdir("upload/logo/".$_POST['organisme_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/logo/'.$_POST['organisme_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_logo_chemin']['name']);

			if(isExtAuthorized($extension)){
				$logo = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_logo_chemin']['tmp_name'], $file_url.'/'.$logo)){
					@chmod("$file_url/$logo", 0777);
					$img="$file_url/$logo";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $logo";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}

		if($_FILES['organisme_banniere_facebook_chemin']['name']!=""){
			mkdir("upload/banniere_facebook/".$_POST['organisme_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/banniere_facebook/'.$_POST['organisme_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_banniere_facebook_chemin']['name']);

			if(isExtAuthorized($extension)){
				$banniere_facebook = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_banniere_facebook_chemin']['tmp_name'], $file_url.'/'.$banniere_facebook)){
					@chmod("$file_url/$banniere_facebook", 0777);
					$img="$file_url/$banniere_facebook";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $banniere_facebook";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}

		if($_FILES['organisme_footer_facebook_chemin']['name']!=""){
			mkdir("upload/footer_facebook/".$_POST['organisme_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/footer_facebook/'.$_POST['organisme_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_footer_facebook_chemin']['name']);

			if(isExtAuthorized($extension)){
				$footer_facebook = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_footer_facebook_chemin']['tmp_name'], $file_url.'/'.$footer_facebook)){
					@chmod("$file_url/$footer_facebook", 0777);
					$img="$file_url/$footer_facebook";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $footer_facebook";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}

		if($_FILES['organisme_image_billet']['name']!=""){
			mkdir("upload/billet/".$_POST['organisme_id']);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/billet/'.$_POST['organisme_id'];
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_image_billet']['name']);

			if(isExtAuthorized($extension)){
				$image_billet = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_image_billet']['tmp_name'], $file_url.'/'.$image_billet)){
					@chmod("$file_url/$image_billet", 0777);
					$img="$file_url/$image_billet";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $image_billet";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}
		
	}

	if($_POST['type_saisie_organisme']=="creation"){
		$extension = getExtension($_FILES['organisme_banniere_chemin_creation']['name']);
		if(isExtAuthorized($extension)){
			$banniere = 'image'.$extension;
		} 
		
		$extension = getExtension($_FILES['organisme_logo_chemin_creation']['name']);
		if(isExtAuthorized($extension)){
			$logo = 'image'.$extension;
		}

		$extension = getExtension($_FILES['organisme_banniere_facebook_chemin_creation']['name']);
		if(isExtAuthorized($extension)){
			$banniere_facebook = 'image'.$extension;
		}

		$extension = getExtension($_FILES['organisme_footer_facebook_chemin_creation']['name']);
		if(isExtAuthorized($extension)){
			$footer_facebook = 'image'.$extension;
		}

		$extension = getExtension($_FILES['organisme_image_billet_creation']['name']);
		if(isExtAuthorized($extension)){
			$image_billet = 'image'.$extension;
		}
        
		$sql ="INSERT INTO sp_organismes VALUES ('', '', '".addslashes($_POST["organisme_nom_creation"])."', '', '".$_POST["organisme_google_analytics_id_creation"]."', '','".$banniere."','".$_POST['organisme_banniere_lien_creation']."','".$logo."','".$banniere_facebook."','".$footer_facebook."', '".$_POST["organisme_couleur_creation"]."', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."', '".$_POST['organisme_mentions_creation']."', '".$_POST['organisme_url_front_creation']."', '".$image_billet."', '".$_POST['organisme_url_image_creation']."')";
		mysql_query($sql) or die(mysql_error());
       	
		$lastIdInsert = mysql_insert_id();


		if($_FILES['organisme_banniere_chemin_creation']['name']!=""){
			mkdir("upload/banniere/".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/banniere/'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_banniere_chemin_creation']['name']);

			if(isExtAuthorized($extension)){
				$banniere = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_banniere_chemin_creation']['tmp_name'], $file_url.'/'.$banniere)){
					@chmod("$file_url/$banniere", 0777);
					$img="$file_url/$banniere";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $banniere";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}
		
		if($_FILES['organisme_logo_chemin_creation']['name']!=""){
			mkdir("upload/logo/".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/logo/'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_logo_chemin_creation']['name']);

			if(isExtAuthorized($extension)){
				$logo = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_logo_chemin_creation']['tmp_name'], $file_url.'/'.$logo)){
					@chmod("$file_url/$logo", 0777);
					$img="$file_url/$logo";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $logo";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}

		if($_FILES['organisme_banniere_facebook_chemin_creation']['name']!=""){
			mkdir("upload/banniere_facebook/".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/banniere_facebook/'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_banniere_facebook_chemin_creation']['name']);

			if(isExtAuthorized($extension)){
				$banniere_facebook = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_banniere_facebook_chemin_creation']['tmp_name'], $file_url.'/'.$banniere_facebook)){
					@chmod("$file_url/$banniere_facebook", 0777);
					$img="$file_url/$banniere_facebook";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $banniere_facebook";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}

		if($_FILES['organisme_footer_facebook_chemin_creation']['name']!=""){
			mkdir("upload/footer_facebook/".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/footer_facebook/'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_footer_facebook_chemin_creation']['name']);

			if(isExtAuthorized($extension)){
				$footer_facebook = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_footer_facebook_chemin_creation']['tmp_name'], $file_url.'/'.$footer_facebook)){
					@chmod("$file_url/$footer_facebook", 0777);
					$img="$file_url/$footer_facebook";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $footer_facebook";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		} 

		if($_FILES['organisme_image_billet_creation']['name']!=""){
			mkdir("upload/billet/".$lastIdInsert);
			// Renseigne ici le chemin de destination de la photo
			$file_url = 'upload/billet/'.$lastIdInsert;
			// Définition des extensions de fichier autorisées (avec le ".")
			$extension = getExtension($_FILES['organisme_image_billet_creation']['name']);

			if(isExtAuthorized($extension)){
				$image_billet = 'image'.$extension;		
				// Upload fichier
				if (@move_uploaded_file($_FILES['organisme_image_billet_creation']['tmp_name'], $file_url.'/'.$image_billet)){
					@chmod("$file_url/$image_billet", 0777);
					$img="$file_url/$image_billet";
					$repertoire_destination="./".$file_url."/";
				}
				else{
					echo "Erreur, impossible d'envoyer le fichier $image_billet";
				}
			}else{
				echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
			}
		}
	} 
	header("Location:organismes.php?menu_actif=logins"); 
}

//Suppression d'un organisme
if( isset($_GET['delete_organisme']) ){
	// delete user 
	$sql="DELETE FROM sp_organismes WHERE organisme_id = '".$_GET['delete_organisme']."'";
	mysql_query($sql) or die(mysql_error());
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_banniere"){
	$sql ="UPDATE sp_organismes SET
				organisme_banniere_chemin = ''
			WHERE organisme_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
	/*$dossier="upload/banniere/".$_GET['id'];
	clearDir($dossier);*/
	header("Location:organismes.php?menu_actif=logins");
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_logo"){
	$sql ="UPDATE sp_organismes SET
				organisme_logo_chemin = ''
			WHERE organisme_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
	/*$dossier="upload/banniere/".$_GET['id'];
	clearDir($dossier);*/
	header("Location:organismes.php?menu_actif=logins");
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_banniere_facebook"){
	$sql ="UPDATE sp_organismes SET
				organisme_banniere_facebook_chemin = ''
			WHERE organisme_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
	/*$dossier="upload/banniere/".$_GET['id'];
	clearDir($dossier);*/
	header("Location:organismes.php?menu_actif=logins");
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_footer_facebook"){
	$sql ="UPDATE sp_organismes SET
				organisme_footer_facebook_chemin = ''
			WHERE organisme_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
	/*$dossier="upload/banniere/".$_GET['id'];
	clearDir($dossier);*/
	header("Location:organismes.php?menu_actif=logins");
}

if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer_image_billet"){
	$sql ="UPDATE sp_organismes SET
				organisme_image_billet = ''
			WHERE organisme_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());  
	/*$dossier="upload/banniere/".$_GET['id'];
	clearDir($dossier);*/
	header("Location:organismes.php?menu_actif=logins");
}

include_once('../vars/constantes_vars.php');
include_once('../vars/statics_vars.php');

include_once('../classe/classe_core_event.php');
include_once('../classe/fonctions.php');

$core = new core();

if($core->isAdmin && $core->userLevel<=1){
	$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
	$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
	$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Sciences Po | Événements : administration</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
	<link href="jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	
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
		<h3>Liste des organismes</h3>
		<p><a href="#" class="buttonenregistrer" id="creer_organisme">Nouvel organisme</a></p> 
	
		<div id="bloc_creation_organisme" class="bloc_modif">
			<form name="formOrganismeCreation" method="post" enctype="multipart/form-data" action="#" id="formOrganismeCreation">
				<div class="formulaire_large">
					<p><label for="organisme_nom_creation">Nom :</label><input name="organisme_nom_creation" type="text" class="inputField" id="organisme_nom_creation" value=""/></p>
					<p><label for="organisme_google_analytics_id_creation">Identifiant Google :</label><input name="organisme_google_analytics_id_creation" class="inputField" type="text" id="organisme_google_analytics_id_creation" value="" /></p>
					<p><label for="organisme_couleur_creation">Couleur (hexa : #000000) :</label><input name="organisme_couleur_creation" class="inputFieldTiny" type="text" id="organisme_couleur_creation" value="" /></p>
					
					<div class="clear"></div>

					<h4>Bannière</h4>
				    <p><label for="organisme_banniere_chemin_creation">Fichier image :</label><input type="file" name="organisme_banniere_chemin_creation" id="organisme_banniere_chemin_creation"/></p>
					<p><label for="organisme_banniere_lien_creation">Lien pour la bannière :</label><input name="organisme_banniere_lien_creation" class="inputField" type="text" id="organisme_banniere_lien_creation" value="" /></p>
					
					<div class="clear"></div>

					<h4>Logo</h4>
				    <p><label for="organisme_logo_chemin_creation">Fichier image :</label><input type="file" name="organisme_logo_chemin_creation" id="organisme_logo_chemin_creation"/></p>
					
					<div class="clear"></div>

					<h4>Facebook</h4>
				    <p><label for="organisme_banniere_facebook_chemin_creation">Banniere :</label><input type="file" name="organisme_banniere_facebook_chemin_creation" id="organisme_banniere_facebook_chemin_creation"/></p>
				    <p><label for="organisme_footer_facebook_chemin_creation">Footer :</label><input type="file" name="organisme_footer_facebook_chemin_creation" id="organisme_footer_facebook_chemin_creation"/></p>	

					<div class="clear"></div>

					<h4>Mentions légales (billet)</h4>
				    <p style="width:600px;"><label for="organisme_mentions_creation">Texte :</label><textarea name="organisme_mentions_creation" rows="10" cols="20" class="inputField tinymce" id="organisme_mentions_creation"></textarea></p>
				
					<div class="clear"></div>

					<h4>URL Front</h4>
				    <p><label for="organisme_url_front_creation">Chemin :</label><input name="organisme_url_front_creation" type="text" class="inputField" id="organisme_url_front_creation" value=""/></p>
				
					<div class="clear"></div>

					<h4>Billet</h4>
				    <p><label for="organisme_image_billet_creation">Image pour les billets :</label><input type="file" name="organisme_image_billet_creation" id="organisme_image_billet_creation"/></p>
					<p><label for="organisme_url_image_creation">Lien pour l'image des billets :</label><input name="organisme_url_image_creation" class="inputField" type="text" id="organisme_url_image_creation" value="" /></p>					
				</div>
				<div class="liens">
					
				</div>
				<div class="bas_modif">
					<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
					<input name="type_saisie_organisme" type="hidden" id="type_saisie_organisme" value="creation" />
				</div>
			</form>
		</div>
		
		<p>&nbsp;</p>
	 	<?php
			// list all les users
	 		$sql = "SELECT * FROM sp_organismes";
	 		$res = mysql_query($sql)or die(mysql_error());
			$iteration = 1;
			while($row = mysql_fetch_array($res)){
				if($iteration%2==1){
			?>
					<div class="listItemRubrique1">
			<?php
				}
				else{
			?>
					<div class="listItemRubrique2">
			<?php
				}
				$iteration++;
			
			?>
						<div class="infos_large"><p><?php echo " <span class=\"uppercase\">".$row['organisme_nom']."</span>";?></p></div>
						<div class="liens modif"><a href="#" id="lien_organisme_<?php echo $row['organisme_id'];?>" class="lien_organisme" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="organismes.php?delete_organisme=<?php echo $row['organisme_id'];?>&amp;menu_actif=logins" onclick="confirmar('organismes.php?delete_organisme=<?php echo $row['organisme_id'];?>&amp;menu_actif=logins', 'Etes-vous sûr de vouloir supprimer cet utilisateur? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a></div>
						<div id="bloc_modif_<?php echo $row['organisme_id'];?>" class="bloc_modif">

						</div>
					</div>
			<?php
			}
		?>		
	</div>	
</div>


<script type="text/javascript">
	$(window).load(function(){

		var sauv_id="";
		
		$("a.lien_organisme").click(function(e){
			e.preventDefault();
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			} 
			
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_"+tableau_id[2]; 
			$('.bloc_modif').removeClass('open');
			$('#bloc_modif_'+tableau_id[2]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
			
			if($('#bloc_modif_'+tableau_id[2]).css("display")!="block"){
				$.post("modifOrganismeAJAX.php", {  id:tableau_id[2], type:"modification" },
				function(data){
					//alert("Data Loaded: " + data);
					document.getElementById(identifiant).innerHTML=data; 
					$('#bloc_modif_'+tableau_id[2]).slideToggle();


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


				});
			} 
			else{
				$('#bloc_modif_'+tableau_id[2]).slideToggle(); 
			}
			
			
			
			sauv_id = identifiant;
		});
			
		$("a#creer_organisme").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation_organisme";
			document.getElementById('bloc_creation_organisme').style.display="block";
						
		});
		
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
		
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

<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

// feedcreator library
//include('feedcreator.class.php');

include('variables.php');
//include_once('../vars/constantes_vars.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

$core = new core(); 

$erreur="";
//Création d'un événement
if(isset($_POST['date_debut'])){
	$filename = 'export.csv';
	$csv_terminated = "\n";
	$csv_separator = ",";
	$csv_enclosed = '';
	$csv_escaped = "\\";  
	$tableauDateDebut = explode("/", $_POST['date_debut']);
	$tableauDateFin = explode("/", $_POST['date_fin']);
	
	$date_debut = $tableauDateDebut[2].'-'.$tableauDateDebut[1].'-'.$tableauDateDebut[0].' 00:00:01'; 
	$date_fin = $tableauDateFin[2].'-'.$tableauDateFin[1].'-'.$tableauDateFin[0].' 23:59:59';	
	$options="";
	
	if(!empty($_POST["options"])){
		$options.=" AND (";
		for($i=0;$i<count($_POST["options"])-1; $i++){ 
			$options.="spe.evenement_statut=".$_POST["options"][$i]." OR ";
		}
		for($i=count($_POST["options"])-1; $i<count($_POST["options"]); $i++){
		  	$options.="spe.evenement_statut=".$_POST["options"][$i].")";
		}
	}

	if($_POST['organisme']!=-1){
		$options.=" AND spo.organisme_id=".$_POST['organisme'];
	}
	
	$sql_query = "SELECT * FROM ".TB."evenements as spe, ".TB."sessions as sps, ".TB."rubriques as spr, ".TB."organismes as spo, ".TB."groupes as spg WHERE spe.evenement_id=sps.evenement_id AND spe.evenement_groupe_id=spg.groupe_id AND spg.groupe_organisme_id=spo.organisme_id AND spe.evenement_rubrique=spr.rubrique_id AND evenement_datetime >= '".$date_debut."' AND evenement_datetime <= '".$date_fin."'".$options;

	$result = mysql_query($sql_query);

	$schema_insert = '';
	$schema_insert .= $csv_enclosed . "Statut :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Titre-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Titre-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator; 
	$schema_insert .= $csv_enclosed . "Description-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Description-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Texte-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Texte-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Organisateur-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator; 
	$schema_insert .= $csv_enclosed . "Organisateur-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator; 
	$schema_insert .= $csv_enclosed . "Co-organisateur-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Co-organisateur-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Rubrique-FR :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Rubrique-EN :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Date de début (événement):") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Date de fin (événément) :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Nom-Session-FR :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Nom-Session-EN :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Date de début (session):") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Date de fin (session) :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Langue :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Lieu :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Code bâtiment :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Lien-FR :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Lien-EN :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Texte-Lien-FR :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Texte-Lien-EN :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Type inscription :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Complément inscription :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Inscription Amphi Ouverte :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
   	$schema_insert .= $csv_enclosed . utf8_decode("Places Internes Amphi totales :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Internes Amphi prises :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Externes Amphi totales :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Externes Amphi prises :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Inscription Visio Ouverte :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
   	$schema_insert .= $csv_enclosed . utf8_decode("Places Internes Visio totales :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Internes Visio prises :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Externes Visio totales :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Places Externes Visio prises :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Adresse :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Autre adresse :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Code externe :") . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . utf8_decode("Traduction disponible :") . $csv_enclosed;
	$schema_insert .= $csv_separator;

	$out = trim(substr($schema_insert, 0, -1));
	$out .= $csv_terminated;

	// Format the data
	while ($row = mysql_fetch_array($result)) {
		$laDateDebut=date("d/m/Y - H:i",$row['evenement_date']);
		if($row['evenement_statut']==1){
			$statut = "brouillon";
		}
		else{
			if($row['evenement_statut']==2){
				$statut = "caché";
			}
			else{
            	if($row['evenement_statut']==3){
					$statut = "publié";
				}
				else{
                	if($row['evenement_statut']==4){
						$statut = "soumission"; 
					}
				}
			}
		} 
		
		$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
		$ressessions = mysql_query($sqlsessions) or die(mysql_error());
		
		$finEvenement = '1970-01-01 00:00:00';
		while($rowsession = mysql_fetch_array($ressessions)){
			if($rowsession['session_fin_datetime']>$finEvenement){
				$finEvenement = $rowsession['session_fin_datetime'];
			}
		}
		
		$tableauFin = explode(" ",$finEvenement);

		if($tableauFin[1]=="23:59:00"){
			$laDateFin = $tableauFin[0];
		}   
		else{
			$laDateFin = $finEvenement;
		}

		foreach($langues_evenement as $cle => $valeur){
			if($row['session_langue']==$valeur){
				$langue=$cle;
			}
		}

		if($row['session_type_inscription']==1){
			$type = "Entrée libre";
		}
		else{
			if($row['session_type_inscription']==2){
				$type = "Inscription obligatoire par la plateforme";
			}
			else{
            	if($row['session_type_inscription']==3){
					$type = "Inscription obligatoire par mail ou autre";
				}
			}
		}

		if($row['session_statut_inscription']==1){
			$statut_inscription = "oui";
		}
		else{
			$statut_inscription = "non";
		}

		if($row['session_statut_visio']==1){
			$statut_visio = "oui";
		}
		else{
			$statut_visio = "non";
		}

		if($row['session_traduction']==1){
			$traduction = "oui";
		}
		else{
			$traduction = "non";
		}
		
		$schema_insert = ''; 
		
		$schema_insert .= $csv_enclosed . '"'.utf8_decode($statut).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_titre']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_titre_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_resume']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_resume_en']))).'"' . $csv_enclosed . $csv_separator; 
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_texte']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_texte_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_organisateur']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_organisateur_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_coorganisateur']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['evenement_coorganisateur_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['rubrique_titre']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['rubrique_titre_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.$row['evenement_datetime'].'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.$laDateFin.'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_nom']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_nom_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_debut_datetime']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_fin_datetime']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($langue))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_lieu']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_decode($row['session_code_batiment']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_lien']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_lien_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_texte_lien']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_texte_lien_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.utf8_decode($type).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_complement_type_inscription']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.utf8_decode($statut_inscription).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_internes_totales']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_internes_prises']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_externes_totales']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_externes_prises']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.utf8_decode($statut_visio).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_internes_totales_visio']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_internes_prises_visio']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_externes_totales_visio']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_places_externes_prises_visio']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_adresse1']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_adresse2']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['session_code_externe']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.utf8_decode($traduction).'"' . $csv_enclosed . $csv_separator;

		$schema_insert .= $csv_separator;

		$out .= $schema_insert;
		$out .= $csv_terminated;
	} // end while

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Length: " . strlen($out));
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=$filename");
	echo $out;
	exit;
}
 
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
		
		<form id="formcreer" name="formcreer" method="post" action="<?php echo $_SERVER['PHP_SELF']."?menu_actif=export"?>">
			<input type="submit" name="button" value="Exporter" class="buttonenregistrer"/>

			<fieldset>
				<p class="legend">Choisir l'intervalle de dates (les bornes seront inclues dans l'export)</p>

				<p>
					<label for="date_debut" class="inline">Date de début : </label>
					<input name="date_debut" type="text" class="inputFieldShort datepicker" id="date_debut" value=""/>
					<input name="date_fin" type="text" class="inputFieldShort datepicker inputdroit" id="date_fin" value=""/>
					<label for="date_fin" class="inline labeldroit">Date de fin : </label>
					
				</p>

				<p>
					<input type="checkbox" name="options[]" value="1" checked="checked" id="option_1"><label for="option_1">&nbsp;Brouillons</label>
					<input type="checkbox" name="options[]" value="2" checked="checked" id="option_2"><label for="option_2">&nbsp;Cachés</label> 
					<input type="checkbox" name="options[]" value="3" checked="checked" id="option_3"><label for="option_3">&nbsp;Publiés</label>
					<input type="checkbox" name="options[]" value="4" checked="checked" id="option_4"><label for="option_4">&nbsp;Soumis</label>
				</p> 
				<p>
				<label for="organisme">Organisme : </label>
					<select name="organisme" id="organisme" style="width:250px;">
						<option value="-1" selected="selected">Tous</option>
					<?php
						$sqlorganismes ="SELECT * FROM sp_organismes ORDER BY organisme_nom ASC";
						$resorganismes = mysql_query($sqlorganismes) or die(mysql_error());
						while($roworganisme = mysql_fetch_array($resorganismes)){
							echo '<option value="'.$roworganisme['organisme_id'].'">'.$roworganisme['organisme_nom'].'</option>';
						}
					?>	
				</select>
				</p>
			</fieldset>
			<input type="submit" name="button" value="Exporter" class="buttonenregistrer" />
		</form>
	</div>
</div>

<script type="text/javascript">
	$(window).load(function(){
		var actif = getParamValue('menu_actif');
		//document.getElementById(actif).className = "actif";
		$('#'+actif).addClass('actif');
		

		$.datepicker.setDefaults($.datepicker.regional['fr']);
		var dateDuJour = new Date();
	
		$('.datepicker').datepicker({
			onSelect:function(dateText, inst){
				if($('#date_debut').val()!=""){
					var tableauDateDebut=$('#date_debut').val().split("/");
					var dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
					$('#date_fin').datepicker( "option", "minDate", dateBorneBasse );
				}
				else{
					$('#date_fin').datepicker();
				}

				if($('#date_fin').val()!=""){
					var tableauDateFin=$('#date_fin').val().split("/");
					var dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
					$('#date_debut').datepicker( "option", "maxDate", dateBorneHaute );
				}
				else{
					$('#date_debut').datepicker();
				}
			}
		});
	});
</script>
</body>
</html>

<?php
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

// feedcreator library
include('feedcreator.class.php');

include('variables.php');

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
	
	$date_debut = mktime(0,0,0,$tableauDateDebut[1],$tableauDateDebut[0],$tableauDateDebut[2]); 
	$date_fin = mktime(0,0,0,$tableauDateFin[1],$tableauDateFin[0],$tableauDateFin[2]);
	
	$options="";
	
	if(!empty($_POST["options"])){
		$options.=" AND (";
		for($i=0;$i<count($_POST["options"])-1; $i++){ 
			$options.="sp_evenements.evenement_statut=".$_POST["options"][$i]." OR ";
		}
		for($i=count($_POST["options"])-1; $i<count($_POST["options"]); $i++){
		  	$options.="sp_evenements.evenement_statut=".$_POST["options"][$i].")";
		}
	}

	/*if($_POST['brouillon']===1){
		$options.=" AND sp_evenements_statut=1";
	} 
	if($_POST['cache']===1){
		$options.=" AND sp_evenements_statut=2";
	}
	if($_POST['publie']===1){
		$options.=" AND sp_evenements_statut=3";
	}
	if($_POST['soumission']===1){
		$options.=" AND sp_evenements_statut=4";
	}*/
	
	$sql_query = "SELECT * FROM sp_evenements, sp_rubriques WHERE sp_evenements.evenement_rubrique=sp_rubriques.rubrique_id AND evenement_date >= ".$date_debut." AND evenement_date <= ".$date_fin.$options;
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
	$schema_insert .= $csv_enclosed . "Date de début :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Date de fin :" . $csv_enclosed;
	$schema_insert .= $csv_separator;
	/*$schema_insert .= $csv_enclosed . "Adresse du lien FR : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Adresse du lien EN : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Texte du lien FR : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Texte du lien EN : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Langue : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Lieu Salle Amphi : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Code bâtiment : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Nom du lieu : " . $csv_enclosed;
	$schema_insert .= $csv_separator;
	$schema_insert .= $csv_enclosed . "Adresse : " . $csv_enclosed;
	$schema_insert .= $csv_separator;*/
   
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
		$finEvenement=0;
		while($rowsession = mysql_fetch_array($ressessions)){
			if($rowsession['session_fin']>$finEvenement){
				$finEvenement = $rowsession['session_fin'];
			}
		}
		
		if(date("H:i",$finEvenement)=="23:59"){
			$laDateFin = date("d/m/Y",$finEvenement);
		}   
		else{
			$laDateFin = date("d/m/Y - H:i",$finEvenement);
		}
		
		$schema_insert = ''; 
		
		$schema_insert .= $csv_enclosed . '"'.$statut.'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_titre'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_titre_en'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_resume'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_resume_en'])).'"' . $csv_enclosed . $csv_separator; 
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_texte'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_texte_en'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_organisateur'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_organisateur_en'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_coorganisateur'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags($row['evenement_coorganisateur_en'])).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['rubrique_titre']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.str_replace('"','""',strip_tags(utf8_encode($row['rubrique_titre_en']))).'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.$laDateDebut.'"' . $csv_enclosed . $csv_separator;
		$schema_insert .= $csv_enclosed . '"'.$laDateFin.'"' . $csv_enclosed . $csv_separator;
	   
		$schema_insert .= $csv_separator;

		$out .= $schema_insert;
		$out .= $csv_terminated;
	} // end while

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Length: " . strlen($out));
	// Output to browser with appropriate mime type, you choose ;)
	header("Content-type: text/x-csv");
	//header("Content-type: text/csv");
	//header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=$filename");
	echo $out;
	exit;
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
					<input type="checkbox" name="options[]" value=1 checked="checked">&nbsp;Brouillons 
					<input type="checkbox" name="options[]" value=2 checked="checked">&nbsp;Cachés 
					<input type="checkbox" name="options[]" value=3 checked="checked">&nbsp;Publiés
					<input type="checkbox" name="options[]" value=4 checked="checked">&nbsp;Soumis
				</p> 
			</fieldset>
			<input type="submit" name="button" value="Exporter" class="buttonenregistrer" />
		</form>
	</div>
</div>

<script type="text/javascript">
	$(window).load(function(){
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";

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

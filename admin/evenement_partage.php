<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

include('variables.php');
//include_once('../vars/constantes_vars.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

$core = new core();
//session_start();

// if editing...
if( isset($_POST['evenement_id']) ){
	/*$sqlcountPartage = mysql_query("SELECT COUNT(*) AS nb FROM sp_rel_evenement_rubrique WHERE evenement_id='".$_POST['evenement_id']."' AND rubrique_id='".$_POST['evenement_rubrique']."'");
	$rescountPartage = mysql_fetch_array($sqlcountPartage); 
	if($rescountPartage['nb']>0){
		
	}*/
	
	$sql="DELETE FROM sp_rel_evenement_rubrique WHERE groupe_id='".$_SESSION['id_actual_group']."' AND evenement_id='".$_POST['evenement_id']."'";
	mysql_query($sql) or die(mysql_error());
	 
	if($_POST['evenement_publier']==1){                   
		$sqlinsert ="INSERT INTO sp_rel_evenement_rubrique VALUES ('', '".$_POST['evenement_id']."', '".$_POST['evenement_rubrique']."', '".$_SESSION['id_actual_group']."')";
		mysql_query($sqlinsert) or die(mysql_error());
	}
	header( "Location:list.php?menu_actif=evenements");
}

$sql ="SELECT * FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
$res = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($res);


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
		<p class="intro_modif">Modification de:</p>
	
		<?php
			$mois = retournerMoisToutesLettres(date("n", $row['evenement_date']));
			echo "<h3>".date("d", $row['evenement_date'])." ".$mois." ".date("Y", $row['evenement_date'])." : <em>".$row['evenement_titre']."</em></h3>";

		?>

      	<form id="formedition" name="formedition" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']?>">
			<?php
				$sql3 ="SELECT * FROM sp_users WHERE user_id = '".$row['evenement_user_id']."'";
				$res3 = mysql_query($sql3) or die(mysql_error());
				$row3 = mysql_fetch_array($res3);
			?>
			<p class="createur">Créé par : <?php echo $row3['user_nom']." / ".$row3['user_login']?></p>
			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer"/>

			<fieldset>
				<?php
					$sql4 ="SELECT * FROM  sp_rel_evenement_rubrique  WHERE groupe_id = '".$_SESSION['id_actual_group']."' AND evenement_id='".$_GET['id']."'";
					$res4 = mysql_query($sql4) or die(mysql_error());
					$row4 = mysql_fetch_array($res4);
					
					$sqlcountPartage2 = mysql_query("SELECT COUNT(*) AS nb FROM  sp_rel_evenement_rubrique  WHERE groupe_id = '".$_SESSION['id_actual_group']."' AND evenement_id='".$_GET['id']."'");
					$rescountPartage2 = mysql_fetch_array($sqlcountPartage2);
				?>
				
				<p class="legend">informations sur l'événement</p>

				<p>
					<label for="evenement_publier" class="inline">Publier sur mon Front Office :</label>
					<select name="evenement_publier" id="evenement_publier">
						<option value="1"<?php if($rescountPartage2['nb']==1){echo "selected=\"selected\"";} ?>>Oui</option>
						<option value="0"<?php if($rescountPartage2['nb']==0){echo "selected=\"selected\"";} ?>>Non</option>
					</select>
				</p>
				
				<p>
					<label for="evenement_rubrique" class="inline">Rubrique* :</label>
					<select name="evenement_rubrique" id="evenement_rubrique">
					<?php 
						$sqlorganisme ="SELECT * FROM sp_organismes, sp_groupes WHERE groupe_organisme_id=organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
						$resorganisme = mysql_query($sqlorganisme) or die(mysql_error());
						$roworganisme = mysql_fetch_array($resorganisme);
					
						$sqlrubriques ="SELECT * FROM sp_rubriques, sp_groupes WHERE groupe_organisme_id='".$roworganisme['organisme_id']."' AND sp_rubriques.rubrique_groupe_id=sp_groupes.groupe_id ORDER BY rubrique_titre ASC";
						$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
						while($rowrubrique = mysql_fetch_array($resrubriques)){
							if( $row4['rubrique_id'] == $rowrubrique['rubrique_id'] ) {
								$selected = ' selected="selected"';
							} else {
								$selected = '';
							}

							echo '<option value="'.$rowrubrique['rubrique_id'].'"'.$selected.'>'.utf8_encode($rowrubrique['rubrique_titre']).'</option>';
						}
					?>
					</select>
				</p>
				
			</fieldset>

			<input type="submit" name="button" value="Enregistrer" class="buttonenregistrer" />
			<input name="evenement_id" type="hidden" id="evenement_id" value="<?php echo $row['evenement_id'];?>" />
		</form>			
	</div>
</div>
</body>
</html>

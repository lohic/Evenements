<?php 
include_once('../vars/config.php');
session_start(); 
// sécurité
include('cookie.php');
// connexion base de données
include('connect.php');
// fichiers nécessaires
include('functions.php');
include_once('../vars/constantes_vars.php');
include_once('../classe/classe_core_event.php');
include_once('../classe/fonctions.php');

$core = new core();

if(isset($_GET['mois'])){
	$debutmois = mktime(0,0,0,$_GET['mois'],1,$_GET['year']);
	$finmois = retourneNumeroFinMois($_GET['mois'], $_GET['year']);
	$complement="";
	if($_GET['rubrique']!=-1){
		$complement = "AND evenement_rubrique='".$_GET['rubrique']."'";
	}
	
}else{ 
	$debutmois = mktime(0,0,0,date("n"),1,date("Y"));
	$finmois = retourneNumeroFinMois(date("n"), date("Y"));
	$complement="";
}

//Récupère la liste des événements partagés
$tableauDesEventsPartages=array();
$tableauDesEventsPartagesTotal=array();


$sqlPartage = sprintf("SELECT spe.evenement_id, evenement_date, evenement_groupe_id, evenement_titre, evenement_image, evenement_statut FROM sp_evenements as spe, sp_rel_rubrique_groupe as spr WHERE spr.rubrique_id=spe.evenement_rubrique  ".$complement." AND spr.groupe_id=%s AND evenement_statut!=4 AND evenement_date >=%s AND evenement_date<=%s GROUP BY spe.evenement_id", 
				GetSQLValueString($_SESSION['id_actual_group'], "int"),
				GetSQLValueString($debutmois, "int"),
				GetSQLValueString($finmois, "int"));

$resPartage = mysql_query($sqlPartage)or die(mysql_error());
while($rowPartage = mysql_fetch_array($resPartage)){
	$tableauDesEventsPartages[] = $rowPartage['evenement_id']; 
}
  

$sqlPartage2 = sprintf("SELECT spe.evenement_id, evenement_date, evenement_groupe_id, evenement_titre, evenement_image, evenement_statut FROM sp_evenements as spe, sp_rel_evenement_groupe as spg WHERE spg.evenement_id=spe.evenement_id  ".$complement." AND spg.groupe_id=%s AND evenement_statut!=4 AND evenement_date >=%s AND evenement_date<=%s GROUP BY spe.evenement_id", 
						GetSQLValueString($_SESSION['id_actual_group'], "int"),
						GetSQLValueString($debutmois, "int"),
						GetSQLValueString($finmois, "int"));

$resPartage2 = mysql_query($sqlPartage2)or die(mysql_error());
while($rowPartage2 = mysql_fetch_array($resPartage2)){ 
	$tableauDesEventsPartages[] = $rowPartage2['evenement_id']; 
} 


//pour la gestion des rubriques dispos pour le tri
$debutmoiscourant = mktime(0,0,0,date("n"),1,date("Y"));
$sqlPartage3 = sprintf("SELECT spe.evenement_id, evenement_date, evenement_groupe_id, evenement_titre, evenement_image, evenement_statut FROM sp_evenements as spe, sp_rel_rubrique_groupe as spr WHERE spr.rubrique_id=spe.evenement_rubrique  AND spr.groupe_id=%s AND evenement_statut!=4 AND evenement_date >=%s GROUP BY spe.evenement_id", 
				GetSQLValueString($_SESSION['id_actual_group'], "int"),
				GetSQLValueString($debutmoiscourant, "int"));

$resPartage3 = mysql_query($sqlPartage3)or die(mysql_error());
while($rowPartage3 = mysql_fetch_array($resPartage3)){
	$tableauDesEventsPartagesTotal[] = $rowPartage3['evenement_id']; 
}

$sqlPartage4 = sprintf("SELECT spe.evenement_id, evenement_date, evenement_groupe_id, evenement_titre, evenement_image, evenement_statut FROM sp_evenements as spe, sp_rel_evenement_groupe as spg WHERE spg.evenement_id=spe.evenement_id  AND spg.groupe_id=%s AND evenement_statut!=4 AND evenement_date >=%s GROUP BY spe.evenement_id", 
						GetSQLValueString($_SESSION['id_actual_group'], "int"),
						GetSQLValueString($debutmoiscourant, "int"));

$resPartage4 = mysql_query($sqlPartage4)or die(mysql_error());
while($rowPartage4 = mysql_fetch_array($resPartage4)){ 
	$tableauDesEventsPartagesTotal[] = $rowPartage4['evenement_id']; 
}

/*$sqlPartage = "SELECT spe.evenement_id, evenement_date, evenement_groupe_id, evenement_titre, evenement_image, evenement_statut FROM sp_evenements as spe, sp_rel_rubrique_groupe as spr, sp_rel_evenement_groupe as spg WHERE ((spr.rubrique_id=spe.evenement_rubrique AND spr.groupe_id='".$_SESSION['id_actual_group']."') OR (spg.evenement_id=spe.evenement_id AND spg.groupe_id='".$_SESSION['id_actual_group']."')) AND evenement_statut!=4 AND evenement_date >='".$debutmois."' GROUP BY spe.evenement_id ORDER BY evenement_date DESC"; 
$resPartage = mysql_query($sqlPartage)or die(mysql_error());

$sqlcountPartage = mysql_query("SELECT COUNT(*) AS nb FROM sp_evenements as spe, sp_rel_rubrique_groupe as spr, sp_rel_evenement_groupe as spg WHERE ((spr.rubrique_id=spe.evenement_rubrique AND spr.groupe_id='".$_SESSION['id_actual_group']."') OR (spg.evenement_id=spe.evenement_id AND spg.groupe_id='".$_SESSION['id_actual_group']."')) AND evenement_statut!=4  AND evenement_date >='".$debutmois."' GROUP BY spe.evenement_id"); 
$rescountPartage = mysql_fetch_array($sqlcountPartage);*/

// efface un événement et ses sessions ainsi que le dossier images lié...
if( isset($_GET['fonction']) && $_GET['fonction']=="supprimer"){
	// delete album 
	$sql="DELETE FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());
	
	$sql="DELETE FROM sp_sessions WHERE evenement_id = '".$_GET['id']."'";
	mysql_query($sql) or die(mysql_error());
	
	$dossier="upload/photos/evenement_".$_GET['id'];
	clearDir($dossier);
	header("Location:list.php?menu_actif=evenements");
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

<script type="text/javascript" src="tools.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
</head>

<body>
<div id="page">
    <?php include("top.php"); ?>
    <div id="menu">
      <?php include("menu.php");?>
    </div>
    <div id="content">
		<div class="liste_mois">
			<?php 
				if(isset($_GET['mois'])){
					$testMois = $_GET['mois'];
					$testAnnee = $_GET['year'];
					$testRubrique = $_GET['rubrique'];
				}
				else{
					$testMois = date('n');
					$testAnnee = date('Y');
					$testRubrique = "toutes";
				}
			?>
			<p>
				<select name="annee" id="annee">
					<?php
						for($i=0; $i<12; $i++){
							$annee = date('Y') + $i - 2;
							$selected = "";
							if($testAnnee==$annee){
								$selected= "selected=\"selected\"";
							}
							echo '<option value="'.$annee.'" '.$selected.'>'.$annee.'</option>';
						}
					?>
				</select>
				<select name="mois" id="mois">
					<option value="1" <?php if($testMois==1){echo "selected=\"selected\"";} ?>>janvier</option>
					<option value="2" <?php if($testMois==2){echo "selected=\"selected\"";} ?>>février</option>
					<option value="3" <?php if($testMois==3){echo "selected=\"selected\"";} ?>>mars</option>
					<option value="4" <?php if($testMois==4){echo "selected=\"selected\"";} ?>>avril</option>
					<option value="5" <?php if($testMois==5){echo "selected=\"selected\"";} ?>>mai</option>
					<option value="6" <?php if($testMois==6){echo "selected=\"selected\"";} ?>>juin</option>
					<option value="7" <?php if($testMois==7){echo "selected=\"selected\"";} ?>>juillet</option>
					<option value="8" <?php if($testMois==8){echo "selected=\"selected\"";} ?>>août</option>
					<option value="9" <?php if($testMois==9){echo "selected=\"selected\"";} ?>>septembre</option>
					<option value="10" <?php if($testMois==10){echo "selected=\"selected\"";} ?>>octobre</option>
					<option value="11" <?php if($testMois==11){echo "selected=\"selected\"";} ?>>novembre</option>
					<option value="12" <?php if($testMois==12){echo "selected=\"selected\"";} ?>>décembre</option>
				</select> 
				
				<select name="rubrique" id="rubrique">
					<option value="-1" <?php if($testRubrique=="toutes"){echo "selected=\"selected\"";} ?>>Toutes</option>
					<?php 
					$sqlrubriques ="SELECT * FROM sp_rubriques, sp_evenements WHERE evenement_id IN (".implode(',',$tableauDesEventsPartagesTotal).") AND rubrique_id=evenement_rubrique GROUP BY rubrique_titre ORDER BY rubrique_titre ASC";
					$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
					
					while($rowrubrique = mysql_fetch_array($resrubriques)){
						if( $testRubrique == $rowrubrique['rubrique_id'] ) {
							$selected = ' selected="selected"';
						} else {
							$selected = '';
						}
						echo '<option value="'.$rowrubrique['rubrique_id'].'"'.$selected.'>'.utf8_encode($rowrubrique['rubrique_titre']).'</option>';
					}
					?>
				</select>   
			</p>
		</div>
		<?php
			if(count($tableauDesEventsPartages)>0){
		?>
				<h3 style="margin-bottom:0px;">Evénements partagés</h3> 
				<?php
				if(isset($_GET['mois'])){
				    $mois = retournerMoisToutesLettres($_GET['mois']);
					echo "<h3>".$mois." ".$_GET['year']."</h3>";
				}
				else{
					$mois = retournerMoisToutesLettres(date("n"));
					echo "<h3>".$mois." ".date("Y")."</h3>";
				} 
				$sql = "SELECT * FROM sp_evenements WHERE evenement_id IN (".implode(',',$tableauDesEventsPartages).") GROUP BY evenement_id ORDER BY evenement_date DESC";
				$res = mysql_query($sql)or die(mysql_error());
				while($row = mysql_fetch_array($res)){
					$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
					$ressessions = mysql_query($sqlsessions) or die(mysql_error());
					$finEvenement=0;
					while($rowsession = mysql_fetch_array($ressessions)){
						if($rowsession['session_fin']>$finEvenement){
							$finEvenement = $rowsession['session_fin'];
						}
					}
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
					$jourDebut = date("d", $row['evenement_date']);
					$jourFin = date("d", $finEvenement); 
					$jour = getDates($jourDebut, $jourFin);
					$horaires = getHoraires($jourDebut, $jourFin, $row['evenement_date'], $finEvenement);
				?>
						<div class="infos">
							<?php 
						
							$sqlcountPartage = mysql_query("SELECT COUNT(*) AS nb FROM sp_rel_evenement_rubrique WHERE groupe_id='".$row['groupe_id']."' AND evenement_id='".$row['evenement_id']."'");
							$rescountPartage = mysql_fetch_array($sqlcountPartage);
					
							if($rescountPartage['nb']!=0){
							?>	
								<p class="jour" style="color:#22c783"><?php echo $jour; ?></p>
							<?php   
							}
							else{
							?>
								<p class="jour"><?php echo $jour; ?></p>
							<?php
							} 
							?>
					
							<?php
								if($row['evenement_image']!=""){
									$extension = getExtension($row['evenement_image']);
									$nom_image = "mini-image".$extension;
									$chemin = "upload/photos/evenement_".$row['evenement_id']."/".$nom_image;
							?>
									<div class="image">
										<img src="<?php echo $chemin;?>?cache=<?php echo time(); ?>" alt="<?php echo $row['evenement_titre'];?>" width="55" height="35"/>
									</div>
							<?php
								}
								else{
									$chemin = "img/pasdimage.gif";
							?>		
									<div class="image">
										<img src="<?php echo $chemin;?>" alt="<?php echo $row['evenement_titre'];?>" width="55" height="35"/>
									</div>
							<?php
								}
							?>
					
							<div class="titre_heure">
								<?php
									$statut = retourneStatutToutesLettres($row['evenement_statut']);
								?>	
									<p class="titre"><a href="evenement_partage.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><?php echo $row['evenement_titre'];?></a><span class=""><?php echo " (".$statut.")";?><span></p>
							
								<p><?php echo $horaires;?></p>
						
								<?php
									$sqlNomGroupe = mysql_query("SELECT groupe_libelle FROM sp_groupes WHERE groupe_id='".$row['evenement_groupe_id']."'");
									$resNomGroupe = mysql_fetch_array($sqlNomGroupe);
								?>
						
								<p>Créé par <?php echo $resNomGroupe['groupe_libelle'];?></p>
							</div>
					 	</div>
				
						<div class="liens">
					       <a href="evenement_partage.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><img src="img/pencil.png" alt="modifier"/></a>&nbsp;<a href="http://www.sciencespo.fr/evenements/#/?lang=fr&amp;id=<?php echo $row['evenement_id'];?>"><img src="img/eye.png" alt="voir"/></a><br/>

						</div>
						<div class="places">
		   
						</div>
						<div class="poubelle">
			 
						</div>
					</div>
				<?php
				}
			}
	    ?>
	</div> 
</div>

<script type="text/javascript">
	$(window).load(function(){ 
		$("#mois").change(function(){
			var mois=$("#mois").val();
			var annee=$("#annee").val();
			var rubrique=$("#rubrique").val();
			location.href = "evenements_partages.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenementspartage";
		});	
		
		$("#annee").change(function(){
			var mois=$("#mois").val();
			var annee=$("#annee").val();
			var rubrique=$("#rubrique").val();
			location.href = "evenements_partages.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenementspartage";
		});
		
		$("#rubrique").change(function(){
			var mois=$("#mois").val();
			var annee=$("#annee").val();
			var rubrique=$("#rubrique").val();
			location.href = "evenements_partages.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenementspartage";
		});
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
	});
</script>
</body>
</html>

<?php 
//session_start(); 
include_once('../vars/config.php');
// security
//include('cookie.php');

// connection to data base
//include('connect.php');

// functions library
include('functions.php');

//include_once('../vars/constantes_vars.php');
//include_once('../vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

$core = new core();

$eventsPerPage = 10;
// Pages...
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
} else {
	$page = 1;
}

$firstEvent = ($page - 1) * $eventsPerPage;



//récupère la liste des evenements
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

$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
/*if($core->userLevel==1){
	$sql = requeteListeTousEvents($debutmois, $finmois, $complement); 
	$sqlcount = requeteCompteListeTousEvents($debutmois, $finmois, $complement); 
}
else{*/
	if($core->userLevel<=3){  
		
		$idGroups= array();
		/*foreach($core->user_info->groups as $cle => $valeur) 
		{
			$idGroups[]=$cle;
		}*/
		$sqlGetGroupes ="SELECT groupe_id FROM sp_groupes WHERE groupe_organisme_id='".$rowGetOrganisme['organisme_id']."'";
		$resGetGroupes= mysql_query($sqlGetGroupes) or die(mysql_error());
		while($rowGroupe= mysql_fetch_array($resGetGroupes)){
			$idGroups[]=$rowGroupe['groupe_id'];
		}
		
		$idGroups = implode(',',$idGroups);
		$sql = "SELECT * FROM sp_evenements WHERE evenement_statut!=4 AND evenement_groupe_id IN ($idGroups) ".$complement." AND evenement_date >='".$debutmois."' AND evenement_date <='".$finmois."'  ORDER BY evenement_date DESC"; 
		$sqlcount = mysql_query("SELECT COUNT(*) AS nb FROM sp_evenements WHERE evenement_statut!=4 ".$complement." AND evenement_groupe_id IN ($idGroups) AND evenement_date >='".$debutmois."' AND evenement_date <='".$finmois."'");
	}
	else{
		if($core->userLevel<=7){
			$sql = "SELECT * FROM sp_evenements WHERE evenement_statut!=4 AND evenement_user_id='".$_SESSION['id']."' ".$complement." AND evenement_date >='".$debutmois."' AND evenement_date <='".$finmois."'  ORDER BY evenement_date DESC"; 
			$sqlcount = mysql_query("SELECT COUNT(*) AS nb FROM sp_evenements WHERE evenement_statut!=4 ".$complement." AND evenement_user_id='".$_SESSION['id']."' AND evenement_date >='".$debutmois."' AND evenement_date <='".$finmois."'");
		}
	}
//}

$res = mysql_query($sql)or die(mysql_error());
$rescount = mysql_fetch_array($sqlcount);
$totalEvents = $rescount['nb'];
$totalPages = ceil($totalEvents / $eventsPerPage);

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
							$annee = date('Y');
							$annee = $annee + $i - 2;
							$selected = "";
							if($testAnnee==$annee)
							{
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
					if($core->isSuperAdmin){ 
						$sqlrubriques ="SELECT * FROM sp_rubriques ORDER BY rubrique_titre ASC";
						$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());      
					}
					else{
						$sqlorganisme ="SELECT * FROM sp_organismes, sp_groupes WHERE groupe_organisme_id=organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
						$resorganisme = mysql_query($sqlorganisme) or die(mysql_error());
						$roworganisme = mysql_fetch_array($resorganisme);
					
						$sqlrubriques ="SELECT * FROM sp_rubriques, sp_groupes WHERE groupe_organisme_id='".$roworganisme['organisme_id']."' AND sp_rubriques.rubrique_groupe_id=sp_groupes.groupe_id ORDER BY rubrique_titre ASC";
						$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
					}  
					
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
			if(isset($_GET['mois'])){
			    $mois = retournerMoisToutesLettres($_GET['mois']);
				echo "<h3>".$mois." ".$_GET['year']."</h3>";
			}
			else{
				$mois = retournerMoisToutesLettres(date("n"));
				echo "<h3>".$mois." ".date("Y")."</h3>";
			}

			$iteration = 1;
						
			while($row = mysql_fetch_array($res)){
				$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
				$ressessions = mysql_query($sqlsessions) or die(mysql_error());
				$finEvenement=0;
				while($rowsession = mysql_fetch_array($ressessions)){
					if($rowsession['session_fin']>$finEvenement){
						$finEvenement = $rowsession['session_fin'];
					}
				}
				
				$sqlcountsessions = mysql_query("SELECT COUNT(*) AS nb FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'");
				$rescountsessions = mysql_fetch_array($sqlcountsessions);
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
					<p class="jour"><?php echo $jour; ?></p>
					<?php
						if($row['evenement_image']!=""){
							$extension = getExtension($row['evenement_image']);
							$nom_image = "mini-image".$extension;
							$chemin = "upload/photos/evenement_".$row['evenement_id']."/".$nom_image;
					?>
							<div class="image">
								<a href="crop.php?id=<?php echo $row['evenement_id']; ?>"><img src="<?php echo $chemin;?>?cache=<?php echo time(); ?>" alt="<?php echo $row['evenement_titre'];?>" width="55" height="35"/></a>
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
							if($rescountsessions['nb']==1){
						?>
						<!-- MODIF LOIC -->
								<p class="titre"><a href="edit_evenement_unique.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><?php echo $row['evenement_titre'];?></a><span class=""><?php echo " (".$statut.")";?></span></p>
						<?php
							}
							else{
						?>
								<p class="titre"><a href="edit_evenement.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><?php echo $row['evenement_titre'];?></a><span class=""><?php echo " (".$statut.")";?></span></p>
						<?php
							}
						?>
						<p><?php echo $horaires;?></p>
					</div>
			 	</div>
				
				<div class="liens">
			
			<?php
				$sqlOrganisme ="SELECT organisme_url_front FROM sp_evenements, sp_groupes, sp_organismes WHERE evenement_id='".$row['evenement_id']."' AND evenement_groupe_id=groupe_id AND groupe_organisme_id=organisme_id";
				$resOrganisme = mysql_query($sqlOrganisme) or die(mysql_error());
			   	$rowOrganisme = mysql_fetch_array($resOrganisme);
			
				
				if($rescountsessions['nb']==1){
			?>
					<a href="edit_evenement_unique.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><img src="img/pencil.png" alt="modifier"/></a>&nbsp;<a href="<?php echo $rowOrganisme['organisme_url_front'];?>#/?lang=fr&amp;id=<?php echo $row['evenement_id'];?>"><img src="img/eye.png" alt="voir"/></a><br/>
			<?php
				}
				else{
			?>
					<a href="edit_evenement.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements" title="modifier"><img src="img/pencil.png" alt="modifier"/></a>&nbsp;<a href="<?php echo $rowOrganisme['organisme_url_front'];?>#/?lang=fr&amp;id=<?php echo $row['evenement_id'];?>"><img src="img/eye.png" alt="voir"/></a><br/>
			<?php
				}
				
			?>
				<?php
					$sqlSession ="SELECT * FROM sp_sessions WHERE evenement_id = '".$row['evenement_id']."'";
					$resSession = mysql_query($sqlSession) or die(mysql_error());
					$rowSession = mysql_fetch_array($resSession);
					if($rescountsessions['nb']==1 && $rowSession['session_type_inscription']==2){
						
				?>
						<a href="exportcsv.php?session_id=<?php echo $rowSession['session_id'];?>&amp;export=2" target="_blank" title="XML : récupérer la liste des inscrits pour l'application ScanEvent"><img src="img/xml.png" alt="XML"/></a><a href="exportcsv.php?session_id=<?php echo $rowSession['session_id'];?>&amp;export=0" title="HTML : visualiser et modifier le listing des inscrits"><img src="img/html.png" alt="HTML"/></a><a href="exportcsv.php?session_id=<?php echo $rowSession['session_id'];?>&amp;export=1" target="_blank" title="CSV : récupérer la liste des inscrits pour l'ouvrir dans Excel"><img src="img/csv.png" alt="CSV"/></a><br/>
				<?php
					}
				?>
				</div>
				<div class="places">
			<?php
				$sqlcountsessions = mysql_query("SELECT COUNT(*) AS nb FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'");
				$rescountsessions = mysql_fetch_array($sqlcountsessions);
				if($rescountsessions['nb']==1 && $rowSession['session_type_inscription']==2){
					$totalInterne = $rowSession['session_places_internes_totales'];
					$totalInternePrises = $rowSession['session_places_internes_prises'];
					$totalExterne = $rowSession['session_places_externes_totales'];
					$totalExternePrises = $rowSession['session_places_externes_prises'];
			?>
					<p><span class="prises"><?php echo $totalInternePrises;?></span><span>/<?php echo $totalInterne;?> INT</span></p>
					<p><span class="prises"><?php echo $totalExternePrises;?></span><span>/<?php echo $totalExterne;?> EXT</span></p>
			<?php
				}
				else{
					if($rowSession['session_type_inscription']==2){
			?>
						<p><strong><?php echo $rescountsessions['nb'];?> SESSIONS</strong></p>
			<?php
					}
				}
			?>	
				</div>
				<div class="poubelle">
			<?php	
				
				if(isset($_GET['mois'])){
			?>
					<a href="list.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>&amp;mois=<?php echo $_GET['mois'];?>&amp;year=<?php echo $_GET['year'];?>" onclick="confirmar('list.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>', 'Etes-vous sûr de vouloir supprimer cet événement? Les sessions et images associées seront également supprimées.')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>
			<?php
				}
				else{	
			?>
					<a href="list.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>" onclick="confirmar('list.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>', 'Etes-vous sûr de vouloir supprimer cet événement? Les sessions et images associées seront également supprimées.')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>
			<?php
				}
			?>	
				</div>
				</div>
			<?php
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
			location.href = "list.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenements";
		});	
		
		$("#annee").change(function(){
			var mois=$("#mois").val();
			var annee=$("#annee").val();
			var rubrique=$("#rubrique").val();
			location.href = "list.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenements";
		});
		
		$("#rubrique").change(function(){
			var mois=$("#mois").val();
			var annee=$("#annee").val();
			var rubrique=$("#rubrique").val();
			location.href = "list.php?mois="+mois+"&year="+annee+"&rubrique="+rubrique+"&menu_actif=evenements";
		});	
		
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
	});
</script>
</body>
</html>

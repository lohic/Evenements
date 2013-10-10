<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');



if( isset($_GET['fonction']) ){
	if($_GET['fonction']=="valider"){
		$sql="UPDATE sp_evenements SET evenement_statut=1, evenement_groupe_id= WHERE evenement_id = '".$_GET['id']."'";
		mysql_query($sql) or die(mysql_error());
	}
	
	if($_GET['fonction']=="supprimer"){
		$sql="DELETE FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
		mysql_query($sql) or die(mysql_error());
		
		$sql="DELETE FROM sp_sessions WHERE evenement_id = '".$_GET['id']."'";
		mysql_query($sql) or die(mysql_error());

		/*$dossier="upload/photos/evenement_".$_GET['id'];
		clearDir($dossier);*/
	}
	header("Location:soumissions.php?menu_actif=soumissions");
}
include_once('../vars/constantes_vars.php');
//include_once('../vars/statics_vars.php');

include_once('../classe/classe_core_event.php');
include_once('../classe/fonctions.php');

$core = new core();

if($core->isAdmin && $core->userLevel<=3){ 
	$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
	$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
	$rowGetOrganisme = mysql_fetch_array($resGetOrganisme); 
	
	if($core->userLevel<3){
		$sql = "SELECT * FROM sp_evenements, sp_users WHERE evenement_statut=4 AND evenement_user_id=user_id ORDER BY evenement_date DESC";
		$res = mysql_query($sql)or die(mysql_error());
	}
	else{
		$idGroups= array();
		foreach($core->user_info->groups as $cle => $valeur) 
		{
			$idGroups[]=$cle;
		}
		$idGroups = implode(',',$idGroups);
		
		$sql = "SELECT * FROM sp_evenements, sp_users WHERE evenement_statut=4 AND evenement_user_id=user_id AND evenement_groupe_id IN ($idGroups) ORDER BY evenement_date DESC";
		$res = mysql_query($sql)or die(mysql_error());
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

<script language="JavaScript" src="tools.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
</head>

<body>
<div id="page">
    <?php include("top.php"); ?>
    <div id="menu">
      <?php include("menu.php"); ?>
    </div>
    <div id="content">
		<h3>Liste des soumissions</h3>
	
		<?php
			$iteration = 1;
		
			while($row = mysql_fetch_array($res)){
				$sqlSession ="SELECT * FROM sp_sessions WHERE evenement_id = '".$row['evenement_id']."'";
				$resSession = mysql_query($sqlSession) or die(mysql_error());
				$rowSession = mysql_fetch_array($resSession);
				$jour=date('d/m',$row['evenement_date']);
				
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
				
				$jourDebut = date("d", $rowSession['session_debut']);
				$jourFin = date("d", $rowSession['session_fin']);
				if($jourDebut==$jourFin){
					if(date("H:i", $rowSession['session_fin'])!="23:59"){
						$horaires = date("H", $rowSession['session_debut'])."h".date("i", $rowSession['session_debut'])." > ".date("H", $rowSession['session_fin'])."h".date("i", $rowSession['session_fin']);
					}
					else{
						$horaires = "à ".date("H", $rowSession['session_debut'])."h".date("i", $rowSession['session_debut']);
					}	
				}
				else{
					if(date("H:i", $rowSession['session_fin'])!="23:59"){
						$horaires = "du ".date("d/m/Y", $rowSession['session_debut'])." à ".date("H", $rowSession['session_debut'])."h".date("i", $rowSession['session_debut'])." au ".date("d/m/Y", $rowSession['session_fin'])." à ".date("H", $rowSession['session_fin'])."h".date("i", $rowSession['session_fin']);
					}
					else{
						$horaires = "du ".date("d/m/Y", $rowSession['session_debut'])." à ".date("H", $rowSession['session_debut'])."h".date("i", $rowSession['session_debut'])." au ".date("d/m/Y", $rowSession['session_fin']);
					}
				}
				
				
			?>
				
						<div class="infos soumissions">
							<p class="jour"><?php echo $jour; ?></p>
							<div class="titre_heure">
								<p class="titre"><a href="edit_evenement_unique.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements"><?php echo $row['evenement_titre'];?></a></p>
								<p><?php echo $horaires;?></p>
							</div>	
						</div>
						<div class="liens soumissions">
							<div class="outils">
								<a href="edit_evenement_unique.php?id=<?php echo $row['evenement_id'];?>&amp;menu_actif=evenements&amp;evenement_groupe=<?php echo $_SESSION['id_actual_group'];?>" class="lien_valider"><img src="img/pencil.png" alt="modifier"/></a>
								<a href="soumissions.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>&amp;menu_actif=soumissions" onclick="confirmar('soumissions.php?fonction=supprimer&amp;id=<?php echo $row['evenement_id'];?>&amp;menu_actif=soumissions', 'Etes-vous sûr de vouloir supprimer cette soumission? ')"><img src="img/trash.png" alt="supprimer"/></a>
							</div>
							<div style="float:left;">
								<p>par <a href="mailto:<?php echo $row['user_email'];?>" class="lien_user createur" id="lien_user_<?php echo $row['evenement_id'].'_'.$row['user_id'];?>"><?php echo $row['user_prenom'].' '.$row['user_nom'].' ('.$row['user_email'].')';?></a></p>
							</div>
						</div>
				
						<div id="bloc_modif_<?php echo $row['evenement_id'];?>" class="bloc_modif">

						</div>
					</div>	
			<?php
			}	
			?>
	</div> 
</div>

<script type="text/javascript">
	var sauv_id="";
	
	/*$("a.lien_rubrique").click(function(e){
		e.preventDefault();
		$("div.bloc_modif").css("display","none");
		
		if(sauv_id!=""){
			document.getElementById(sauv_id).innerHTML="";
		}
		
		var tableau_id=$(this).attr("id").split('_');
		var identifiant = "bloc_modif_"+tableau_id[2];
		document.getElementById(identifiant).style.display="block";
			
		$.post("afficherSoumissionAJAX.php", { id: tableau_id[2]},
		function(data){
			document.getElementById(identifiant).innerHTML=data;
		});
		
		sauv_id = identifiant;
	});
	
	
	$("a.lien_user").click(function(e){
		e.preventDefault();
		$("div.bloc_modif").css("display","none");
		
		if(sauv_id!=""){
			document.getElementById(sauv_id).innerHTML="";
		}
		
		var tableau_id=$(this).attr("id").split('_');
		var identifiant = "bloc_modif_"+tableau_id[2];
		document.getElementById(identifiant).style.display="block";
			
		$.post("afficherUserSoumissionAJAX.php", { id: tableau_id[3]},
		function(data){
			document.getElementById(identifiant).innerHTML=data;
		});
		
		sauv_id = identifiant;
	});*/
	
	var actif = getParamValue('menu_actif');
	document.getElementById(actif).className = "actif";
	
</script>
</body>
</html>
<?php
}
else{
	header('Location:index.php?error=1');
}

?>
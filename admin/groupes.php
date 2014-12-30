<?php
include_once('../vars/config.php');
// security
include('cookie.php');
// connection to data base
include('connect.php');
// functions library
include('functions.php');
//include_once('../vars/constantes_vars.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

$core = new core();

//édition ou ajout d'un groupe
if( isset($_POST['type_saisie_groupe'])){
	if($_POST['type_saisie_groupe']=="modification"){
		$sql ="UPDATE sp_groupes SET
					groupe_libelle = '".addslashes($_POST["groupe_libelle"])."',
					groupe_organisme_id = '".$_POST["groupe_organisme_id"]."',
					groupe_editeur_id = '".$_SESSION['id']."',
					groupe_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."'
				WHERE groupe_id = '".$_POST['groupe_id']."'";
		mysql_query($sql) or die(mysql_error());
	}
	
	if($_POST['type_saisie_groupe']=="creation"){
		$sql ="INSERT INTO sp_groupes VALUES ('', '".addslashes($_POST["groupe_libelle_creation"])."', '', '', '".$_POST["groupe_organisme_id_creation"]."', '', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."')";
		mysql_query($sql) or die(mysql_error()); 
	}
	header("Location:groupes.php?menu_actif=logins");  
}

//Suppression d'un groupe
if( isset($_GET['delete_groupe']) ){
	// delete user 
	$sql="DELETE FROM sp_groupes WHERE groupe_id = '".$_GET['delete_groupe']."'";
	mysql_query($sql) or die(mysql_error());
} 



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
</head>

<body>
<div id="page">
	    <?php include("top.php"); ?>
    <div id="menu">
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
		<h3>Liste des groupes</h3>
		<p><a href="#" class="buttonenregistrer" id="creer_groupe">Nouveau groupe</a></p> 
		<div id="bloc_creation_groupe" class="bloc_modif">
			<form name="formGroupCreation" method="post" action="#" id="formGroupCreation">
				<div class="formulaire_large">
					<p><label for="groupe_libelle_creation">Nom :</label><input name="groupe_libelle_creation" type="text" class="inputField" id="groupe_libelle_creation" value=""/></p>
					<!--<p>
						<label for="groupe_type_creation" class="inline">Droits du groupe :</label>
						<select name="groupe_type_creation" id="groupe_type_creation">
							<?php
								$sqlRoles ="SELECT * FROM sp_user_level ORDER BY user_level_libelle ASC";
								$resRoles= mysql_query($sqlRoles) or die(mysql_error());
								while($rowRole = mysql_fetch_array($resRoles)){
									if($rowRole['user_level_level']<=8){
							?>
										<option value="<?php echo $rowRole['user_level_level'];?>"><?php echo $rowRole['user_level_libelle'];?></option>
							<?php 
									}
								}
							?>
						</select>  
					</p>-->
					
					<p>
						<label for="groupe_organisme_id_creation" class="inline">Organisme du groupe :</label>
						<select name="groupe_organisme_id_creation" id="groupe_organisme_id_creation">
							<?php
								$sqlOrganismes ="SELECT * FROM sp_organismes ORDER BY organisme_nom ASC";
								$resOrganismes= mysql_query($sqlOrganismes) or die(mysql_error());
								while($rowOrganisme = mysql_fetch_array($resOrganismes)){
							?>
									<option value="<?php echo $rowOrganisme['organisme_id'];?>"><?php echo utf8_encode($rowOrganisme['organisme_nom']);?></option>
							<?php
								}
							?>
							
						</select>  
					</p>
				</div>
				<div class="liens">
					
				</div>
				<div class="bas_modif">
					<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
					<input name="type_saisie_groupe" type="hidden" id="type_saisie_groupe" value="creation" />
				</div>
			</form>
		</div>
		
		
		<p>&nbsp;</p>
	 	<?php  
			// list all les users
	 		$sql = "SELECT * FROM sp_groupes";
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
						<div class="infos_large"><p><?php echo " <span class=\"uppercase\">".$row['groupe_libelle']."</span>";?></p></div>
						<div class="liens modif">
							<a href="#" id="lien_groupe_<?php echo $row['groupe_id'];?>" class="lien_groupe" title="modifier"><img src="img/pencil.png" alt="modifier"/></a>
							<a href="comptes.php?id=<?php echo $row['groupe_id'];?>&amp;menu_actif=logins"><img src="img/eye.png" alt="voir les comptes associés"/></a>
							<a href="groupes.php?delete_groupe=<?php echo $row['groupe_id'];?>&amp;menu_actif=logins" onclick="confirmar('groupes.php?delete_groupe=<?php echo $row['groupe_id'];?>&amp;menu_actif=logins', 'Etes-vous sûr de vouloir supprimer ce groupe? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a>
						</div>
						<div id="bloc_modif_<?php echo $row['groupe_id'];?>" class="bloc_modif">

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
		
		$("a.lien_groupe").click(function(e){
			e.preventDefault();
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_"+tableau_id[2]; 
			$('.bloc_modif').removeClass('open');
			
			//$(this).removeClass('lien_groupe');
			$('#bloc_modif_'+tableau_id[2]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
		
			if($('#bloc_modif_'+tableau_id[2]).css("display")!="block"){
				$.post("modifGroupeAJAX.php", {  id:tableau_id[2], type:"modification" },
				function(data){
					document.getElementById(identifiant).innerHTML=data; 
					$('#bloc_modif_'+tableau_id[2]).slideToggle();
				});
			} 
			else{
				$('#bloc_modif_'+tableau_id[2]).slideToggle(); 
			}
			sauv_id = identifiant;
		});
	   
		$("a#creer_groupe").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation_groupe";
			document.getElementById('bloc_creation_groupe').style.display="block";
						
		});
		
		var actif = getParamValue('menu_actif');
		//document.getElementById(actif).className = "actif";
		$('#'+actif).addClass('actif');
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

<?php
// security
include_once('../vars/config.php');

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

// édition ou ajout d'un utilisateur
if( isset($_POST['type_saisie'])){
	// query

	if($_POST['type_saisie']=="modification"){ 
		
		$sql ="UPDATE sp_users SET
					user_login = '".$_POST["user_login"]."',
					user_nom = '".addslashes($_POST["user_nom"])."',
					user_prenom = '".addslashes($_POST["user_prenom"])."',
					user_password = '".$_POST["user_password"]."',
					user_alerte = '".$_POST["user_alerte"]."',
					user_email = '".$_POST["user_mail"]."',
					user_type = '".$_POST["user_type"]."', 
					user_account_type = '".$_POST["user_account_type"]."',
					user_editeur_id = '".$_SESSION['id']."',
					user_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."'
				WHERE user_id = '".$_POST['user_id']."'";
		mysql_query($sql) or die(mysql_error());
		
		$sql="DELETE FROM sp_rel_user_groupe WHERE user_id = '".$_POST['user_id']."'";
		mysql_query($sql) or die(mysql_error());
		
		for ($i = 0; $i < count($_POST['groupes']); $i++) {
			$sql ="INSERT INTO sp_rel_user_groupe VALUES ('', '".$_POST['user_id']."', '".$_POST['groupes'][$i]."','')";
			mysql_query($sql) or die(mysql_error());
		}
		
	}
	
	if($_POST['type_saisie']=="creation"){
		$sqlcountcompte = sprintf("SELECT COUNT(*) AS nb FROM sp_users WHERE user_email=%s", GetSQLValueString($_POST["user_mail_creation"], "text"));
		$sqlcountcomptes = mysql_query($sqlcountcompte) or die(mysql_error());
		$rescountcompte = mysql_fetch_array($sqlcountcomptes);

		if($rescountcompte['nb']>0){
			$sqlcomptes = sprintf("SELECT * FROM sp_users WHERE user_email=%s", GetSQLValueString($_POST["user_mail_creation"], "text"));
			$sqlcompte = mysql_query($sqlcomptes) or die(mysql_error());
			$rescompte = mysql_fetch_array($sqlcompte);
			$sql ="UPDATE sp_users SET user_type = '".$_POST["user_type_creation"]."' WHERE user_id = '".$rescompte['user_id']."'";
			mysql_query($sql) or die(mysql_error());
			$lastIdInsert = $rescompte['user_id'];	
		}
		else{
			$sql ="INSERT INTO sp_users VALUES ('', '".addslashes($_POST["user_nom_creation"])."', '".addslashes($_POST["user_prenom_creation"])."', '".$_POST["user_login_creation"]."', '".$_POST["user_password_creation"]."', '".$_POST["user_mail_creation"]."', '".$_POST["user_type_creation"]."', '0', '".$_POST["user_alerte_creation"]."','','".$_POST['user_account_type_creation']."', '', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."')";
			mysql_query($sql) or die(mysql_error()); 
			$lastIdInsert = mysql_insert_id();	
		}
		
		for ($i = 0; $i < count($_POST['groupes']); $i++) {
			$sql ="INSERT INTO sp_rel_user_groupe VALUES ('', '".$lastIdInsert."', '".$_POST['groupes'][$i]."','')";
			mysql_query($sql) or die(mysql_error());
		}
	}
	
   // reedirect
	header("Location:logins.php?menu_actif=logins");
}

//Suppression d'un utilisateur
if( isset($_GET['delete']) ){
	// delete user 
	$sql="DELETE FROM sp_users WHERE user_id = '".$_GET['delete']."'";
	mysql_query($sql) or die(mysql_error()); 
	
	$sql="DELETE FROM sp_rel_user_groupe WHERE user_id = '".$_GET['delete']."'";
	mysql_query($sql) or die(mysql_error());
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
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
		<h3>Liste des logins</h3>
	<?php
	if($core->isAdmin && $core->userLevel<=1){	
	?>
		<p><a href="#" class="buttonenregistrer" id="creer_login">Nouveau login</a></p> 
		<div id="bloc_creation" class="bloc_modif">
			<form name="formLoginCreation" method="post" action="#" id="formLoginCreation">
				<div class="formulaire_large">
					<p><label for="user_nom_creation">Nom :</label><input name="user_nom_creation" type="text" class="inputField" id="user_nom_creation" value=""/></p>
					<p><label for="user_prenom_creation">Prénom :</label><input name="user_prenom_creation" type="text" class="inputField" id="user_prenom_creation" value=""/></p>
					<p><label for="user_login_creation">Identifiant :</label><input name="user_login_creation" type="text" class="inputField" id="user_login_creation" value=""/></p>
					<p><label for="user_password_creation">Mot de passe :</label><input name="user_password_creation" type="text" class="inputField" id="user_password_creation" value=""/></p>
					<p><label for="user_mail_creation">Mail :</label><input name="user_mail_creation" type="text" class="inputField" id="user_mail_creation" value=""/></p>
					<p><label for="user_alerte_creation" class="inline">Alerte :</label><input name="user_alerte_creation" type="checkbox" id="user_alerte_creation" value="1"/></p>
					<div class="clear"></div>
					
					<p>
						<label for="user_type_creation" class="inline">Droits utilisateur :</label>
						<select name="user_type_creation" id="user_type_creation">
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
					</p> 
					
					<p>
						<label for="user_account_type_creation" class="inline">Type de compte :</label>
						<select name="user_account_type_creation" id="user_account_type_creation">
							<option value="mail" selected="selected">mail</option>
							<option value="ldap">ldap</option>
						</select>  
					</p>
					<div class="clear"></div>
					<h4>Groupes :</h4>
					<div class="clear"></div>
					<p> 
						<?php
							$sqlGroupes ="SELECT * FROM sp_groupes ORDER BY groupe_libelle ASC";
							$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
							while($rowGroupe = mysql_fetch_array($resGroupes)){
						?>
								<input type="checkbox" name="groupes[]" value="<?php echo $rowGroupe['groupe_id'];?>" id="groupe_<?php echo $rowGroupe['groupe_id'];?>"/><label for="groupe_<?php echo $rowGroupe['groupe_id'];?>" class="checkbox"><?php echo $rowGroupe['groupe_libelle'];?></label>
						<?php
							}
						?>
					</p>
				</div>
				<div class="liens">
					
				</div>
				<div class="bas_modif">
					<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
					<input name="type_saisie" type="hidden" id="type_saisie" value="creation" />
					
				</div>
			</form>
		</div>
		
-
		<p>&nbsp;</p>
	 	<?php
			$sql = "SELECT * FROM sp_users WHERE user_type<=8";
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
						<div class="infos_large"><p><?php echo $row['user_prenom']." <span class=\"uppercase\">".$row['user_nom']."</span> (".$row['user_login'].")";?></p></div>
						<div class="liens modif"><a href="#" id="lien_login_<?php echo $row['user_id'];?>" class="lien_login" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="logins.php?delete=<?php echo $row['user_id'];?>&amp;menu_actif=logins" onclick="confirmar('logins.php?delete=<?php echo $row['user_id'];?>&amp;menu_actif=logins', 'Etes-vous sûr de vouloir supprimer cet utilisateur? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a></div>
						<div id="bloc_modif_<?php echo $row['user_id'];?>" class="bloc_modif">

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

		var sauv_id="";
		
		$("a.lien_login").click(function(e){
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
				$.post("modifLoginAJAX.php", {  id:tableau_id[2], type:"modification" },
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
		
		$("a#creer_login").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation";
			document.getElementById('bloc_creation').style.display="block";
						
		});
		
	   		
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
	});
</script>
</body>
</html>

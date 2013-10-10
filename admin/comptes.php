<?php
// security
include('cookie.php');
// connection to data base
include('connect.php');
// functions library
include('functions.php');


if(isset($_POST['user'])){
    	
	$sql="DELETE FROM sp_rel_user_groupe WHERE groupe_id = '".$_POST['groupe_id']."'";
	mysql_query($sql) or die(mysql_error());
	
	for ($i = 0; $i < count($_POST['user']); $i++) {
		$sql ="INSERT INTO sp_rel_user_groupe VALUES ('', '".$_POST['user'][$i]."', '".$_POST['groupe_id']."','')";
		mysql_query($sql) or die(mysql_error());
	}
	header("Location:groupes.php");
	
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
		<p>&nbsp;</p> 
		<form name="liste" action="#" method="post">
			<input type="submit" name="button" id="button" value="Valider" class="buttonenregistrer decale comptes" />
	 	<?php
	
	        $sql = "SELECT * FROM sp_users as spu, sp_rel_user_groupe as spru WHERE user_type<='8' AND spru.user_id=spu.user_id AND spru.groupe_id='".$_GET['id']."'";
		 	$res = mysql_query($sql)or die(mysql_error());  
			
			// list all les users
	 		
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
						<div class="liens modif">
						<input type="checkbox" name="user[]" value="<?php echo $row['user_id'];?>" checked="checked" class="checkbox_comptes"/>   
						</div>
					</div>
			<?php
			}
		?>
		<input type="hidden" name="groupe_id" value="<?php echo $_GET['id'];?>"/> 
		</form>
<?php
}
?>   	
	</div>	
</div>


<script type="text/javascript">
	$(window).load(function(){ 
		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
	});
</script>
</body>
</html>

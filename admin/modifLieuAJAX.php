<?php
include_once('../vars/config.php');
// connection to data base
include('connect.php');
include('functions.php');
session_start();
$organismeSelectionne=array();

if($_POST['update']=='create'){
	$sqlGetOrganisme =sprintf("SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id=%s",GetSQLValueString($_SESSION['id_actual_group'], "int"));
	$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
	$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
	$organismeSelectionne[] = $rowGetOrganisme['organisme_id'];
}
else{
	$sql = sprintf("SELECT * FROM sp_lieux as spl, sp_rel_lieu_organisme as sprl WHERE spl.lieu_id=sprl.lieu_id AND spl.lieu_id=%s",GetSQLValueString($_POST['id'], "int"));
	$res = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		$organismeSelectionne[] = $row['organisme_id'];
	}
}
?>

<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="lieu_nom">Lieu :</label><input name="lieu_nom" type="text" class="inputField" id="lieu_nom" value="<?php echo strip_tags($_POST['nom_lieu']);?>"/></p>
		<h4 class="clear_both">Organismes : </h4>
	<?php
		$sqlorganismes ="SELECT * FROM sp_organismes ORDER BY organisme_nom ASC";
		$resorganismes = mysql_query($sqlorganismes) or die(mysql_error());
		while($roworganisme = mysql_fetch_array($resorganismes)){
	?>
			<input type="checkbox" name="organismes[]" value="<?php echo $roworganisme['organisme_id'];?>" id="organisme_<?php echo $roworganisme['organisme_id'];?>" <?php if(in_array($roworganisme['organisme_id'], $organismeSelectionne)){echo "checked";}?> /><label for="organisme_<?php echo $roworganisme['organisme_id'];?>" class="checkbox" ><?php echo $roworganisme['organisme_nom'];?></label>
	<?php
		}
	?>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="lieu_id" type="hidden" id="lieu_id" value="<?php echo $_POST['id'];?>" />
	<input name="update" type="hidden" id="update" value="<?php echo $_POST['update'];?>" />
</form>


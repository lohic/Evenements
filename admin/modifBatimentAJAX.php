<?php
include_once('../vars/config.php');
// connection to data base
include('connect.php');
session_start();

$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
?>

<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="code_batiment_nom">Code bâtiment :</label><input name="code_batiment_nom" type="text" class="inputField" id="code_batiment_nom" value="<?php echo strip_tags($_POST['nom_batiment']);?>"/></p>
		<p><label for="code_batiment_adresse">Adresse bâtiment :</label><input name="code_batiment_adresse" class="inputFieldTiny" type="text" id="code_batiment_adresse" value="<?php echo strip_tags($_POST['adresse_batiment']);?>" /></p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="organisme_id" type="hidden" id="organisme_id" value="<?php echo $rowGetOrganisme['organisme_id'];?>" />
	<input name="code_batiment_id" type="hidden" id="code_batiment_id" value="<?php echo $_POST['id'];?>" />
	<input name="update" type="hidden" id="update" value="<?php echo $_POST['update'];?>" />
</form>


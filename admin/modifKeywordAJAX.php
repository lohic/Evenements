<?php
// connection to data base
include('connect.php');
session_start();

$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
?>

<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="keyword_nom">Mot-clé :</label><input name="keyword_nom" type="text" class="inputField" id="keyword_nom" value="<?php echo strip_tags($_POST['keyword_nom']);?>"/></p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="organisme_id" type="hidden" id="organisme_id" value="<?php echo $rowGetOrganisme['organisme_id'];?>" />
	<input name="keyword_id" type="hidden" id="keyword_id" value="<?php echo $_POST['id'];?>" />
	<input name="update" type="hidden" id="update" value="<?php echo $_POST['update'];?>" />
</form>


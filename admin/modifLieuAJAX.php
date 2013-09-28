<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="lieu_nom">Lieu :</label><input name="lieu_nom" type="text" class="inputField" id="lieu_nom" value="<?php echo strip_tags($_POST['nom_lieu']);?>"/></p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="lieu_id" type="hidden" id="lieu_id" value="<?php echo $_POST['id'];?>" />
	<input name="type_saisie" type="hidden" id="type_saisie" value="<?php echo $_POST['type'];?>" />
</form>


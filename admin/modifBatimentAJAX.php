<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="code_batiment_nom">Code bâtiment :</label><input name="code_batiment_nom" type="text" class="inputField" id="code_batiment_nom" value="<?php echo strip_tags($_POST['nom_batiment']);?>"/></p>
		<p><label for="code_batiment_adresse">Adresse bâtiment :</label><input name="code_batiment_adresse" class="inputFieldTiny" type="text" id="code_batiment_adresse" value="<?php echo strip_tags($_POST['adresse_batiment']);?>" /></p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="code_batiment_id" type="hidden" id="code_batiment_id" value="<?php echo $_POST['id'];?>" />
	<input name="type_saisie" type="hidden" id="type_saisie" value="<?php echo $_POST['type'];?>" />
</form>


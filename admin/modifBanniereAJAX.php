<form name="formRubrique" method="post" enctype="multipart/form-data" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="banniere_chemin">Fichier image :</label><input type="file" name="banniere_chemin" id="banniere_chemin"/></p>
		<p><label for="banniere_lien">Lien pour la bannière :</label><input name="banniere_lien" class="inputField" type="text" id="banniere_lien" value="<?php echo strip_tags($_POST['banniere_lien']);?>" /></p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="banniere_id" type="hidden" id="code_batiment_id" value="<?php echo $_POST['id'];?>" />
	<input name="type_saisie" type="hidden" id="type_saisie" value="<?php echo $_POST['type'];?>" />
</form>


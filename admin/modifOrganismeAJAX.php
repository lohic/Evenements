<?php
include_once('../vars/config.php');
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_organismes WHERE organisme_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

?>

<form name="formOrganisme" method="post" enctype="multipart/form-data" action="#" id="formOrganisme">
	<div class="formulaire_large">
		<p><label for="organisme_nom">Nom :</label><input name="organisme_nom" type="text" class="inputField" id="organisme_nom" value="<?php echo $row3['organisme_nom'];?>"/></p>       
		<p><label for="organisme_google_analytics_id">Identifiant Google :</label><input name="organisme_google_analytics_id" class="inputField" type="text" id="organisme_google_analytics_id" value="<?php echo $row3['organisme_google_analytics_id'];?>" /></p>
		<p><label for="organisme_couleur">Couleur (hexa : #000000) :</label><input name="organisme_couleur" class="inputFieldTiny" type="text" id="organisme_couleur" value="<?php echo $row3['organisme_couleur'];?>" /></p>
		<div class="clear"></div>  
		
		<h4>Bannière</h4>
		<img src="upload/banniere/<?php echo $row3['organisme_id'];?>/<?php echo $row3['organisme_banniere_chemin'];?>" width="600" alt=""/>
	    <p><label for="organisme_banniere_chemin">Fichier image :</label><input type="file" name="organisme_banniere_chemin" id="organisme_banniere_chemin"/></p>
		<a href="organismes.php?fonction=supprimer_banniere&amp;id=<?php echo $row3['organisme_id'];?>&amp;menu_actif=logins" class="supprimer" onclick="confirmar('organismes.php?fonction=supprimer_banniere&amp;id=<?php echo $row3['organisme_id'];?>', 'Etes-vous sûr de vouloir supprimer cette banniere?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>	
		<input type="hidden" name="banniere_cachee" id="banniere_cachee" value="<?php echo $row3['organisme_banniere_chemin']; ?>"/>
		<p><label for="organisme_banniere_lien">Lien pour la bannière :</label><input name="organisme_banniere_lien" class="inputField" type="text" id="organisme_banniere_lien" value="<?php echo $row3['organisme_banniere_lien'];?>" /></p>
		
		<div class="clear"></div>  
		
		<h4>Logo</h4>
		<img src="upload/logo/<?php echo $row3['organisme_id'];?>/<?php echo $row3['organisme_logo_chemin'];?>" alt=""/>
	    <p><label for="organisme_logo_chemin">Fichier image :</label><input type="file" name="organisme_logo_chemin" id="organisme_logo_chemin"/></p>	
		<a href="organismes.php?fonction=supprimer_logo&amp;id=<?php echo $row3['organisme_id'];?>&amp;menu_actif=logins" class="supprimer" onclick="confirmar('organismes.php?fonction=supprimer_logo&amp;id=<?php echo $row3['organisme_id'];?>', 'Etes-vous sûr de vouloir supprimer ce logo?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>		
		<input type="hidden" name="logo_cache" id="logo_cache" value="<?php echo $row3['organisme_logo_chemin']; ?>"/>
		
		<div class="clear"></div>

		<h4>Facebook</h4>
		<img src="upload/banniere_facebook/<?php echo $row3['organisme_id'];?>/<?php echo $row3['organisme_banniere_facebook_chemin'];?>" width="600" alt=""/>
	    <p><label for="organisme_banniere_facebook_chemin">Banniere :</label><input type="file" name="organisme_banniere_facebook_chemin" id="organisme_banniere_facebook_chemin"/></p>	
		<a href="organismes.php?fonction=supprimer_banniere_facebook&amp;id=<?php echo $row3['organisme_id'];?>&amp;menu_actif=logins" class="supprimer" onclick="confirmar('organismes.php?fonction=supprimer_banniere_facebook&amp;id=<?php echo $row3['organisme_id'];?>', 'Etes-vous sûr de vouloir supprimer cette banniere?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>	
		<input type="hidden" name="banniere_facebook_cache" id="banniere_facebook_cache" value="<?php echo $row3['organisme_banniere_facebook_chemin']; ?>"/>
		
		<div class="clear"></div>

		<img src="upload/footer_facebook/<?php echo $row3['organisme_id'];?>/<?php echo $row3['organisme_footer_facebook_chemin'];?>" alt=""/>
	    <p><label for="organisme_footer_facebook_chemin">Footer :</label><input type="file" name="organisme_footer_facebook_chemin" id="organisme_footer_facebook_chemin"/></p>	
		<a href="organismes.php?fonction=supprimer_footer_facebook&amp;id=<?php echo $row3['organisme_id'];?>&amp;menu_actif=logins" class="supprimer" onclick="confirmar('organismes.php?fonction=supprimer_footer_facebook&amp;id=<?php echo $row3['organisme_id'];?>', 'Etes-vous sûr de vouloir supprimer ce footer?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>
	    <input type="hidden" name="footer_facebook_cache" id="footer_facebook_cache" value="<?php echo $row3['organisme_footer_facebook_chemin']; ?>"/>

		<div class="clear"></div>

		<h4>Mentions légales (billet)</h4>
	    <p style="width:600px;"><label for="organisme_mentions">Texte :</label><textarea name="organisme_mentions" rows="10" cols="20" class="inputField tinymce" id="organisme_mentions"><?php echo $row3['organisme_mentions'];?></textarea></p>
	
		<div class="clear"></div>

		<h4>URL Front</h4>
	    <p><label for="organisme_url_front">Chemin :</label><input name="organisme_url_front" type="text" class="inputField" id="organisme_url_front" value="<?php echo $row3['organisme_url_front'];?>"/></p>
	
		<div class="clear"></div>

		<h4>Billet</h4>
		<img src="upload/billet/<?php echo $row3['organisme_id'];?>/<?php echo $row3['organisme_image_billet'];?>" alt=""/>
	    <p><label for="organisme_image_billet">Image pour les billets :</label><input type="file" name="organisme_image_billet" id="organisme_image_billet"/></p>
		<a href="organismes.php?fonction=supprimer_image_billet&amp;id=<?php echo $row3['organisme_id'];?>&amp;menu_actif=logins" class="supprimer" onclick="confirmar('organismes.php?fonction=supprimer_image_billet&amp;id=<?php echo $row3['organisme_id'];?>', 'Etes-vous sûr de vouloir supprimer cette image?')" title="supprimer"><img src="img/trash.png" alt="supprimer"/></a>	
		<input type="hidden" name="image_billet_cache" id="image_billet_cache" value="<?php echo $row3['organisme_image_billet']; ?>"/>
		<p><label for="organisme_url_image">Lien pour l'image des billets :</label><input name="organisme_url_image" class="inputField" type="text" id="organisme_url_image" value="<?php echo $row3['organisme_url_image'];?>" /></p>
	</div>
	<div class="liens">
		
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" /> 
		<input name="organisme_id" type="hidden" id="organisme_id" value="<?php echo $row3['organisme_id'];?>" />
		<input name="type_saisie_organisme" type="hidden" id="type_saisie_organisme" value="modification" />
	</div>
</form> 
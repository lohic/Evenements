<?php
include_once('../vars/config.php');
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_sessions WHERE session_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

$jour_debut = date("d/m/Y",$row3['session_debut']);
$heure_debut = date("H:i",$row3['session_debut']);

$jour_fin = date("d/m/Y",$row3['session_fin']);
$heure_fin = date("H:i",$row3['session_fin']);

if($heure_fin=="23:59"){
	$heure_fin="inconnue";
}
include_once('../classe/classe_core_event.php');
$core = new core();
if($core->isAdmin && $core->userLevel<=1){
	$sqllieux ="SELECT * FROM sp_lieux ORDER BY lieu_nom ASC";
	$reslieux = mysql_query($sqllieux) or die(mysql_error());

	$sqlcodes ="SELECT * FROM sp_codes_batiments ORDER BY code_batiment_nom ASC";
	$rescodes = mysql_query($sqlcodes) or die(mysql_error());
}
else{
	$sqllieux ="SELECT * FROM sp_lieux as spl, sp_rel_lieu_organisme as sprl WHERE spl.lieu_id=sprl.lieu_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY lieu_nom ASC";
	$reslieux = mysql_query($sqllieux) or die(mysql_error());

	$sqlcodes ="SELECT * FROM sp_codes_batiments, sp_rel_batiment_organisme WHERE batiment_id=code_batiment_id AND organisme_id='".$rowGetOrganisme['organisme_id']."' ORDER BY code_batiment_nom ASC";
	$rescodes = mysql_query($sqlcodes) or die(mysql_error());
}

?>
<form id="form<?php echo $row3['session_id']; ?>" name="form<?php echo $row3['session_id']; ?>" method="post" action="#">

	<fieldset>
		<p>
			<label for="session_nom" class="inline">Titre de la session : </label>
			<input name="session_nom" type="text" class="inputField french" id="session_nom" value="<?php echo $row3['session_nom']; ?>"/>
			<input name="session_nom_en" type="text" class="inputField english inputdroit" id="session_nom_en" value="<?php echo $row3['session_nom_en']; ?>"/>
		</p>	
	
		<p>
			<label for="session_date_debut" class="inline">Date de début : </label>
			<input name="session_date_debut" type="text" class="inputFieldShort datepicker" id="session_date_debut" value="<?php echo $jour_debut;?>"/>
			<input name="session_date_fin" type="text" class="inputFieldShort datepicker inputdroit" id="session_date_fin" value="<?php echo $jour_fin;?>"/>
			<label for="session_date_fin" class="inline labeldroit">Date de fin : </label>

		</p>
		<p>
			<label for="session_heure_debut" class="inline">Horaire de début :</label>
			<input name="session_heure_debut" type="text" id="session_heure_debut" class="inputFieldShort" value="<?php echo $heure_debut; ?>"/>
			<input name="session_heure_fin" type="text" class="inputFieldShort inputdroit" id="session_heure_fin" value="<?php echo $heure_fin; ?>"/>
			<label for="session_heure_fin" class="inline labeldroit">Horaire de fin : </label>
		</p>

		<p id="slider_heure_debut"></p>
		<p id="slider_heure_fin" class="inputdroit"></p>
		
		<div class="separateur">&nbsp;</div>
		<p>
			<label for="session_lien" class="inline">Adresse du lien :</label>
			<input name="session_lien" type="text" class="inputField french" id="session_lien" value="<?php echo $row3['session_lien']; ?>"/>
			<input name="session_lien_en" type="text" class="inputField inputdroit english" id="session_lien_en" value="<?php echo $row3['session_lien_en']; ?>"/>
		</p>

		<p>
			<label for="session_texte_lien">Texte du lien :</label>
			<input type="text" name="session_texte_lien" value="<?php echo $row3['session_texte_lien']; ?>" class="inputField french" id="session_texte_lien"/>
			<input type="text" name="session_texte_lien_en" value="<?php echo $row3['session_texte_lien_en']; ?>" class="inputField inputdroit english" id="session_texte_lien_en"/>
		</p>
		<div class="separateur">&nbsp;</div>
		<p>
			<label for="session_langue" class="inline">Langue :</label>
			<select name="session_langue" id="session_langue">
			<?php
				foreach($langues_evenement as $cle => $valeur){
					echo '<option value="'.$valeur.'"';
					if($row3['session_langue']==$valeur){echo "selected=\"selected\"";}
					echo '>'.$cle.'</option>';
				}
			?>
			</select>
		</p>
		<p>
			<label for="session_lieu">Lieu / Salle / Amphi :</label>
			<select name="session_lieu" id="session_lieu" style="width:250px;">
			<?php
				$estsalle = false;
				while($rowlieu = mysql_fetch_array($reslieux)){
					echo '<option value="'.$rowlieu['lieu_id'].'" ';
					if($row3['session_lieu']==$rowlieu['lieu_id']){
						echo "selected=\"selected\"";
						$estsalle =true;
					}
					echo '>'.utf8_encode(stripslashes($rowlieu['lieu_nom'])).'</option>';
				}
				if($estsalle){
					echo '<option value="-1">aucun</option>';
				}
				else{
					echo '<option value="-1" selected="selected">aucun</option>';
				}
			?>	
			</select> 
		</p>
		<p>
			<label for="session_code_batiment">Code du bâtiment :</label>
			<select name="session_code_batiment" id="session_code_batiment"  style="width:300px;">
			<?php
				$estbatiment = false;
				while($rowcode = mysql_fetch_array($rescodes)){
					echo '<option value="'.$rowcode['code_batiment_id'].'" ';
					if($row3['session_code_batiment']==$rowcode['code_batiment_id']){
						echo "selected=\"selected\"";
						$estbatiment = true;
					}
					echo '>'.utf8_encode($rowcode['code_batiment_nom']).' => '.utf8_encode(stripslashes($rowcode['code_batiment_adresse'])).'</option>';
					
				}
			
				if($estbatiment){
					echo '<option value="-1">aucun</option>';
				}
				else{
					echo '<option value="-1" selected="selected">aucun</option>';
				}
			?>
			</select>
		</p>
		
		<p>
			<label for="session_adresse1" class="inline">Nom du lieu : </label>
			<input name="session_adresse1" type="text" class="inputField" id="session_adresse1" value="<?php echo stripslashes($row3['session_adresse1']); ?>"/>
		</p>
		<p>
			<label for="session_adresse2" class="inline">Adresse : </label>
			<textarea name="session_adresse2" class="textareaField" cols="20" rows="2" id="session_adresse2"><?php echo stripslashes($row3['session_adresse2']); ?></textarea>
		</p>
		
		<div class="separateur">&nbsp;</div>
		
		<p>
			<label for="session_type_inscription">Type d'inscription : </label>
			<select name="session_type_inscription" id="session_type_inscription">
				<option value="1" <?php if($row3['session_type_inscription']==1){echo "selected=\"selected\"";}?>>Entrée libre</option>
				<option value="2" <?php if($row3['session_type_inscription']==2){echo "selected=\"selected\"";}?>>Inscription obligatoire par la plateforme</option>
				<option value="3" <?php if($row3['session_type_inscription']==3){echo "selected=\"selected\"";}?>>Inscription obligatoire par mail ou autre</option>
			</select>
			
			<?php
				if($row3['session_type_inscription']==3){
			?>	
					<span id="champ_complement">
						<label for="session_complement_type_inscription">Complément : </label>
						<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value="<?php echo $row3['session_complement_type_inscription']; ?>"/>
					</span>
			<?php
				}else{
			?>
					<span id="champ_complement" style="display:none">
						<label for="session_complement_type_inscription">Complément : </label>
						<input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" class="inputField" value=""/>
					</span>
			<?php
				}
			?>
		
		</p>
		<p>
			<label for="session_code_externe" class="inline">Code d'inscription externe :</label>
			<input name="session_code_externe" type="text" class="inputFieldTiny chiffre" id="session_code_externe" value="<?php echo $row3['session_code_externe']; ?>"/>
		</p>
		
		<p>
			<label for="session_traduction" class="inline">Traduction simultanée :</label>
			<select name="session_traduction" id="session_traduction">
				<option value="1" <?php if($row3['session_traduction']==1){echo "selected=\"selected\"";}?>>Oui</option>
				<option value="0" <?php if($row3['session_traduction']==0){echo "selected=\"selected\"";}?>>Non</option>
			</select>
		</p>
		
		<p>
		<?php
			if($row3['session_code_externe']!=""){
		?>
			<a href="http://www.sciencespo.fr/evenements/inscription_externe.php?code=<?php echo $row3['session_code_externe'];?>&amp;session=<?php echo $row3['session_id'];?>&amp;evenement=<?php echo $row3['evenement_id'];?>">http://www.sciencespo.fr/evenements/inscription_externe.php?code=<?php echo $row3['session_code_externe'];?>&amp;session=<?php echo $row3['session_id'];?>&amp;evenement=<?php echo $row3['evenement_id'];?></a>
		<?php		
			}
			else{
		?>
			<a href="http://www.sciencespo.fr/evenements/inscription/inscription.php?id=<?php echo $row3['session_id'];?>">http://www.sciencespo.fr/evenements/inscription/inscription.php?id=<?php echo $row3['session_id'];?></a>
		<?php
			}
		?>
		</p>
		
		<p>
			<span class="gauche">Ouvert/Fermé</span>
			<span class="centre">Nbr de places internes</span>
			<span class="droite">Nbr de places externes</span>
		</p>

		<p>
			<label for="session_statut_inscription" class="inline">Amphithéâtre : </label>
			<input name="session_statut_inscription" type="checkbox" id="session_statut_inscription" value="1" <?php if($row3['session_statut_inscription']==1){echo "checked";}?>/>
			<input name="session_places_internes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales" value="<?php echo $row3['session_places_internes_totales']; ?>"/>
			<input name="session_places_externes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales" value="<?php echo $row3['session_places_externes_totales']; ?>"/>
		</p>

		<p>
			<label for="session_statut_vision" class="inline">Retransmission :</label>
			<input name="session_statut_vision" type="checkbox" id="session_statut_vision" value="1" <?php if($row3['session_statut_visio']==1){echo "checked";}?>/>
			<input name="session_places_internes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_vision" value="<?php echo $row3['session_places_internes_totales_visio']; ?>"/>
			<input name="session_places_externes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_vision" value="<?php echo $row3['session_places_externes_totales_visio']; ?>"/>	

		</p>
	</fieldset>
	<input name="session_id" type="hidden" id="session_id" value="<?php echo $row3['session_id']?>" />
	<input type="submit" name="button" value="Enregistrer"  class="buttonenregistrer"  />
</form>




<!--


	<p><label for="session_nom" class="inline">Nom de la session : </label><input name="session_nom" type="text" class="inputFieldShort" id="session_nom" value="<?php echo $row3['session_nom']; ?>"/></p>	
	<div class="colonne_gauche">
		<p><label for="session_date_debut" class="inline">Date de début : </label><input name="session_date_debut" type="text" class="inputFieldShort datepicker" id="session_date_debut" value="<?php echo $jour_debut; ?>"/></p>

		<p>
			<label for="session_heure_debut" class="inline">Heure de début :</label><input name="session_heure_debut" type="text" id="session_heure_debut" class="inputFieldShort" value="<?php echo $heure_debut; ?>"/>
		</p>

		<p id="slider_heure_debut"></p>

	</div>

	<div class="colonne_droite">
		<p><label for="session_date_fin" class="inline">Date de fin : </label><input name="session_date_fin" type="text" class="inputFieldShort datepicker" id="session_date_fin" value="<?php echo $jour_fin; ?>"/></p>
		<p><label for="session_heure_fin" class="inline">Heure de fin : </label><input name="session_heure_fin" type="text" class="inputFieldShort" id="session_heure_fin" value="<?php echo $heure_fin; ?>"/></p>
		<p id="slider_heure_fin"></p>
	</div>

	<hr/>
	<p>
		<label for="session_langue" class="inline">Langue de la conférence :</label>
			<?php
			
				echo '<select name="session_langue" id="session_langue">';
				foreach($langues_evenement as $cle => $valeur)
				{
					echo '<option value="'.$valeur.'"';
					if($row3['session_langue']==$valeur){echo "selected=\"selected\"";}
					echo '>'.$cle.'</option>';
				}
				echo '</select>'; 
			?>
	</p>
	<div class="colonne_gauche">
		<p><label for="session_lieu">lieu de l'événement :</label><input type="text" name="session_lieu" value="<?php echo $row3['session_lieu']; ?>" class="inputField" id="session_lieu"/></p>
		<p><label for="session_code_batiment">code du bâtiment :</label>
			<?php
				echo '<select name="session_code_batiment" id="session_code_batiment">';
				
				foreach($batiments as $cle => $valeur)
				{
					echo '<option value="'.$cle.'" ';
					if($row3['session_code_batiment']==$cle){echo "selected=\"selected\"";}
					echo '>'.$valeur.'</option>';
				}
				
				echo '</select>'; 
			?>
		</p>
	</div>

	<div class="colonne_droite">
		<p><label for="session_lieu_en">lieu de l'événement (en):</label><input type="text" name="session_lieu_en" value="<?php echo $row3['session_lieu_en']; ?>" class="inputField" id="session_lieu_en"/></p>
	</div>
	
	<hr/>
	<div class="colonne_gauche">
		<p><label for="session_texte_lien">Texte du lien :</label><input type="text" name="session_texte_lien" value="<?php echo $row3['session_texte_lien']; ?>" class="inputField" id="session_texte_lien"/></p>
		<p><label for="session_lien" class="inline">Lien :</label><input name="session_lien" type="text" class="inputFieldShort" id="session_lien" value="<?php echo $row3['session_lien']; ?>"/></p>
	</div>

	<div class="colonne_droite">
		<p><label for="session_texte_lien_en">Texte du lien (en) :</label><input type="text" name="session_texte_lien_en" value="<?php echo $row3['session_texte_lien_en']; ?>" class="inputField" id="session_texte_lien_en"/></p>
		<p><label for="session_lien_en" class="inline">Lien (en) :</label><input name="session_lien_en" type="text" class="inputFieldShort" id="session_lien_en" value="<?php echo $row3['session_lien_en']; ?>"/></p>
	</div>
	
	<div class="parametres_inscriptions">
		<h3>Inscription :</h3>
		<p><label for="session_type_inscription">Type d'inscription : </label>
			<select name="session_type_inscription" id="session_type_inscription">
				<option value="1" <?php if($row3['session_type_inscription']==1){echo "selected=\"selected\"";}?>>Entrée libre</option>
				<option value="2" <?php if($row3['session_type_inscription']==2){echo "selected=\"selected\"";}?>>Inscription obligatoire par la plateforme</option>
				<option value="3" <?php if($row3['session_type_inscription']==3){echo "selected=\"selected\"";}?>>Inscription obligatoire par mail ou autre</option>
			</select>
		<p style="display:block" id="champ_complement"><label for="session_complement_type_inscription">Complément d'information : </label><input type="text" id="session_complement_type_inscription" name="session_complement_type_inscription" value="<?php echo $row3['session_complement_type_inscription']; ?>"/></p>
		<div class="colonne_gauche">
			<p><label for="session_statut_inscription" class="inline">Inscriptions ouvertes :</label><input name="session_statut_inscription" type="checkbox" id="session_statut_inscription" value="1" <?php if($row3['session_statut_inscription']==1){echo "checked";}?>/></p>
		</div>

		<div class="colonne_droite">
				<p><label for="session_places_internes_totales" class="inline">Nombre total de place (<strong>interne</strong>) :</label><input name="session_places_internes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales" value="<?php echo $row3['session_places_internes_totales']; ?>"/></p>
				<p><label for="session_places_externes_totales" class="inline">Nombre total de place (<strong>externe</strong>) :</label><input name="session_places_externes_totales" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales" value="<?php echo $row3['session_places_externes_totales']; ?>"/></p>
		</div>

		<hr/>

		<div class="colonne_gauche">
			<p><label for="session_statut_vision" class="inline">Inscriptions ouvertes pour la vision conférence :</label><input name="session_statut_vision" type="checkbox" id="session_statut_vision" value="1" <?php if($row3['session_statut_visio']==1){echo "checked";}?>/></p>
		</div>

		<div class="colonne_droite">
				<p><label for="session_places_internes_totales_vision" class="inline">Nombre total de place pour la vision conférence (<strong>interne</strong>) : </label><input name="session_places_internes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_internes_totales_vision" value="<?php echo $row3['session_places_internes_totales_visio']; ?>"/></p>
				<p><label for="session_places_externes_totales_vision" class="inline">Nombre total de place pour la vision conférence (<strong>externe</strong>) : </label><input name="session_places_externes_totales_vision" type="text" class="inputFieldTiny chiffre" id="session_places_externes_totales_vision" value="<?php echo $row3['session_places_externes_totales_visio']; ?>"/></p>
				<p><strong>Adresse de l'événement qui sera inscrit sur le ticket envoyé dans le mail : </strong></p>
				<p><label for="session_adresse1" class="inline"><strong>Adresse 1 : </strong></label><input name="session_adresse1" type="text" class="inputFieldTiny" id="session_adresse1" value="<?php echo $row3['session_adresse1'] ?>"/></p>
				<p><label for="session_adresse2" class="inline"><strong>Adresse 2 : </strong></label><input name="session_adresse2" type="text" class="inputFieldTiny" id="session_adresse2" value="<?php echo $row3['session_adresse2'] ?>"/></p>
		</div>

		<hr/>
	</div>
	
	<p><input type="submit" name="button" id="button" value="Enregistrer" class="button" /></p>
	<input name="session_id" type="hidden" id="session_id" value="<?php echo $row3['session_id']?>" />
	<input name="type_saisie" type="hidden" id="type_saisie" value="modification" />
	
</form>-->
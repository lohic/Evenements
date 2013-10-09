<?php
session_start();
include_once('../vars/config.php');
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_rubriques WHERE rubrique_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

?>

<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="rubrique_titre">Titre :</label><input name="rubrique_titre" type="text" class="inputField french" id="rubrique_titre" value="<?php echo strip_tags($_POST['titre']);?>"/><input name="rubrique_titre_en" type="text" class="inputField english" id="rubrique_titre_en" value="<?php echo strip_tags($_POST['titre_en']);?>"/></p>
		<p><label for="rubrique_couleur">Couleur (hexa : #000000) :</label><input name="rubrique_couleur" class="inputFieldTiny" type="text" id="rubrique_couleur" value="<?php echo $_POST['couleur'];?>" /></p>
		<p>
			<label for="rubrique_groupe_id" class="inline">Groupe propriétaire :</label>
			<select name="rubrique_groupe_id" id="rubrique_groupe_id">
				<?php
					$sqlGroupes ="SELECT * FROM sp_groupes ORDER BY groupe_libelle ASC";
					$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
					while($rowGroupe = mysql_fetch_array($resGroupes)){
				?>
						<option value="<?php echo $rowGroupe['groupe_id'];?>" <?php if($row3['rubrique_groupe_id']==$rowGroupe['groupe_id']){echo "selected=\"selected\"";}?>><?php echo utf8_encode($rowGroupe['groupe_libelle']);?></option>
				<?php
					}
				?>
				
			</select>  
		</p>
		<p>
			<label for="flux_rss" class="inline">Flux RSS :</label>
			<a href="<?php echo ABSOLUTE_URL;?>integration/rss_events.php?cat=<?php echo $_POST['id'];?>" class="rss" target="_blank"><?php echo ABSOLUTE_URL;?>integration/rss_events.php?cat=<?php echo $_POST['id'];?></a>
		</p>
		
		<div class="clear"></div>
		<h4>Partager avec les Groupes :</h4>
		<div class="clear"></div>
		<p> 
			<?php         
				$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo, sp_rubriques as spr WHERE spr.rubrique_groupe_id=spg.groupe_id AND spg.groupe_organisme_id=spo.organisme_id AND rubrique_id='".$_POST['id']."'";
				$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
				$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
			
			
			    /*$sqlOrganisme ="SELECT groupe_organisme_id FROM sp_groupes WHERE groupe_id='".$_SESSION['id_actual_group']."'";
				$resOrganisme= mysql_query($sqlOrganisme) or die(mysql_error());
				$rowOrganisme = mysql_fetch_array($resOrganisme);*/
				
				$sqlGroupes ="SELECT * FROM sp_groupes WHERE groupe_organisme_id!='".$rowGetOrganisme['organisme_id']."' ORDER BY groupe_libelle ASC";
				$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
				while($rowGroupe = mysql_fetch_array($resGroupes)){ 
					$sqlRels ="SELECT * FROM sp_rel_rubrique_groupe WHERE rubrique_id='".$_POST['id']."'";
					$resRels= mysql_query($sqlRels) or die(mysql_error());
					$appartient=false;
					while($rowRel = mysql_fetch_array($resRels)){
						if($rowRel['groupe_id']==$rowGroupe['groupe_id']){
							$appartient=true;
						}
					}
			?>
					<input type="checkbox" name="groupes[]" value="<?php echo $rowGroupe['groupe_id'];?>" id="groupe_<?php echo $rowGroupe['groupe_id'];?>" <?php if($appartient){echo "checked";}?> /><label for="groupe_<?php echo $rowGroupe['groupe_id'];?>" class="checkbox" ><?php echo $rowGroupe['groupe_libelle'];?></label>
			<?php
				}
			?>
		</p>

	</div>
	<div class="liens">
		<div class="couleur_modif" style="background:<?php echo $_POST['couleur'];?>" id="couleur"><p id="couleur_rubrique_<?php echo $_POST['id'];?>" style="display:none"><?php echo $_POST['couleur'];?></p></div>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="rubrique_id" type="hidden" id="rubrique_id" value="<?php echo $_POST['id'];?>" />
	<input name="type_saisie" type="hidden" id="type_saisie" value="<?php echo $_POST['type'];?>" />
</form>


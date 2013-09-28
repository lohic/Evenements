<?php
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_groupes WHERE groupe_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

?>

<form name="formGroup" method="post" action="#" id="formGroup">
	<div class="formulaire_large">
		<p><label for="groupe_libelle">Nom :</label><input name="groupe_libelle" type="text" class="inputField" id="groupe_libelle" value="<?php echo $row3['groupe_libelle'];?>"/></p>
		<!--<p>
			<label for="groupe_type" class="inline">Droits du groupe :</label>
			<select name="groupe_type" id="groupe_type">
				<?php
					$sqlRoles ="SELECT * FROM sp_user_level ORDER BY user_level_libelle ASC";
					$resRoles= mysql_query($sqlRoles) or die(mysql_error());
					while($rowRole = mysql_fetch_array($resRoles)){
						if($rowRole['user_level_level']<=8){
				?>
							<option value="<?php echo $rowRole['user_level_level'];?>" <?php if($row3['groupe_type']==$rowRole['user_level_level']){echo "selected=\"selected\"";}?>><?php echo $rowRole['user_level_libelle'];?></option>
				<?php 
						}
					}
				?>
			</select>  
		</p>-->
		
		<p>
			<label for="groupe_organisme_id" class="inline">Organisme du groupe :</label>
			<select name="groupe_organisme_id" id="groupe_organisme_id">
				<?php
					$sqlOrganismes ="SELECT * FROM sp_organismes ORDER BY organisme_nom ASC";
					$resOrganismes= mysql_query($sqlOrganismes) or die(mysql_error());
					while($rowOrganisme = mysql_fetch_array($resOrganismes)){
				?>
						<option value="<?php echo $rowOrganisme['organisme_id'];?>" <?php if($row3['groupe_organisme_id']==$rowOrganisme['organisme_id']){echo "selected=\"selected\"";}?>><?php echo utf8_encode($rowOrganisme['organisme_nom']);?></option>
				<?php
					}
				?>
				
			</select>  
		</p>
	</div>
	<div class="liens">
		
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />   
		<input name="groupe_id" type="hidden" id="groupe_id" value="<?php echo $row3['groupe_id'];?>" />
		<input name="type_saisie_groupe" type="hidden" id="type_saisie_groupe" value="modification" />
	</div>
</form>


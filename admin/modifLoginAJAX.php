<?php
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_users WHERE user_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

?>


<form name="formLoginCreation" method="post" action="#" id="formLoginCreation">
	<div class="formulaire_large">
		<p><label for="user_nom">Nom :</label><input name="user_nom" type="text" class="inputField" id="user_nom" value="<?php echo $row3['user_nom'];?>"/></p>
		<p><label for="user_prenom">Prénom :</label><input name="user_prenom" type="text" class="inputField" id="user_prenom" value="<?php echo $row3['user_prenom'];?>"/></p>
		<p><label for="user_login">Identifiant :</label><input name="user_login" type="text" class="inputField" id="user_login" value="<?php echo $row3['user_login'];?>"/></p>
		<p><label for="user_password">Mot de passe :</label><input name="user_password" type="text" class="inputField" id="user_password" value="<?php echo $row3['user_password'];?>"/></p>
		<p><label for="user_mail">Mail :</label><input name="user_mail" type="text" class="inputField" id="user_mail" value="<?php echo $row3['user_email'];?>"/></p>
		<p><label for="user_alerte">Alerte :</label><input name="user_alerte" type="checkbox" id="user_alerte" value="1" <?php if($row3['user_alerte']==1){echo "checked";}?>/></p>
		<div class="clear"></div>
		
		<p>
			<label for="user_type" class="inline">Droits utilisateur :</label>
			<select name="user_type" id="user_type"> 
				<?php
					$sqlRoles ="SELECT * FROM sp_user_level ORDER BY user_level_libelle ASC";
					$resRoles= mysql_query($sqlRoles) or die(mysql_error());
					while($rowRole = mysql_fetch_array($resRoles)){
						if($rowRole['user_level_level']<=8){
				?>
							<option value="<?php echo $rowRole['user_level_level'];?>" <?php if($row3['user_type']==$rowRole['user_level_level']){echo "selected=\"selected\"";}?>><?php echo $rowRole['user_level_libelle'];?></option>
				<?php 
						}
					}
				?>
			</select>  
		</p>
		
		<p>
			<label for="user_account_type" class="inline">Type de compte :</label>
			<select name="user_account_type" id="user_account_type">
				<option value="mail" <?php if($row3['user_account_type']=="mail"){echo "selected=\"selected\"";}?>>mail</option>
				<option value="ldap" <?php if($row3['user_account_type']=="ldap"){echo "selected=\"selected\"";}?>>ldap</option>
			</select>  
		</p> 
		<div class="clear"></div>
		<h4>Groupes :</h4>
		<div class="clear"></div>
		<p> 
			<?php
				$sqlGroupes ="SELECT * FROM sp_groupes ORDER BY groupe_libelle ASC";
				$resGroupes= mysql_query($sqlGroupes) or die(mysql_error());
				while($rowGroupe = mysql_fetch_array($resGroupes)){ 
					$sqlRels ="SELECT * FROM sp_rel_user_groupe WHERE user_id='".$row3['user_id']."'";
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
		
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
		<input name="user_id" type="hidden" id="user_id" value="<?php echo $row3['user_id'];?>" /> 
		<input name="type_saisie" type="hidden" id="type_saisie" value="modification" />
	</div>
</form>





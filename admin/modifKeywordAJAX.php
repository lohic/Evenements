<?php
include_once('../vars/config.php');
// connection to data base
include('connect.php');
include('functions.php');
session_start();

if($_POST['update']=='create'){
	$sqlGetOrganisme =sprintf("SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id=%s",GetSQLValueString($_SESSION['id_actual_group'], "int"));
	$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
	$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
	$organismeSelectionne = $rowGetOrganisme['organisme_id'];
}
else{
	$sql = sprintf("SELECT * FROM sp_keywords WHERE keyword_id=%s",GetSQLValueString($_POST['id'], "int"));
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$organismeSelectionne = $row['keyword_organisme_id'];
}

?>

<form name="formRubrique" method="post" action="#" id="formRubrique">
	<div class="formulaire_large">
		<p><label for="keyword_nom">Mot-clé :</label><input name="keyword_nom" type="text" class="inputField" id="keyword_nom" value="<?php echo strip_tags($_POST['nom_keyword']);?>"/></p>
		<p>
			<label for="organisme_id">Organisme : </label>
			<select name="organisme_id" id="organisme_id" style="width:250px;">
			<?php
				$sqlorganismes ="SELECT * FROM sp_organismes ORDER BY organisme_nom ASC";
				$resorganismes = mysql_query($sqlorganismes) or die(mysql_error());
				while($roworganisme = mysql_fetch_array($resorganismes)){
					echo '<option value="'.$roworganisme['organisme_id'].'" ';
					if($organismeSelectionne==$roworganisme['organisme_id']){
						echo "selected=\"selected\"";
					}
					echo '>'.utf8_encode(stripslashes($roworganisme['organisme_nom'])).'</option>';
				}
			?>	
			</select>
		</p>
	</div>
	<div class="bas_modif">
		<input type="submit" name="button" id="button" value="enregistrer" class="buttonenregistrer decale" />
	</div>
	<input name="keyword_id" type="hidden" id="keyword_id" value="<?php echo $_POST['id'];?>" />
	<input name="update" type="hidden" id="update" value="<?php echo $_POST['update'];?>" />
</form>


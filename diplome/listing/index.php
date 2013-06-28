<?php


include_once("../var/vars.php");
include_once("../var/classe_connexion.php");
include_once("../php/fonctions.php");

$connect		= new connexion($news_cInfo['server'],$news_cInfo['user'],$news_cInfo['password'],$news_cInfo['db']);

if($_POST['action'] == 'suppr'){
	$updateSQL 		= sprintf("DELETE FROM sp_diplomation WHERE id=%s", GetSQLValueString($_POST['id_suppr'], 'text'));
		$update_query	= mysql_query($updateSQL) or die(mysql_error());
}


if($_POST['action'] == 'edit_form'){
	
	$updateSQL 		= sprintf("UPDATE sp_diplomation SET year=%s, id_session=%s, genre=%s, nom=%s, prenom=%s, id_etudiant=%s, nationalite=%s, filiere=%s, double_diplome=%s, mail_sciencespo=%s, mail_perso=%s, adresse=%s, cp=%s, ville=%s, pays=%s, tel_fixe=%s, tel_mobile=%s, adresse_perm=%s, cp_perm=%s, ville_perm=%s, pays_perm=%s, tel_fixe_perm=%s, boursier=%s, boursier_type=%s,handicap=%s,present=%s, medaille=%s WHERE id=%s",
														GetSQLValueString($_POST['year'], 'text'),
														GetSQLValueString($_POST['id_session'], 'int'),
														GetSQLValueString($_POST['genre'], 'text'),
														GetSQLValueString($_POST['nom'], 'text'),
														GetSQLValueString($_POST['prenom'], 'text'),
														GetSQLValueString($_POST['id_etudiant'], 'int'),
														GetSQLValueString($_POST['nationalite'], 'text'),
														GetSQLValueString($_POST['filiere'], 'text'),
														GetSQLValueString($_POST['double_diplome'], 'text'),
														GetSQLValueString($_POST['mail_sciencespo'], 'text'),
														GetSQLValueString($_POST['mail_perso'], 'text'),
														GetSQLValueString($_POST['adresse'], 'text'),
														GetSQLValueString($_POST['cp'], 'text'),
														GetSQLValueString($_POST['ville'], 'text'),
														GetSQLValueString($_POST['pays'], 'text'),
														GetSQLValueString($_POST['tel_fixe'], 'text'),
														GetSQLValueString($_POST['tel_mobile'], 'text'),
														GetSQLValueString($_POST['adresse_perm'], 'text'),
														GetSQLValueString($_POST['cp_perm'], 'text'),
														GetSQLValueString($_POST['ville_perm'], 'text'),
														GetSQLValueString($_POST['pays_perm'], 'text'),
														GetSQLValueString($_POST['tel_fixe_perm'], 'text'),
														GetSQLValueString($_POST['boursier'], 'boolean'),
														GetSQLValueString($_POST['boursier_type'], 'text'),
														GetSQLValueString($_POST['handicap'], 'boolean'),
														GetSQLValueString($_POST['present'], 'boolean'),
														GetSQLValueString($_POST['medaille'], 'boolean'),
														GetSQLValueString($_POST['id'], 'int'));
	$update_query	= mysql_query($updateSQL) or die(mysql_error());
}

if(!empty($_GET['year'])){
	//$listeInscrits = "SELECT * FROM sp_diplomation WHERE year='".$_GET['year']."' ORDER BY id,nom, prenom, mail_sciencespo, filiere";
	$listeInscrits = "SELECT d.id, d.year, d.id_session, d.genre, d.nom, d.prenom, d.id_etudiant, d.nationalite, d.filiere, d.double_diplome, d.mail_sciencespo, d.mail_perso, d.adresse, d.cp, d.ville, d.pays, d.tel_fixe, d.tel_mobile, d.adresse_perm, d.cp_perm, d.ville_perm, d.pays_perm, d.tel_fixe_perm, d.boursier, d.boursier_type, d.handicap, d.present, d.medaille, d.date_valid ,i.inscrit_date, i.inscrit_unique_id
FROM sp_diplomation d
	LEFT OUTER JOIN sp_inscrits i ON d.mail_sciencespo = i.inscrit_mail AND i.inscrit_session_id = '428'
WHERE d.year='".$_GET['year']."'
ORDER BY d.id";
	
	$listeInscrits_query	= mysql_query($listeInscrits) or die(mysql_error());
}

if(!empty($_GET['edit'])){
	$editInfos = "SELECT * FROM sp_diplomation WHERE id='".$_GET['edit']."'";
	$editInfos_query	= mysql_query($editInfos) or die(mysql_error());
}


if(isset($_GET['export'])){
	$FileName = 'export-inscrits-'.$_GET['year'] . '.csv';
	$Content = "";
	
	$Content .= "\"ID\",\"ID billet\",\"date billet\",\"date inscription\",\"présent\",\"Genre\",\"Prénom\",\"Nom\",\"N° étudiant\",\"Nationalité\",\"Filière\",\"Double Diplôme\",\"email SciencesPo\",\"email perso\",\"adresse\",\"CP\",\"ville\",\"pays\",\"tél fixe\",\"tél mobile\",\"adresse perm\",\"CP perm\",\"ville perm\",\"pays perm\",\"tél perm\",boursier,\"type de bourse\",\"handicap\",\"présent\",\"médaille\" \n";
	
	while ($row = mysql_fetch_assoc($listeInscrits_query)) {

		$Content .= '"'.$row['id'].'"';
		$Content .= ',"'.$row['inscrit_unique_id'].'"';
		$Content .= ',"'.(isset($row['inscrit_date'])?date('y/m/d H:i:s',$row['inscrit_date']):'').'"';
		$Content .= ',"'.$row['date_valid'].'"';
		$Content .= ',"'.$row['present'].'"';
		$Content .= ',"'.$row['genre'].'"';
		$Content .= ',"'.$row['prenom'].'"';
		$Content .= ',"'.$row['nom'].'"';
		$Content .= ',"'.(!empty($row['id_etudiant'])?$row['id_etudiant']:'#').'"';
		$Content .= ',"'.$row['nationalite'].'"';
		$Content .= ',"'.$row['filiere'].'"';
		$Content .= ',"'.$row['double_diplome'].'"';
		$Content .= ',"'.$row['mail_sciencespo'].'"';
		$Content .= ',"'.$row['mail_perso'].'"';
		$Content .= ',"'.$row['adresse'].'"';
		$Content .= ',"'.$row['cp'].'"';
		$Content .= ',"'.$row['ville'].'"';
		$Content .= ',"'.$row['pays'].'"';
		$Content .= ',"'.$row['tel_fixe'].'"';
		$Content .= ',"'.$row['tel_mobile'].'"';
		$Content .= ',"'.$row['adresse_perm'].'"';
		$Content .= ',"'.$row['cp_perm'].'"';
		$Content .= ',"'.$row['ville_perm'].'"';
		$Content .= ',"'.$row['pays_perm'].'"';
		$Content .= ',"'.$row['tel_fixe_perm'].'"';
		$Content .= ',"'.$row['boursier'].'"';
		$Content .= ',"'.$row['boursier_type'].'"';
		$Content .= ',"'.$row['handicap'].'"';
		$Content .= ',"'.$row['present'].'"';
		$Content .= ',"'.$row['medaille'].'"';
		$Content .= " \n";
	}
	
	
	header('Content-Type: application/csv'); 
	//header("Content-length: " . filesize($NewFile)); 
	header('Content-Disposition: attachment; filename="' . $FileName . '"'); 
	echo $Content;
	exit(); 
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></title>

<link rel="stylesheet" href="../css/supersized.core.css" type="text/css" media="screen" />
<?php 

///// CIBLE
///// inscription/inscription.php?id=428

?>

<style>
body{
	margin:0;
	font-family:Arial, Helvetica, sans-serif;
	background:#E3DCC7;
	font-size:14px;
	color:#F06;
}

#enquete{
	position:absolute;
	width:2400px;
	padding:10px;
	margin:40px 0 20px 50px;
}

#form_save{
	width:800px;
}

#logo{
	margin-bottom:20px;
}

label{
	width:220px;
	display:inline-block;
	text-transform:uppercase;
	font-size:14px;
	font-weight:bold;
}

label.large{
	width:620px;
}

input{
	display:inline-block;
	width:400px;
	background:none;
	color:#333;
	border:solid 1px #F06;
	padding:2px;
	font-size:14px;
}

input.short{
	display:inline-block;
	width:200px;
}

input[type=radio],input[type=checkbox]{
	display:inline-block;
	width:20px;
}

input[type=submit]{
	display:inline-block;
	width:auto;
	color:#F6F3E9;
	background:#F06;
	text-transform:uppercase;
	float:right;
	cursor:pointer;
	padding:4px;
}

input[readonly] {
   border:solid 1px #333;
}

fieldset{
	border:0;
	margin:10px 0;
	padding:5px 0px;
}

p{
	padding:0;
	margin:5px 0;
	font-size:16px;
	font-style:italic;
}

.reset{
	clear:both;
}

#note{
	font-size:12px;
	font-style:italic;
	width:400px;
	float:left;
}

#alerte{
	background:#F06;
	color:#FFF;
	padding:5px;
	font-style:italic;
	margin:20px 0;
}

.ligne{
	font-size:11px;
}

.fond0{
	background:#CCC;
}

.fond1{
	background:#FFF;
}

.identite{
}
.filiere{
}
.double_diplome{
}
.mails{
}
.adresse{
}
.infos{
}
.handicap{
}
.present{
}
.medaille{
}

td{
	padding:5px;
}
table, td{
		color:#333;
}

.over{
	background:#333;
	color:#FFF;
}

table .legende td{
	color:#FFF;
	background:#F06;
}

a{
	text-decoration:none;
	color:#F06;
}

</style>


<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../js/supersized.3.1.3.js"></script>

<script type="text/javascript">  
	$(function($){
		$.supersized({
			//Background image
			slides	:  [ { image : '../img/fond.jpg' } ]					
		});
	});
	
</script>
<script>
	$(document).ready(function(){
		$overA = false;
		
		$('a').mouseover(function(){
			$overA = true;
		});
		
		$('a').mouseout(function(){
			$overA = false;
		});
		
		$('tr').click(function(){
			if(!$overA){
				alert($(this).find('.identite').text());
			}
		});
		
		$('tr').mouseover(function(){
			$(this).find('td').addClass('over');
		});
		
		$('tr').mouseout(function(){
			$(this).find('td').removeClass('over');
		});
	});
	
	function supprInscrit(id,nom){
		if(confirm('Voulez vous supprimer l\'inscrit '+nom+' ? Cette action est irréversible.')){
			$('#id_suppr').val(id);
			$('#suppr_form').submit();
		}
	}
</script>

</head>



<body>


<div id="enquete">
	<img src="../img/ScPo-logo-Rouge.gif" width="184" height="50" id="logo" alt="Logo Sciences Po" />
    
    
    <?php if(!isset($_GET['edit'])) { ?>
    <h1>Liste des inscrits à la Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></h1>    
    
  <?php
	$nbrTotal		= 0;
    $nbrPresent		= 0;
	$nbrMedaille	= 0;
	$nbrHandicap	= 0;
	
	$i = 0;
	
	
	$retour .= '<tr class="ligne legende">';
	$retour .= '<td width="60"></td>';
	$retour .= '<td>ID</td>';
	$retour .= '<td>ID billet</td>';
	$retour .= '<td>date billet</td>';
	$retour .= '<td>date inscription</td>';
	$retour .= '<td class="present">présent</td>';
	$retour .= '<td class="identite">Prénom Nom</td>';
	$retour .= '<td>N° étudiant</td>';
	$retour .= '<td>Nationalité</td>';
	$retour .= '<td class="filiere">Filière</td>';
	$retour .= '<td class="doucle_diplome">Double Diplôme</td>';
	$retour .= '<td class="mails">email SciencesPo // email perso</td>';
	$retour .= '<td class="adresse">adresse</td>';
	$retour .= '<td>CP - ville</td>';
	$retour .= '<td>pays</td>';
	$retour .= '<td>tél fixe</td>';
	$retour .= '<td>tél mobile</td>';
	$retour .= '<td class="adresse">adresse perm</td>';
	$retour .= '<td>CP - ville perm</td>';
	$retour .= '<td>pays perm</td>';
	$retour .= '<td>tél perm</td>';
	$retour .= '<td>boursier</td>';
	$retour .= '<td>type de bourse</td>';
	$retour .= '<td class="handicap">handicap</td>';
	$retour .= '<td class="present">présent</td>';
	$retour .= '<td class="medaille">médaille</td>';
	$retour .= '</tr>';
	
	while ($row = mysql_fetch_assoc($listeInscrits_query)) {
		$nbrTotal		++;
		$nbrPresent		+= $row['present'];
		$nbrMedaille	+= $row['medaille'];
		$nbrHandicap	+= $row['handicap'];
		
		
		$retour .= '<tr class="ligne fond'.$i.'">';
		
		$genre = '';
		switch($row['genre']){
			case 'monsieur' :
				$genre = 'Mr';
			break;
			case 'madame' :
				$genre = 'Mme';
			break;
			case 'mademoiselle' :
				$genre = 'Melle';
			break;
			default :
				$genre = 'Mr';
			break;	
		}
		
		
		$retour .= '<td><a href="?edit='.$row['id'].'">edit</a> / <a href="#" class="suppr" onclick="supprInscrit('.$row['id'].',\''.$row['prenom'].' '.$row['nom'].'\')">suppr</a></td>';
		$retour .= '<td>'.$row['id'].'</td>';
		$retour .= '<td>'.$row['inscrit_unique_id'].'</td>';
		$retour .= '<td>'.(isset($row['inscrit_date'])?date('y/m/d H:i:s',$row['inscrit_date']):'').'</td>';
		$retour .= '<td>'.$row['date_valid'].'</td>';
		$retour .= '<td class="present" align="center" >'.($row['present']=='1'?'x':'o').'</td>';
		$retour .= '<td class="identite">'.$genre.' '.$row['prenom'].' '.$row['nom'].'</td>';
		$retour .= '<td>'.(!empty($row['id_etudiant'])?$row['id_etudiant']:'#').'</td>';
		$retour .= '<td>'.$row['nationalite'].'</td>';
		$retour .= '<td class="filiere">'.$row['filiere'].'</td>';
		$retour .= '<td class="doucle_diplome">'.$row['double_diplome'].'</td>';
		$retour .= '<td class="mails"><a href="mailto:'.$row['mail_sciencespo'].'">'.$row['mail_sciencespo'].'</a> // <a href="mailto:'.$row['mail_perso'].'">'.$row['mail_perso'].'</a></td>';
		$retour .= '<td class="adresse">'.$row['adresse'].'</td>';
		$retour .= '<td>'.$row['cp'].' - '.$row['ville'].'</td>';
		$retour .= '<td>'.$row['pays'].'</td>';
		$retour .= '<td>'.$row['tel_fixe'].'</td>';
		$retour .= '<td>'.$row['tel_mobile'].'</td>';
		$retour .= '<td class="adresse">'.$row['adresse_perm'].'</td>';
		$retour .= '<td>'.$row['cp_perm'].' - '.$row['ville_perm'].'</td>';
		$retour .= '<td>'.$row['pays_perm'].'</td>';
		$retour .= '<td>'.$row['tel_fixe_perm'].'</td>';
		$retour .= '<td align="center">'.($row['boursier']=='1'?'x':'o').'</td>';
		$retour .= '<td>'.$row['boursier_type'].'</td>';
		$retour .= '<td class="handicap" align="center" >'.($row['handicap']=='1'?'x':'o').'</td>';
		$retour .= '<td class="present" align="center" >'.($row['present']=='1'?'x':'o').'</td>';
		$retour .= '<td class="medaille" align="center" >'.($row['medaille']=='1'?'x':'o').'</td>';
		$retour .= '</tr>'."\n";
	
		$i = ($i+1)%2;
	
	} ?>
    
    <div id="resume">
    	<p>>><a href="?year=<?php echo $_GET['year']; ?>&export">Exporter en CSV</a></p>
    	<p><span>Nombre de diplômés ayant répondu au formulaire :</span> <?php echo $nbrTotal; ?></p>
    	<p><span>Nombre de présents :</span> <?php echo $nbrPresent; ?></p>
    	<p><span>Nombre de personnes en situation de handicap :</span> <?php echo $nbrHandicap; ?></p>
    	<p><span>Nombre de médailles :</span> <?php echo $nbrMedaille; ?></p>
    </div>
    <form id="suppr_form" action="" method="post" >
    	<input type="hidden" name="action" value="suppr" id="action" />
    	<input type="hidden" name="id_suppr" value="" id="id_suppr" />
    </form>
    <div id="listing">
    	<table cellpadding="4" cellspacing="0" width="">
    	<?php echo $retour;?>
    	</table>
    </div>
    
    <?php }else{ 
	
	while ($row = mysql_fetch_assoc($editInfos_query)) {
	?>
    <form id="form_save" name="form_save" method="post" action="">
    	<input type="hidden" value="edit_form" name="action" id="action" />
    	<input type="hidden" value="<?php echo $row['id']; ?>" name="id" id="id" />
    	<input type="hidden" value="<?php echo date('Y'); ?>" name="year" id="year" />
    	<input type="hidden" value="428" name="id_session" id="id_session" />
        <p><a href="?year=<?php echo $row['year']; ?>">Retour au listing</a></p>
        
        <fieldset>
            <p>Serez-vous présent(e) à la Cérémonie et soirée du Diplôme le vendredi 1er juillet ?<br />(modifier cette valeur ne permet pas d'inscrire l'étudiant à la session)</p>
            <p><input name="present" type="radio" id="present_0" value="1" <?php if($row['present'] =='1'){ echo 'checked="checked"'; }?> /><label for="present_0">Oui</label>
               <input name="present" type="radio" id="present_1" value="" <?php if($row['present'] !='1'){ echo 'checked="checked"'; }?> /><label for="present_1">Non</label></p>
        </fieldset>
        
        <h2>Renseignements Promotion <?php echo date('Y'); ?></h2>
        
        <?php if($uncomplete){ ?>    
        <div id="alerte">Attention certains champs * obligatoires n'ont pas été remplis.</div>
        <?php } ?>
        <fieldset>
            <p>
              <input name="genre" type="radio" id="genre_0" value="madame" <?php if($row['genre'] =='madame'){ echo 'checked="checked"'; }?> /><label for="genre_0">Madame</label>
              <input name="genre" type="radio" id="genre_1" value="mademoiselle" <?php if($row['genre'] =='mademoiselle'){ echo 'checked="checked"'; }?> /><label for="genre_1">Mademoiselle</label>
              <input name="genre" type="radio" id="genre_2" value="monsieur" <?php if($row['genre'] =='monsieur'){ echo 'checked="checked"'; }?> /><label for="genre_2">Monsieur</label>
            </p>
            <p>
              <label for="nom">Nom* :</label><input type="text" name="nom" id="nom" value="<?php echo $row['nom'];?>" class="obligatory" />
            </p>
            <p>
              <label for="prenom">Prénom* :</label><input type="text" name="prenom" id="prenom" value="<?php echo $row['prenom'];?>" class="obligatory" />
            </p>
            <p>
              <label for="id_etudiant">Numéro d'étudiant* :</label><input name="id_etudiant" value="<?php echo $row['id_etudiant'];?>" type="text" class="short obligatory" id="id_etudiant" />
            </p>
            <p>
              <label for="nationalite">Nationalité :</label><input type="text" name="nationalite" id="nationalite" value="<?php echo $row['nationalite']; ?>" />
            </p>
            <p>
              <label for="filiere">Master / Filière :</label><input type="text" name="filiere" id="filiere" value="<?php echo $row['filiere']; ?>" />
            </p>
            <p>
              <label for="double_diplome">Double diplôme :</label><input type="text" name="double_diplome" id="double_diplome" value="<?php echo $row['double_diplome']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>
              <label for="mail_sciencespo">E-mail Sciences Po :</label><input name="mail_sciencespo" type="text" class="" value="<?php echo $row['mail_sciencespo'];?>" id="mail_sciencespo" />
            </p>
            <p>
              <label for="mail_perso">E-mail Personnel* :</label><input name="mail_perso" type="text" class="obligatory" id="mail_perso" value="<?php echo $row['mail_perso']; ?>"  />
            </p>
            <p>
              <label for="adresse">Adresse :</label><input type="text" name="adresse" id="adresse" value="<?php echo $row['adresse']; ?>" />
            </p>
            <p>
              <label for="cp">Code postal :</label><input name="cp" type="text" class="short" id="cp" value="<?php echo $row['cp']; ?>" />
            </p>
            <p>
              <label for="ville">Ville :</label><input type="text" name="ville" id="ville" value="<?php echo $row['ville']; ?>" />
            </p>
            <p>
              <label for="pays">Pays :</label><input type="text" name="pays" id="pays" value="<?php echo $row['pays']; ?>" />
            </p>
            <p>
              <label for="tel_fixe">Tél fixe :</label><input name="tel_fixe" type="text" class="short" id="tel_fixe" value="<?php echo $row['tel_fixe']; ?>" />
            </p>
            <p>
              <label for="tel_mobile">Tél mobile* :</label><input name="tel_mobile" type="text" class="short obligatory" id="tel_mobile" value="<?php echo $row['tel_mobile']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>
              <label for="adresse_perm">Adresse permanente (parents, etc.) :</label><input type="text" name="adresse_perm" id="adresse_perm" value="<?php echo $row['adresse_perm']; ?>" />
            </p>
            <p>
              <label for="cp_perm">Code postal :</label><input name="cp_perm" type="text" class="short" id="cp_perm" value="<?php echo $row['cp_perm']; ?>" />
              </p>
            <p>
              <label for="ville_perm">Ville :</label><input type="text" name="ville_perm" id="ville_perm" value="<?php echo $row['ville_perm']; ?>" />
            </p>
            <p>
              <label for="pays_perm">Pays :</label><input type="text" name="pays_perm" id="pays_perm" value="<?php echo $row['pays_perm']; ?>" />
            </p>
            <p>
              <label for="tel_fixe_perm">Tél fixe :</label><input name="tel_fixe_perm" type="text" class="short" id="tel_fixe_perm" value="<?php echo $row['tel_fixe_perm']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>Êtes-vous boursier(e) ?</p>
            <p><input name="boursier" type="radio" id="boursier_0" value="1" <?php if($row['boursier'] =='1'){ echo 'checked="checked"'; }?> /><label for="boursier_0">Oui</label>
               <input name="boursier" type="radio" id="boursier_1" value="0" <?php if($row['boursier'] !='1'){ echo 'checked="checked"'; }?>  /><label for="boursier_1">Non</label></p>
            <p id="boursier_type_p">
              <label for="boursier_type">Type de bourse / échelon :</label><input type="text" name="boursier_type" id="boursier_type" value="<?php if($row['boursier'] =='1'){ echo $row['boursier_type'];} ?>" />
            </p>
            <p>Êtes-vous en situation de handicap ?</p>
            <p><input name="handicap" type="radio" id="handicap_0" value="1" <?php if($row['handicap'] =='1'){ echo 'checked="checked"'; }?> /><label for="handicap_0">Oui</label>
               <input name="handicap" type="radio" id="handicap_1" value="0" <?php if($row['handicap'] !='1'){ echo 'checked="checked"'; }?> /><label for="handicap_1">Non</label></p>
        </fieldset>
        <fieldset id="medaille_p">
            <p>Si vous êtes diplômé(e) en juillet <?php echo date('Y'); ?>, souhaitez-vous recevoir la médaille du diplômé gravée à votre nom ?</p>
            <p><input name="medaille" type="radio" id="medaille_0" value="1" <?php if($row['present'] =='1'){if($row['medaille'] =='1'){ echo 'checked="checked"'; }}?> /><label for="medaille_0">Oui</label>
               <input name="medaille" type="radio" id="medaille_1" value="0" <?php if($row['medaille'] !='1' || $row['present'] !='1'){ echo 'checked="checked"'; }?> /><label for="medaille_1">Non</label></p>
        </fieldset>
        <div id="note">Les champs * sont obligatoires.</div>
        <input name="Valider" type="submit" value="Modifier" id="Modifier"  />
    </form>
    
    <?php } } ?>
    
    <div class="reset"></div>
    
</div>
</body>
</html>

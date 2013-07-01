<?php

//// ID DE LA SESSION A LAQUELLE ON DOIT S'INSCRIRE POUR PARTICIPER A L'EVENEMENT
$IDsessionEvent = 428;

include_once("var/vars.php");
include_once("var/classe_connexion.php");
include_once("php/fonctions.php");
include_once("../inscription/connectLDAP2.php");

session_start();

$erreurLDAP = "";
$erreurSessionComplete = "";
$erreurDejaInscrit = "";
$erreurChamps = "";
$confirmation = "";

$uncomplete=false;

$connect		= new connexion($news_cInfo['server'],$news_cInfo['user'],$news_cInfo['password'],$news_cInfo['db']);

if(!empty($_POST['user_login_LDAP']) && !empty($_POST['user_password_LDAP'])){
	$infosEtudiant = connectLDAP($_POST['user_login_LDAP'],$_POST['user_password_LDAP']);
	
	switch ($infosEtudiant->info){
		case "login error" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
		case "no connexion" : $erreurLDAP="Impossible de se connecter au serveur d'identification pour le moment."; break;
		case "no login" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
		default : $erreurLDAP=""; break;
	}
	if($erreurLDAP==""){
		$_SESSION['nomSP'] = $infosEtudiant->nom;
		$_SESSION['prenomSP'] = $infosEtudiant->prenom;
		$_SESSION['mailSP'] = $infosEtudiant->email;
		$_SESSION['typeSP'] = $infosEtudiant->type;
		$_SESSION['idSP'] = $infosEtudiant->spID;
		$_SESSION['annee'] = $infosEtudiant->annee;
		
		$LDAP = 'ok';
		
	}else{
		$LDAP = 'pb';	
	}
}else if(!empty($_SESSION['nomSP'])){
	$LDAP = 'ok';
}else{
	$LDAP = 'pb';	
}


if($_POST['action'] == 'send_form'){
	
	if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['id_etudiant']) && !empty($_POST['mail_perso']) && !empty($_POST['tel_mobile']) && !empty($_POST['present'])){
		
		
		$insertSQL 		= sprintf("INSERT INTO sp_diplomation (year, id_session, genre, nom, prenom, id_etudiant, nationalite, filiere, double_diplome, mail_sciencespo, mail_perso, adresse, cp, ville, pays, tel_fixe, tel_mobile, adresse_perm, cp_perm, ville_perm, pays_perm, tel_fixe_perm, boursier, boursier_type ,handicap ,present ,medaille ) 
									VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
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
															GetSQLValueString($_POST['medaille'], 'boolean'));
		$insert_query	= mysql_query($insertSQL) or die(mysql_error());
		
		if(GetSQLValueString($_POST['present'], 'boolean') == 1){
			//header('location:../inscription/inscription.php?id='.$IDsessionEvent);
			header('location:valid.php');
			exit();
		}else{
			$validForm = true;
		}
	}else{
		$uncomplete = true;
	}
}
if(!empty($_SESSION['mailSP'])){
	$checkSubscription = "SELECT COUNT(id_etudiant) AS nbr FROM sp_diplomation WHERE mail_sciencespo='".$_SESSION['mailSP']."'";
	$checkSubscription_query	= mysql_query($checkSubscription) or die(mysql_error());
	while ($row = mysql_fetch_assoc($checkSubscription_query)) {
		if($row['nbr'] >0) {
			$dejaInscrit = true;
		}
	}
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></title>

<link rel="stylesheet" href="css/supersized.core.css" type="text/css" media="screen" />
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
	width:800px;
	padding:10px;
	margin:40px 0 20px 50px;
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

.alerte{
	background:#F06;
	color:#FFF;
	padding:5px;
	font-style:italic;
	margin:20px 0;
}

</style>


<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="js/supersized.3.1.3.js"></script>

<script type="text/javascript">  
	$(function($){
		$.supersized({
			//Background image
			slides	:  [ { image : 'img/fond.jpg' } ]					
		});
	});
	
</script>
<script>
	$(document).ready(function(){
	
		<?php if($_POST['boursier'] !='1'){?>
		$('#boursier_type_p').hide();
		<?php } ?>
		
		$("input[type=radio][name=boursier]").click(function() {
			if($('input[type=radio][name=boursier]:checked').attr('value') == 1){
				$('#boursier_type_p').show();
			}else{
				$('#boursier_type_p').hide();
			}
			
		});
		
		
		
	});
</script>

</head>



<body>

 <?php //echo '<p>infos LDAP : login :'.$infosEtudiant->login.', prenom : '.$infosEtudiant->prenom.', nom : '.$infosEtudiant->nom.', email : '.$infosEtudiant->email.', type : '.$infosEtudiant->type.', n° étudiant : '.$infosEtudiant->spID.', année : '.$infosEtudiant->annee.'</p>';?>

<div id="enquete">
	<img src="img/ScPo-logo-Rouge.gif" width="184" height="50" id="logo" alt="Logo Sciences Po" />
    
    <h1>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></h1>    
    
    <?php if(!empty($validForm)){ ?>
    	<p>Merci d'avoir répondu au questionnaire !</p>
        <?php session_destroy(); ?>
    <?php }else if(!empty($dejaInscrit)){ ?>
    	<p>Vous avez déjà rempli le formulaire.</p>
        <?php session_destroy(); ?>    
   	
    <?php }else if($LDAP == 'ok' && $_SESSION['annee']=='05'){ ?>
        
    <form id="form_save" name="form_save" method="post" action="">
    	<input type="hidden" value="send_form" name="action" id="action" />
    	<input type="hidden" value="<?php echo date('Y'); ?>" name="year" id="year" />
    	<input type="hidden" value="428" name="id_session" id="id_session" />
    	<input type="hidden" value="" name="login" id="login" />
    	<input type="hidden" value="" name="password" id="password" />
        <fieldset>
        <?php if($uncomplete){ ?>    
        <div class="alerte">Attention certains champs * obligatoires n'ont pas été remplis.</div>
        <?php } ?>
            <p>Serez-vous présent(e) à la Cérémonie et soirée du Diplôme le vendredi 1er juillet ? *</p>
            <p><input name="present" type="radio" id="present_0" value="1" <?php if($_POST['present'] =='1'){ echo 'checked="checked"'; }?> /><label for="present_0">Oui</label>
               <input name="present" type="radio" id="present_1" value="" <?php if($_POST['present'] =='0'){ echo 'checked="checked"'; }?> /><label for="present_1">Non</label></p>
        </fieldset>
        
        <h2>Renseignements Promotion <?php echo date('Y'); ?></h2>
        
        <p>Dans un souci d'amélioration continue de ses programmes de formation, Sciences Po mène chaque année une enquête d'insertion professionnelle auprès de ses jeunes diplômés.<br />
    De la qualité de cette enquête dépend en partie la valeur qui sera accordée à votre diplôme. Nous vous remercions donc, par avance, de l'attention que vous voudrez bien nous accorder lorsque, dans quelques mois, nous vous contacterons pour connaître votre situation.<br />
    Nous vous remercions de bien vouloir compléter vos coordonnées ci-dessous afin que nous puissions vous joindre le moment venu.</p>
        <p>
          <?php if($uncomplete){ ?>    
  </p>
<div class="alerte">Attention certains champs * obligatoires n'ont pas été remplis.</div>
        <?php } ?>
        <fieldset>
            <p>
              <input name="genre" type="radio" id="genre_0" value="madame" <?php if($_POST['genre'] =='madame'){ echo 'checked="checked"'; }?> /><label for="genre_0">Madame</label>
              <input name="genre" type="radio" id="genre_1" value="mademoiselle" <?php if($_POST['genre'] =='mademoiselle'){ echo 'checked="checked"'; }?> /><label for="genre_1">Mademoiselle</label>
              <input name="genre" type="radio" id="genre_2" value="monsieur" <?php if($_POST['genre'] =='monsieur'){ echo 'checked="checked"'; }?> /><label for="genre_2">Monsieur</label>
            </p>
            <p>
              <label for="nom">Nom* :</label><input type="text" name="nom" id="nom" value="<?php echo $_SESSION['nomSP'];?>" class="obligatory" readonly="readonly" />
            </p>
            <p>
              <label for="prenom">Prénom* :</label><input type="text" name="prenom" id="prenom" value="<?php echo $_SESSION['prenomSP'];?>" class="obligatory" readonly="readonly" />
            </p>
            <p>
              <label for="id_etudiant">Numéro d'étudiant* :</label><input name="id_etudiant" value="<?php echo $_SESSION['idSP'];?>" type="text" class="short obligatory" id="id_etudiant"  readonly="readonly" />
            </p>
            <p>
              <label for="nationalite">Nationalité :</label><input type="text" name="nationalite" id="nationalite" value="<?php echo $_POST['nationalite']; ?>" />
            </p>
            <p>
              <label for="filiere">Master / Filière :</label><input type="text" name="filiere" id="filiere" value="<?php echo $_POST['filiere']; ?>" />
            </p>
            <p>
              <label for="double_diplome">Double diplôme :</label><input type="text" name="double_diplome" id="double_diplome" value="<?php echo $_POST['double_diplome']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>
              <label for="mail_sciencespo">E-mail Sciences Po :</label><input name="mail_sciencespo" type="text" class="" value="<?php echo $_SESSION['mailSP'];?>" id="mail_sciencespo" readonly="readonly" />
            </p>
            <p>
              <label for="mail_perso">E-mail Personnel* :</label><input name="mail_perso" type="text" class="obligatory" id="mail_perso" value="<?php echo $_POST['mail_perso']; ?>"  />
            </p>
            <p>
              <label for="adresse">Adresse :</label><input type="text" name="adresse" id="adresse" value="<?php echo $_POST['adresse']; ?>" />
            </p>
            <p>
              <label for="cp">Code postal :</label><input name="cp" type="text" class="short" id="cp" value="<?php echo $_POST['cp']; ?>" />
            </p>
            <p>
              <label for="ville">Ville :</label><input type="text" name="ville" id="ville" value="<?php echo $_POST['ville']; ?>" />
            </p>
            <p>
              <label for="pays">Pays :</label><input type="text" name="pays" id="pays" value="<?php echo $_POST['pays']; ?>" />
            </p>
            <p>
              <label for="tel_fixe">Tél fixe :</label><input name="tel_fixe" type="text" class="short" id="tel_fixe" value="<?php echo $_POST['tel_fixe']; ?>" />
            </p>
            <p>
              <label for="tel_mobile">Tél mobile* :</label><input name="tel_mobile" type="text" class="short obligatory" id="tel_mobile" value="<?php echo $_POST['tel_mobile']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>
              <label for="adresse_perm">Adresse permanente (parents, etc.) :</label><input type="text" name="adresse_perm" id="adresse_perm" value="<?php echo $_POST['adresse_perm']; ?>" />
            </p>
            <p>
              <label for="cp_perm">Code postal :</label><input name="cp_perm" type="text" class="short" id="cp_perm" value="<?php echo $_POST['cp_perm']; ?>" />
              </p>
            <p>
              <label for="ville_perm">Ville :</label><input type="text" name="ville_perm" id="ville_perm" value="<?php echo $_POST['ville_perm']; ?>" />
            </p>
            <p>
              <label for="pays_perm">Pays :</label><input type="text" name="pays_perm" id="pays_perm" value="<?php echo $_POST['pays_perm']; ?>" />
            </p>
            <p>
              <label for="tel_fixe_perm">Tél fixe :</label><input name="tel_fixe_perm" type="text" class="short" id="tel_fixe_perm" value="<?php echo $_POST['tel_fixe_perm']; ?>" />
            </p>
        </fieldset>
        <fieldset>
            <p>Êtes-vous boursier(e) ?</p>
            <p><input name="boursier" type="radio" id="boursier_0" value="1" <?php if($_POST['boursier'] =='1'){ echo 'checked="checked"'; }?> /><label for="boursier_0">Oui</label>
               <input name="boursier" type="radio" id="boursier_1" value="0" <?php if($_POST['boursier'] !='1'){ echo 'checked="checked"'; }?>  /><label for="boursier_1">Non</label></p>
            <p id="boursier_type_p">
              <label for="boursier_type">Type de bourse / échelon :</label><input type="text" name="boursier_type" id="boursier_type" value="<?php if($_POST['boursier'] =='1'){ echo $_POST['boursier_type'];} ?>" />
            </p>
            <p>Êtes-vous en situation de handicap ?</p>
            <p><input name="handicap" type="radio" id="handicap_0" value="1" <?php if($_POST['handicap'] =='1'){ echo 'checked="checked"'; }?> /><label for="handicap_0">Oui</label>
               <input name="handicap" type="radio" id="handicap_1" value="0" <?php if($_POST['handicap'] !='1'){ echo 'checked="checked"'; }?> /><label for="handicap_1">Non</label></p>
        </fieldset>
        <fieldset id="medaille_p">
            <p>Les demandes de médailles effectuées après le 23 juin ne permettront pas de récupérer celles-ci le 1er juillet prochain.<br />
Vous pourrez venir la retirer courant septembre auprès du Pôle central de Sciences Po.</p>
            <p>Si vous êtes diplômé(e) en juillet <?php echo date('Y'); ?>, souhaitez-vous recevoir la médaille du diplômé gravée à votre nom ?</p>
           <p><input name="medaille" type="radio" id="medaille_0" value="1" <?php if($_POST['present'] =='1'){if($_POST['medaille'] =='1'){ echo 'checked="checked"'; }}?> /><label for="medaille_0">Oui</label>
               <input name="medaille" type="radio" id="medaille_1" value="0" <?php if($_POST['medaille'] !='1' || $_POST['present'] !='1'){ echo 'checked="checked"'; }?> /><label for="medaille_1">Non</label></p>
        </fieldset>
        <div id="note">Les champs * sont obligatoires.</div>
        <input name="Valider" type="submit" value="valider" id="Valider"  />
    </form>
    <div class="reset"></div>
    <?php }else { ?>
    <form id="form_login" name="form_login" method="post" action="">
    	<fieldset>
            <p>
              <label for="user_login_LDAP">Identifiant :</label><input type="text" name="user_login_LDAP" id="user_login_LDAP" />
            </p>
          <p>
            <label for="user_password_LDAP">Mot de passe :</label><input type="password" name="user_password_LDAP" id="user_password_LDAP" />
            </p>
        </fieldset>
        
        <input name="Valider" type="submit" value="valider" id="Valider"  />
    </form>
    
    <div class="reset"></div>
    
		<?php if(!empty($_POST['user_login_LDAP']) && ( $LDAP == 'pb' || $_SESSION['annee']!='05')){ ?>
        <p>Vous avez fait une erreur d'identifiant/mot de passe ou vous n'êtes pas en 5e année.</p>
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>

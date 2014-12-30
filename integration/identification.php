<?php   
// connection to data base
include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');// functions library
include_once('connectLDAP.php');

session_start();

$erreurLDAP = "";
$erreurChamps = "";

if(isset($_POST['user_login_LDAP']) && isset($_POST['user_password_LDAP']) && $_POST['user_login_LDAP']!="" && $_POST['user_password_LDAP']!=""){
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
    }
}

if(isset($_POST['user_login_LDAP']) && ($_POST['user_login_LDAP']=="" || $_POST['user_password_LDAP']=="")){
    $erreurChamps = "Tous les champs sont obligatoires.";
}

if(isset($_SESSION['nomSP'])){
    header( "Location:soumettre.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Sciences Po / Evénements</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <link href="admin/jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="admin/tools.js"></script>
    <script type="text/javascript" src="admin/jquery-ui/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="admin/jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
</head>

<body class="iframe">
    <div id="cartouche" style="display: block;">
        <h2 class="little_bigger">Proposer un événement</h2>
        <div class="formulaire_interne">
            <h3>Merci d'entrer votre identifiant Sciences Po</h3>
            <form name="formSciencesPo" id="formSciencesPo" action="#" method="post">
                <p class="bit_small"><label for="user_login_LDAP">Identifiant* :</label><input type="text" id="user_login_LDAP" name="user_login_LDAP" /></p>
                <p class="bit_small"><label for="user_password_LDAP">Mot de passe* :</label><input type="password" id="user_password_LDAP" name="user_password_LDAP" /></p>
                
                <p class="erreur bit_small">* Champs obligatoires</p>
        <?php
                if($erreurLDAP!=""){
        ?>
                    <p class="erreur bit_small"><?php echo $erreurLDAP;?></p>
        <?php
                }
                if($erreurChamps!=""){
        ?>
                    <p class="erreur bit_small"><?php echo $erreurChamps;?></p>
        <?php
                }
        ?>
                <input type="submit" value="Valider" id="envoyer"/>
            </form>
        </div>
        <div class="mentions small">
            <p>Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d’un droit d’accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l’exercer, adressez-vous à Sciences Po Pôle Evénements  - 27 rue Saint Guillaume - 75007 Paris</p>
        </div>
    </div>
</body>
</html>

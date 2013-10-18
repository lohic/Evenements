<?php
header('Content-type: text/html; charset=UTF-8');

//include_once('../../vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_session.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

$session = new session();

echo $session->make_inscription_externe($_GET['id_session'], $_GET['nom'], $_GET['prenom'], $_GET['mail'], $_GET['entreprise'], $_GET['fonction'], $_GET['casque'], $_GET['titre'], $_GET['date'], $_GET['lieu']);

?>
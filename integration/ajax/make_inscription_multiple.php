<?php
header('Content-type: text/html; charset=UTF-8');

include_once('../../vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_session.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

$evenement = new evenement();

echo $evenement->make_inscription_multiple($_GET['sessions'], $_GET['casques'], $_GET['id_evenement'], $_GET['login'], $_GET['password'], $_GET['titre'], $_GET['date']);

?>
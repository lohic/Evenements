<?php
header('Content-type: text/html; charset=UTF-8');

//include_once('../../vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

$event = new evenement();

echo $event->get_event_infos_inscription_multiple_externe($_GET['id_event'], $_GET['code']);

?>
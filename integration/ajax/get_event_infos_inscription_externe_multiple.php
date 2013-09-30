<?php
header('Content-type: text/html; charset=UTF-8');

include_once('../../vars/config.php');
include_once('../../vars/statics_vars.php');
include_once('../../classe/classe_evenement.php');
include_once('../../classe/classe_fonctions.php');

$event = new evenement();

echo $event->get_event_infos_inscription_externe_multiple($_GET['id_event'], $_GET['code']);

?>
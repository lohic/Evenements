<?php
header('Content-type: text/html; charset=UTF-8');

$json = new stdClass();

$session = array();


$json->nom = "le nom";

for($i=0; $i<4; $i++){
	$temp = new stdClass();
	$temp->val = "ok $i";

	$session[] = $temp;
}

$json->session = $session;

$json->super = "valeur super";


echo json_encode($json);
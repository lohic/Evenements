<?php

// FICHIER INDEX pour événement
// sa fonction est d'aller chercher les éléments dans le bon dossier de template en fonction du .HTACCESS
// 
// 

define('REAL_LOCAL_PATH', dirname(__FILE__));
define('ABSOLUTE_URL', 'http://localhost:8888/Site_SCIENCESPO_EVENEMENTS');

$template = 'default' ; 

if( ! empty( $_GET['organisme'] )){
	$template = $_GET['organisme'];

	echo 'front office ORGANISME : ' . $_GET['organisme'] . '<br/>';
} else {
	echo 'default'. '<br/>';
}


$template_url = ABSOLUTE_URL.'/template_front/' . $template;
$template_local_path = REAL_LOCAL_PATH.'/template_front/' . $template;

echo $template_url  .' '.$template_local_path ;


ob_start();

include($template_local_path.'/index.php');

$contents = ob_get_contents();

ob_end_clean();

echo $contents;


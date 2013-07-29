<?php

// FICHIER INDEX pour événement
// sa fonction est d'aller chercher les éléments dans le bon dossier de template en fonction du .HTACCESS
// 
// 


// a récupérer dans vars/config.php
define('REAL_LOCAL_PATH', dirname(__FILE__));
define('ABSOLUTE_URL', 'http://localhost:8888/Site_SCIENCESPO_EVENEMENTS');


// cette mecanique devra être placée dans une classe
$template = 'default' ;

// on verifie qu'un ORGANISME a bien été envoyé par le HTACCESS
if( ! empty( $_GET['organisme'] )){

	// on verifie que l'ORGANISME correspond bien à un dossier
	if ( is_dir ( REAL_LOCAL_PATH.'/template_front/' . $_GET['organisme'] ) ){

		$template = $_GET['organisme'];

	}
}


// on créé les variables locales et absolues pour le chemin du template
$template_url = ABSOLUTE_URL.'/template_front/' . $template;
$template_local_path = REAL_LOCAL_PATH.'/template_front/' . $template;


echo '<strong>$template_url :</strong><br/>' . $template_url  .' <br/><strong>$template_local_path :</strong><br/>'.$template_local_path ;


ob_start();

include($template_local_path.'/index.php');

$contents = ob_get_contents();

ob_end_clean();

echo $contents;


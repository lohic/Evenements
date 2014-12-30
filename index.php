<?php

// FICHIER INDEX pour événement
// sa fonction est d'aller chercher les éléments dans le bon dossier de template en fonction du .HTACCESS
// 
// 

include_once('vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_front_office.php');

// a récupérer dans vars/config.php
// define('REAL_LOCAL_PATH', dirname(__FILE__));
// define('ABSOLUTE_URL', 'http://localhost:8888/Site_SCIENCESPO_EVENEMENTS');


$front = new frontoffice();

<?php

// CONFIG SAMPLE

define ('MAINTENANCE',			false);

/**
* variables de connexion
*/
$connexion_info['server'] 		= 'localhost';
$connexion_info['user'] 		= 'root';
$connexion_info['password'] 	= 'z6po';
$connexion_info['db']		 	= 'sciences_po_plasma_db';


/**
* variables du système de fichier
*/
define ('SUB_FOLDER',			'Site_SCIENCESPO_PLASMA/');
define ('ABSOLUTE_URL',			'http://localhost:8888/'.SUB_FOLDER);
define ('LOCAL_PATH',			getcwd().'/../');
define ('REAL_LOCAL_PATH',		dirname(__FILE__).'/../');

define ('IS_LDAP_SERVER',		false);
define ('IS_MAIL_LOGIN',		false);

define ('SLIDE_TEMPLATE_FOLDER','slides_templates/');
define ('ACTU_MEDIA_FOLDER',	'actu_medias/');
define ('SLIDESHOW_FOLDER',		'slideshow/');
define ('IMG_SLIDES',			'slides_images/');
define ('BILLETS_FOLDER',		'billet/');

define ('TB',					'sp_');

define ('EVENEMENT_DATA_URL',	'http://www.sciencespo.fr/evenements/api/');

define ('METEO_DATA_URL',		'vars/meteo_json.txt');

define("CHEMIN_DOCUMENTS", "http://www.sciencespo.fr/evenements/admin/upload/medias/");
define("CHEMIN_IMAGES", "http://www.sciencespo.fr/evenements/admin/upload/photos/");
define("ABSOLU_IMAGES", "http://www.sciencespo.fr/evenements/admin/upload/photos/");
define("CHEMIN_GENERAL", "http://www.sciencespo.fr/evenements/"); 
define("CHEMIN_TRIANGLES", "http://www.sciencespo.fr/evenements/admin/");
define("CHEMIN_BACK", "http://www.sciencespo.fr/evenements/admin/");

define("CHEMIN_FRONT_OFFICE", "http://www.sciencespo.fr/evenements/");
define("CHEMIN_INSCRIPTION", "http://www.sciencespo.fr/evenements/inscription/");
define("CHEMIN_ICONES", "http://www.sciencespo.fr/evenements/images/");
define("CHEMIN_BANNIERE", "http://www.sciencespo.fr/evenements/admin/upload/banniere/");
define("CHEMIN_LOGO", "http://www.sciencespo.fr/evenements/admin/upload/logo/"); 

define("CHEMIN_BANNIERE_DEFAUT", "http://www.sciencespo.fr/evenements/admin/upload/banniere/"); 
define("CHEMIN_LOGO_DEFAUT", "http://www.sciencespo.fr/evenements/admin/upload/logo/");

define("CHEMIN_UPLOAD", "admin/upload/photos/");

define("PREFIXE_TABLES", "sp_evenements_");
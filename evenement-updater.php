<?php

// connection to data base
include('connect.php');

// functions library
include('functions.php');

  
$connexion_info['server'] 		= 'localhost';
$connexion_info['user'] 		= 'root';
$connexion_info['password'] 	= 'root';
$connexion_info['db']		 	= 'evenements';


$connect = mysql_connect($connexion_info['server'], $connexion_info['user'], $connexion_info['password']); 
mysql_select_db($connexion_info['db'],$connect);

/*
$sql_query = "ALTER TABLE `sp_evenements` ADD `evenement_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `evenement_date` ";
$result = mysql_query($sql_query);

$sql_query = "ALTER TABLE `sp_inscrits` ADD `inscrit_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `inscrit_date` ";
$result = mysql_query($sql_query);

$sql_query = "ALTER TABLE `sp_sessions` ADD `session_debut_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `session_debut` ";
$result = mysql_query($sql_query);

$sql_query = "ALTER TABLE `sp_sessions` ADD `session_fin_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `session_fin` ";
$result = mysql_query($sql_query);

$updateSQL 		= sprintf("UPDATE sp_evenements SET evenement_datetime = FROM_UNIXTIME(evenement_date)");
$update_query	= mysql_query($updateSQL) or die(mysql_error());

$updateSQL 		= sprintf("UPDATE sp_inscrits SET inscrit_datetime = FROM_UNIXTIME(inscrit_date)");
$update_query	= mysql_query($updateSQL) or die(mysql_error());

$updateSQL 		= sprintf("UPDATE sp_sessions SET session_debut_datetime = FROM_UNIXTIME(session_debut), session_fin_datetime = FROM_UNIXTIME(session_fin)");
$update_query	= mysql_query($updateSQL) or die(mysql_error());
*/






$SQLqueries = array();

$SQLqueries[] = sprintf("ALTER TABLE `sp_organismes` ADD `organisme_shortcode` VARCHAR(20) NOT NULL DEFAULT 'dircom' AFTER `organisme_id` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_organismes` ADD `organisme_banniere_facebook_chemin` VARCHAR(255) NOT NULL AFTER `organisme_url_front` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_organismes` ADD `organisme_footer_facebook_chemin` VARCHAR(255) NOT NULL AFTER `organisme_banniere_facebook_chemin` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_organismes` ADD `organisme_image_billet` VARCHAR(255) NOT NULL AFTER `organisme_footer_facebook_chemin` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_organismes` ADD `organisme_url_image` VARCHAR(255) NOT NULL AFTER `organisme_image_billet` ");

$SQLqueries[] = sprintf("ALTER TABLE `sp_evenements` ADD `evenement_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `evenement_date` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_inscrits`   ADD `inscrit_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `inscrit_date` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_sessions`   ADD `session_debut_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `session_debut` ");
$SQLqueries[] = sprintf("ALTER TABLE `sp_sessions`   ADD `session_fin_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `session_fin` ");

$SQLqueries[] = sprintf("UPDATE `sp_evenements` SET `evenement_datetime` = FROM_UNIXTIME(evenement_date)");
$SQLqueries[] = sprintf("UPDATE `sp_inscrits`   SET `inscrit_datetime` = FROM_UNIXTIME(inscrit_date)");
$SQLqueries[] = sprintf("UPDATE `sp_sessions`   SET `session_debut_datetime` = FROM_UNIXTIME(session_debut), `session_fin_datetime` = FROM_UNIXTIME(session_fin)");


$SQLqueries[] = sprintf("CREATE TABLE IF NOT EXISTS `sp_keywords` (
						  `keyword_id` int(11) NOT NULL AUTO_INCREMENT,
						  `keyword_nom` varchar(255) NOT NULL,
						  `keyword_organisme_id` int(11) NOT NULL,
						  `keyword_last_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
						  `keyword_editeur_id` int(11) NOT NULL,
						  `keyword_editeur_ip` varchar(50) NOT NULL,
						  PRIMARY KEY (`keyword_id`)
						) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;");

$SQLqueries[] = sprintf("CREATE TABLE IF NOT EXISTS `sp_rel_batiment_organisme` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `batiment_id` int(11) NOT NULL,
						  `organisme_id` int(11) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=latin1;");


$SQLqueries[] = sprintf("CREATE TABLE IF NOT EXISTS `sp_rel_evenement_keyword` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `evenement_id` int(11) NOT NULL,
						  `keyword_id` int(11) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;");


$SQLqueries[] = sprintf("CREATE TABLE IF NOT EXISTS `sp_rel_lieu_organisme` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `lieu_id` int(11) NOT NULL,
						  `organisme_id` int(11) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;");


foreach ($SQLqueries as $key => $SQLquery) {
	$query	= mysql_query($SQLquery) or die(mysql_error());
}


// POUR RELIER TOUS LES BARTIMENTS AUX ORGANISMES
$sqlcodes ="SELECT code_batiment_id FROM sp_codes_batiments ORDER BY code_batiment_id ASC";
$rescodes = mysql_query($sqlcodes) or die(mysql_error());
while($rowcode = mysql_fetch_array($rescodes)){
	$sqlorganismes ="SELECT organisme_id FROM sp_organismes ORDER BY organisme_id ASC";
	$resorganismes = mysql_query($sqlorganismes) or die(mysql_error());
	while($roworganisme = mysql_fetch_array($resorganismes)){
		$sql ="INSERT INTO sp_rel_batiment_organisme VALUES('','".$rowcode["code_batiment_id"]."','".$roworganisme["organisme_id"]."')";
		mysql_query($sql) or die(mysql_error());
	}
}

// POUR RELIER TOUS LES LIEUX AUX ORGANISMES
$sqllieux ="SELECT lieu_id FROM sp_lieux ORDER BY lieu_id ASC";
$reslieux = mysql_query($sqllieux) or die(mysql_error());
while($rowlieu = mysql_fetch_array($reslieux)){
	$sqlorganismes ="SELECT organisme_id FROM sp_organismes ORDER BY organisme_id ASC";
	$resorganismes = mysql_query($sqlorganismes) or die(mysql_error());
	while($roworganisme = mysql_fetch_array($resorganismes)){
		$sql ="INSERT INTO sp_rel_lieu_organisme VALUES('','".$rowlieu["lieu_id"]."','".$roworganisme["organisme_id"]."')";
		mysql_query($sql) or die(mysql_error());
	}
}


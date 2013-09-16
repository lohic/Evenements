<?php
$connexion_info['server'] 		= 'localhost';
$connexion_info['user'] 		= 'root';
$connexion_info['password'] 	= 'root';
$connexion_info['db']		 	= 'evenements';


$connect = mysql_connect($connexion_info['server'], $connexion_info['user'], $connexion_info['password']); 
mysql_select_db($connexion_info['db'],$connect);


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

?>
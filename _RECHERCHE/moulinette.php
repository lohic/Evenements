<?php

// connection to data base
include('connect.php');

// functions library
include('functions.php');

session_start();

$sql_query = "SELECT MAX(inscrit_id) FROM sp_inscrits";
$result = mysql_query($sql_query);
$row = mysql_fetch_array($result);

$demarrage = $row[0]+1;

$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_id=0 ORDER BY inscrit_date";
$result = mysql_query($sql_query);
	
echo "Plus grand ID avant la moulinette : ".$row[0]."<br/>";

while ($row = mysql_fetch_array($result)) {
	$sql=sprintf("UPDATE sp_inscrits SET inscrit_id=%s WHERE inscrit_date=%s",
						GetSQLValueString($demarrage, "int"),
						GetSQLValueString($row['inscrit_date'], "int"));;
	mysql_query($sql) or die(mysql_error());
	$demarrage++;
}

$fin = $demarrage-1;

$sql_query = "ALTER TABLE `sp_inscrits` ADD PRIMARY KEY(`inscrit_id`)";
$result = mysql_query($sql_query);


$sql_query = "ALTER TABLE `sp_inscrits` CHANGE `inscrit_id` `inscrit_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
$result = mysql_query($sql_query);

echo "Plus grand ID après la moulinette : ".$fin."<br/>";

?>

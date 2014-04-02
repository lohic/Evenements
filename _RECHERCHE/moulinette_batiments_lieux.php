<?php

// connection to data base
include('connect.php');

// functions library
include('functions.php');

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
?>

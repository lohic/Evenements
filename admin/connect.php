<?php

// configuration /////////////////////////////////////
<<<<<<< HEAD
/*$host = 'mysqlserver01.sciences-po.fr';
$user = 'evenements';
$password = 'pF98dQ#';
$database = 'evenements';*/

$host = 'localhost';
$user = 'root';
$password = 'root';
$database = 'evenements';
=======
$host = 'localhost';
$user = 'root';
$password = 'z6po';
$database = 'sciences_po_evenements_new_db';


>>>>>>> api

// make the connection //////////////////////////////
$link = mysql_connect($host, $user, $password) or die("ERROR: ".mysql_error());
mysql_select_db($database, $link);
?>
<?php

// configuration /////////////////////////////////////
$host = 'localhost';
$user = 'root';
$password = 'z6po';
$database = 'sciences_po_evenements_new2_db';

// make the connection //////////////////////////////
$link = mysql_connect($host, $user, $password) or die("ERROR: ".mysql_error());
mysql_select_db($database, $link);
?>
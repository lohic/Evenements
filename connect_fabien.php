<?php

// configuration /////////////////////////////////////
$host = 'localhost';
$user = 'root';
$password = 'root';
$database = 'evenements';

// make the connection //////////////////////////////
$link = mysql_connect($host, $user, $password) or die("ERROR: ".mysql_error());
mysql_select_db($database, $link);
?>
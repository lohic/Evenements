<?php
header('Content-type: text/html; charset=UTF-8');

include_once('../../vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_spuser_event.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

$user = new spuser();

echo $user->test_LDAP($_GET['login'], $_GET['password']);

?>
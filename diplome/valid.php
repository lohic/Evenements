<?php
session_start();
if($_SESSION['annee'] != '05'){
	session_destroy();
	header('location:index.php');
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></title>

<link rel="stylesheet" href="css/supersized.core.css" type="text/css" media="screen" />
<?php 


?>

<style>
body{
	margin:0;
	font-family:Arial, Helvetica, sans-serif;
	background:#000;
	font-size:14px;
	color:#F06;
	height:100%;
	width:100%;
}

#incription{
	width:100%;
	height:100%;
	margin:0;
	padding:0;
	border:0;
}


</style>



</head>


<body>

<?php if($_SESSION['annee'] == '05'){ ?>
<iframe id="incription" src="http://www.sciencespo.fr/evenements/inscription/inscription.php?id=428" height="100%" width="100%"></iframe>
<?php } ?>
</body>
</html>
<?php

//// ID DE LA SESSION A LAQUELLE ON DOIT S'INSCRIRE POUR PARTICIPER A L'EVENEMENT
$IDsessionEvent = 428;

include_once("var/vars.php");
include_once("var/classe_connexion.php");
include_once("php/fonctions.php");
include_once("../inscription/connectLDAP2.php");


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></title>

<link rel="stylesheet" href="css/supersized.core.css" type="text/css" media="screen" />
<?php 

///// CIBLE
///// inscription/inscription.php?id=428

?>

<style>
body{
	margin:0;
	font-family:Arial, Helvetica, sans-serif;
	background:#E3DCC7;
	font-size:14px;
	color:#F06;
}

#enquete{
	position:absolute;
	width:800px;
	padding:10px;
	margin:40px 0 20px 50px;
}

#logo{
	margin-bottom:20px;
}

label{
	width:220px;
	display:inline-block;
	text-transform:uppercase;
	font-size:14px;
	font-weight:bold;
}

label.large{
	width:620px;
}

input{
	display:inline-block;
	width:400px;
	background:none;
	color:#333;
	border:solid 1px #F06;
	padding:2px;
	font-size:14px;
}

input.short{
	display:inline-block;
	width:200px;
}

input[type=radio],input[type=checkbox]{
	display:inline-block;
	width:20px;
}

input[type=submit]{
	display:inline-block;
	width:auto;
	color:#F6F3E9;
	background:#F06;
	text-transform:uppercase;
	float:right;
	cursor:pointer;
	padding:4px;
}

input[readonly] {
   border:solid 1px #333;
}

fieldset{
	border:0;
	margin:10px 0;
	padding:5px 0px;
}

p{
	padding:0;
	margin:5px 0;
	font-size:16px;
	font-style:italic;
}

.reset{
	clear:both;
}

#note{
	font-size:12px;
	font-style:italic;
	width:400px;
	float:left;
}

.alerte{
	background:#F06;
	color:#FFF;
	padding:5px;
	font-style:italic;
	margin:20px 0;
}

</style>


<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="js/supersized.3.1.3.js"></script>

<script type="text/javascript">  
	$(function($){
		$.supersized({
			//Background image
			slides	:  [ { image : 'img/fond.jpg' } ]					
		});
	});
	
</script>
<script>
	$(document).ready(function(){
	

		
	});
</script>

</head>



<body>

<div id="enquete">
	<img src="img/ScPo-logo-Rouge.gif" width="184" height="50" id="logo" alt="Logo Sciences Po" />
    
    <h1>Cérémonie et soirée du Diplôme <?php echo date('Y'); ?></h1>    
    
    <p>Les incrisptions sont closes.</p>  
    
    <div class="reset"></div>
    
</div>
</body>
</html>
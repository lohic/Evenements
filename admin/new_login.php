<?php
// security
include('cookie.php');

// connection to data base
include('connect.php');


// if new album 
if( isset($_POST['user_login']) ){

		$sql ="INSERT INTO sp_users
			VALUES(
				'',
				'".$_POST["user_login"]."',
				'".$_POST["user_password"]."',
				'".$_POST["user_email"]."',
				'',
				'',
				'".$_POST["user_alerte"]."'
				)	";
		mysql_query($sql) or die(mysql_error());
		
		// reedirect
		header("Location:logins.php");
}// if



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>CMS</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="page">
	<?php include("top.php"); ?>
    <div id="menu">
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
      	<p>Création d'un <strong>nouveau login</strong>. Remplissez les données ci-dessous et appuyez sur 'Enregistrer'</p>
      	<form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			<p><label for="user_login">Login :</label><input name="user_login" type="text" class="inputFieldShort" id="user_login" value="<?php echo $row['user_login'] ?>"/></p>
			<p><label for="user_password">Mot de passe :</label><input name="user_password" type="text" class="inputFieldShort" id="user_password" value="<?php echo $row['user_password'] ?>"/></p>
			<p><label for="user_email">Email :</label><input name="user_email" type="text" class="inputFieldShort" id="user_email" value="<?php echo $row['user_email'] ?>"/></p>
			<p><label for="user_alerte">Alerte mail :</label><input name="user_alerte" type="checkbox" id="user_alerte" value="1"/></p>
			<input type="submit" name="button" id="button" value="Save" class="button" />
		</form>
	</div>
</div>
</body>
</html>

<?php
// connection to data base
/*include('connect.php');

session_start();

// user query
if( $_POST['login'] != ''  ){

	$sql = "SELECT * FROM sp_users
			WHERE user_login = '". $_POST['login'] ."'
			LIMIT 1";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	
	// validate
	if($_POST['login'] == $row['user_login'] && $_POST['password'] == $row['user_password']){
		setcookie('CMSCookie', '1' , time()+6*3600);
		setcookie('admin', $row['user_id'] , time()+6*3600);
		
		if( $row['user_admin'] == 1 ) {
			$_SESSION['admin'] = 1;
		}

		header('Location:list.php?menu_actif=evenements');
	}else{
		header('Location:index.php?error=2.');
	}
	
}
*/   
session_start();
// Error message
if( isset($_GET['error']) ){
		switch($_GET['error']){
	  		case '1':
				// destroy cookie
				setcookie('CMSCookie', '0' , time()+6*3600);
				$error='Votre session a expiré.';
				session_unset();
			break;
				case '2':
				$error='Mauvais login/mot de passe!';
			break;
		}
}

include_once('../vars/constantes_vars.php');
include_once('../vars/statics_vars.php');

include_once('../classe/classe_core_event.php');
include_once('../classe/fonctions.php');
$core = new core(); 

if(!$core->isAdmin || isset($error)){ 
 
}
else{
	$sql = "SELECT * FROM sp_users
			WHERE user_login = '". $_POST['login'] ."'
			LIMIT 1";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	
	setcookie('CMSCookie', '1' , 0);
	setcookie('admin', $row['user_id'] , 0);
	
	if( $row['user_admin'] == 1 ) {
		$_SESSION['admin'] = 1;
	} 

	// sinon redirection vers la liste des événements 
	header('Location:list.php?menu_actif=evenements');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sciences Po | Événements : administration</title>
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<link href="css/couleur.css" rel="stylesheet" type="text/css" />

</head>

<body>	
<div id="page">
	<?php 
	if(!$core->isAdmin || isset($error)){ 
	    include("top_sans.php"); 
    ?>
	    <div id="content_login">
			<form action="index.php" method="post">
			<?php
		  		// Print error
		  		if( isset($_POST['login']) ){
		  			echo '<p>Vous n\'avez pas accès à cette page
					ou vous vous êtes trompé d\'identifiant/mot de passe !</p>';
				}
			?>
				<p><label for="login">IDENTIFIANT :</label><input name="login" type="text" id="login" /></p>
				<p><label for="password">MOT DE PASSE :</label><input name="password" type="password" id="password" /></p>
				<p><input type="submit" name="Submit" value="> SE CONNECTER" class="button" /></p>
			</form>
	     </div>                                                                                                              
	<?php
    }
	?>   
</div>  
</body>
</html>

<?php
include_once('../vars/config.php');
// security
include('cookie.php');

// connection to data base
include('connect.php');

// if editing...
if( isset($_POST['rubrique_id']) ){

		// query
		$sql ="UPDATE sp_rubriques SET
					rubrique_titre = '".$_POST["rubrique_titre"]."'
				WHERE rubrique_id = '".$_POST['rubrique_id']."'";
		mysql_query($sql) or die(mysql_error());

		// reedirect
		header("Location:rubriques.php?r=".rand());
}else{
	// query of this album
	$sql ="SELECT * FROM sp_rubriques WHERE rubrique_id = '".$_GET['id']."'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
}

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
		<p>Edition d'une rubrique</p><br/>
		<form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			<p><label for="rubrique_titre">Titre :</label><input name="rubrique_titre" type="text" class="inputFieldShort" id="rubrique_titre" value="<?php echo $row['rubrique_titre'] ?>"/></p>
			<input type="submit" name="button" id="button" value="Save" class="button" />
			<input name="rubrique_id" type="hidden" id="rubrique_id" value="<?php echo $row['rubrique_id']?>" />
		</form>
	</div>
</div>
</body>
</html>

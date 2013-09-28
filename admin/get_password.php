<?php
//connect
include('connect.php');

//retrive password
if( isset($_POST['email']) ){
	
	//query
	$sql = "SELECT * FROM sp_users
			WHERE user_email = '".$_POST['email']."'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	
	
	//Headers
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers .= "From: <RETRIEVE PASSWORD>" ;
	
	// message
	$msg = 'Here is your lost password:
			<br>
			<br>
			LOGIN: '.$row['user_login'].'
			<br>
			PASSWORD:'.$row['user_password'].'<br>
';
	// send mail
	mail($row['email'], 'Password retrieve', $msg, $headers);
	
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
<table width="781" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <div class="white">ENTER YOUR LOGIN INFO:    </div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">
    
    
    
     <div class="content">
      
      <form action="get_password.php" method="post">
        <table width="489" border="0" >
          <tr>
            <td colspan="2">
			
			<?php
	  //mandamos mensajes de error
	  if( isset($_POST['user_email']) ){
	  		echo '<span class="error">Your login and password has been sent to your email account.</span>';
	  }
	  ?>
            </td>
          </tr>
          <tr>
            <td width="142">Email :</td>
            <td width="337"><input name="email" type="text" id="email" />
            </td>
          </tr>
          <tr>
            <td colspan="2"><input type="submit" name="Submit" value="retrieve password" /></td>
          </tr>
          <tr>
            <td colspan="2"><p>&nbsp;</p>
                <p><a href="index.php">Login</a></p></td>
          </tr>
        </table>
      </form>
       
       
       
       
     </div>     </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>

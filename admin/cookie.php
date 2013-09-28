<?php
// Security ...
if($_COOKIE['CMSCookie'] != '1'){
   header('Location:index.php?error=1');
}
?>
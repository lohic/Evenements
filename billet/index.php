<?php

include('../vars/config.php');
include(REAL_LOCAL_PATH.'classe/classe_billet.php');


$billet = new billet('3220110088396');

// echo $billet->HTMLticket;
// echo $billet->passbookFile;
 echo $billet->PDFurl;


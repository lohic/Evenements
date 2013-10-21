<?php

include('../vars/config.php');
include(REAL_LOCAL_PATH.'classe/classe_billet.php');

$billet = new billet('3220110088396','#cb021a','nom de la session','21/10/2013','12:00', 'FR', 'nom', 'prenom', 'interne', 'retransmission', false, 'boutmy', 'dir com', '', 'http://www.test.com');
echo $billet->HTMLticket;
//echo $billet->passbookFile;
//echo $billet->PDFurl;


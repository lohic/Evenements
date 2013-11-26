<?php

include('../vars/config.php');
include(REAL_LOCAL_PATH.'classe/classe_billet.php');



$billet = new billet('3220110088396',
					'#cb021a',
					'nom de la session',
					'01/10/2013',
					'12:23:56',
					'FR',
					'nom',
					'prénom',
					'retransmission',
					'amphithéâtre',
					false,
					'boutmy',
					'dir com',
					'defaut.png',
					'http://www.test.com');

$billet->HTMLticket;
$billet->PDFurl;
$billet->passbookFile;

//file_put_contents('billet.pkpass',$billet->passbookFile);
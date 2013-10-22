<?php

include('../vars/config.php');
include(REAL_LOCAL_PATH.'classe/classe_billet.php');



$billet = new billet('3220110088396',
					'#cb021a',
					'nom de la session',
					'2013/10/01',
					'12:23:56',
					'FR',
					'nom',
					'prénom',
					'retransmission',
					'amphithéâtre',
					false,
					'boutmy',
					'dir com',
					'',
					'http://www.test.com');
echo $billet->PDFurl;
echo $billet->HTMLticket;
echo $billet->passbookFile;

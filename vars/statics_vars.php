<?php 

//include_once("constantes_vars.php");
$langues_evenement = array( "Français"	=> "33",
							"Anglais"	=> "44",
							"Chinois"	=> "86",
							"Allemand"	=> "49",
							"Danois"	=> "45",
							"Espagnol"	=> "34",
							"Italien"	=> "39",
							"Japonais"	=> "83",
							"Polonais"	=> "48",
							"Russe"		=> "7",
							"Tchèque"	=> "420");

$batiments = array(	"A"			=> "A => 27, rue Saint Guillaume 75006 - Paris",
					"B"			=> "B => 56, rue des Saint-Pères 75006 - Paris",
					"C"			=> "C => 9, rue de la Chaise 75006 - Paris",
					"D"			=> "D => 199, bd Saint-Germain 75006 - Paris",
					"J"			=> "J => 13, rue de l'Université 75006 - Paris",
					"K"			=> "K => 117, bd Saint-Germain 75006 - Paris",
					"H"			=> "H => 28, rue des Saints-Pères 75006 - Paris",
					"S"			=> "S => 56, rue Jacob 75006 - Paris",
					"CEVIPOF"	=> "CEVIDOF => 98, rue de l'Université 75006 - Paris",
					"Bibliothèque et Librairie"=>"Bibliothèque et Librairie => 30, rue Saint Guillaume 75006 - Paris");

$salles = array(	"Amphithéâtre Emile Boutmy",
					"Amphithéâtre Jacques Chapsal",
					"Salle Leroy-Beaulieu",
					"Salle Albert Sorel",
					"Amphithéâtre Leroy-Beaulieu-Sorel",
					"Salle François Goguel",
					"Amphithéâtre Jean Moulin",
					"Amphithéâtre Albert Caquot",
					"Amphithéâtre Erignac");

$code_langues_evenement = array();
$code_langues_evenement ['33']  = "FR";
$code_langues_evenement ['44']  = "EN";
$code_langues_evenement ['86']  = "ZH";
$code_langues_evenement ['49']  = "DE";
$code_langues_evenement ['45']  = "DA";
$code_langues_evenement ['34']  = "ES";
$code_langues_evenement ['39']  = "IT";
$code_langues_evenement ['83']  = "JA";
$code_langues_evenement ['48']  = "PL";
$code_langues_evenement ['7']   = "RU";
$code_langues_evenement ['420'] = "CS";

date_default_timezone_set('UTC');


$langEvenement = array();
$langEvenement[0]	= 'français';
$langEvenement[1]	= 'anglais';
$langEvenement[2]	= 'chinois';
$langEvenement[3]	= 'allemand';
$langEvenement[4]	= 'danois';
$langEvenement[5]	= 'espagnol';
$langEvenement[6]	= 'italien';
$langEvenement[7]	= 'japonais';
$langEvenement[8]	= 'polonais';
$langEvenement[9]	= 'russe';
$langEvenement[10]	= 'tchèque';

$moisListe = array();
$moisListe['01']	= 'janvier';
$moisListe['02']	= 'février';
$moisListe['03']	= 'mars';
$moisListe['04']	= 'avril';
$moisListe['05']	= 'mai';
$moisListe['06']	= 'juin';
$moisListe['07']	= 'juillet';
$moisListe['08']	= 'août';
$moisListe['09']	= 'septembre';
$moisListe['10']	= 'octobre';
$moisListe['11']	= 'novembre';
$moisListe['12']	= 'décembre';

$jourListe = array();
$jourListe[1]		= 'lundi';
$jourListe[2]		= 'mardi';
$jourListe[3]		= 'mercredi';
$jourListe[4]		= 'jeudi';
$jourListe[5]		= 'vendredi';
$jourListe[6]		= 'samedi';
$jourListe[7]		= 'dimanche';


$villeListe = array();
$villeListe['75000']	= 'Paris';
$villeListe['21000']	= 'Dijon';
$villeListe['76600']	= 'Le Havre';
$villeListe['06500']	= 'Menton';
$villeListe['54000']	= 'Nancy';
$villeListe['86000']	= 'Poitiers';
$villeListe['51100']	= 'Reims';

$anneeListe = array();
for($i=date('Y')+1;$i>=2012;$i--){
	$anneeListe[$i] = $i;
}

$JListe = array();
for($i=1;$i<=31;$i++){
	$JListe[$i] = $i;
}

$jListe = array();
$jListe['1']	= 'lundi';
$jListe['2']	= 'mardi';
$jListe['3']	= 'mercredi';
$jListe['4']	= 'jeudi';
$jListe['5']	= 'vendredi';
$jListe['6']	= 'samedi';
$jListe['7']	= 'dimanche';


$templateListe = array();
foreach(glob("{".LOCAL_PATH.SLIDE_TEMPLATE_FOLDER."*}",GLOB_BRACE) as $folder){
    
        if(is_dir($folder)){
        	$dossier = str_replace(LOCAL_PATH.SLIDE_TEMPLATE_FOLDER,'',$folder);
      		$templateListe[$dossier] = $dossier ;
		}
}

$typeTab				= array();
$typeTab['admin'] 		= 'administrateur';
$typeTab['super_admin']	= 'super administrateur';

$accountTypeTab			= array();
$accountTypeTab['mail']	= 'compte mail';
$accountTypeTab['ldap']	= 'compte ldap';

/* meteo */
$meteo_refresh_delay 	= 2*60*60;		// 2 minutes
$meteo_wind_teshold 	= 18.5;			// en mph, environ 30 km/h... 1 km = 0.62 miles
$meteo_cold_treshold 	= 10;			// en degrés
$meteo_hot_treshold 	= 26;			// en degrés

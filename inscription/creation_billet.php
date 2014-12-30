<?php

//============================================================+
// File name   : test_tcpdf-1.php
//
// Description : Generation du billet sciences po en pdf
//
// Author: Loic Horellou
//
// (c) Copyright:
//               Loic Horellou
//               www.syclo.fr
//               Fabien Raymondaud
//               www.fabien-raymondaud.net
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */
/*
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');


function createBillet($uniqueID,$titre,$date,$heure,$nom,$prenom,$statut,$organisateur,$adresse,$salle){
	
	// ON CREE UN DOCUMENT AU FORMAT 18x11 cm
	$pdf = new TCPDF('H', 'mm', 'A4', true, 'UTF-8', false);
	//$pdf = new TCPDF('L', 'mm', array(180.000,110.000), true, 'UTF-8', false);
	
	// DECLARATION DES INFORMATIONS
	$pdf->SetCreator('Sciences Po - Sÿclo');
	$pdf->SetAuthor('Sciences Po - Sÿclo');
	$pdf->SetTitle('Billet - '.$titre);
	$pdf->SetSubject('Billet - '.$titre);
	$pdf->SetKeywords('Sciences Po, '.$titre.', billet, Sÿclo,');
	

	// REGLAGE DE LA PAGE
	$pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->setCellPaddings(0,0,0,0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->AddPage();

	
	// ON FORMATTE L'ID POUR AVOIR UN ESPACE APRES LE PREMIER CHIFFRE PUIS UN ESPACE TOUS LES 4
	$uniqueIDstr = strrev(implode(' ',str_split(strrev($uniqueID), 4)));
	
	$decalX = 15;
	$decalY = 20;
	
	// ON RECUPERE LE FICHIER ILLUSTRATOR QUI VA SERVIR DE FOND
	$pdf->ImageEps($file='billet.ai', $x=$decalX, $y=$decalY, $w=180, $h=110, $link='', $useBoundingBox=false, $align='', $palign='', $border=0, $fitonpage=false);
	
	
	//************** GAUCHE **************

	//TITRE
	$pdf->SetFont('helvetica', '', 14);
	$pdf->SetY(25+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(170, 5,uc_strtoupper_fr($titre), 0, 'L', 1, 0, '', '', true);

	
	//DATE
	$pdf->SetFont('helvetica', 'I', 10);
	$pdf->SetY(38+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(170, 5,dateFormat($date), 0, 'L', 1, 0, '', '', true);

	//NOM
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Nom :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,uc_strtoupper_fr($nom), 0, 'L', 1, 0, '', '', true);

	//PRENOM
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(49+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Prénom :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(49+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$prenom, 0, 'L', 1, 0, '', '', true);

	//N°
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(53+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'N° :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(53+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$uniqueIDstr, 0, 'L', 1, 0, '', '', true);

	//STATUT
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(57+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Statut :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(57+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$statut, 0, 'L', 1, 0, '', '', true);

	//ORGANISATEUR
	$pdf->SetFont('helvetica', '', 8);
	$pdf->SetY(65+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Organisateur :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetY(65+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$organisateur, 0, 'L', 1, 0, '', '', true);

	//ADRESSE
	$pdf->SetY(69+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Adresse :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetY(69+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$adresse, 0, 'L', 1, 0, '', '', true);	


	//************** DROITE **************

	

	//SALLE
	$pdf->SetFont('helvetica', 'B', 18);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(90+$decalX,0,0,0);
	$pdf->MultiCell(90, 5,uc_strtoupper_fr($salle), 0, 'C', 1, 0, '', '', true);

	// ON CREE LE CODE BARRE AU FORMAT 128B
	$pdf->setCellMargins(0,0,0,0);
	$pdf->SetY(62+$decalY);
	$pdf->SetFont('helvetica', '', 10);
	$style = array(
		'hpadding'		=> 'auto',
		'vpadding'		=> 'auto',
		'text'			=> true,
		'label'			=> $uniqueIDstr,
		'fontsize'		=> 8,
		'stretchtext'	=> 4
	);
	$pdf->write1DBarcode($uniqueID, 'C128B', $x=100+$decalX, $y=62+$decalY, $w=70, $h=20, $xres=0.4, $style, 'N');

	
	//INFORMATIONS COMPLEMENTAIRES
	$pdf->SetFont('helvetica', '', 6);
	$pdf->SetY(88+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$texte = "INFORMATIONS COMPLÉMENTAIRES :\nCe billet sera contrôlé à l'entrée, nous vous conseillons vivement d'arriver au plus tard 20 minutes avant le début de l'événement. - Pour vérifier la bonne qualité du billet, assurez-vous que les informations, ainsi que le code barre, sont bien lisibles. - Ce billet est strictement personnel et incessible. Lors des contrôles, vous devrez obligatoirement être muni(e) d'une pièce d'identité, en cours de validité avec photo (carte d'identité, carte d'étudiant, passeport, permis de conduire ou carte de séjour). - Ce billet est uniquement valable pour cet événement, à la date et aux conditions mentionnées. - Sciences Po décline toute responsabilité en cas de perte ou de vol du billet ainsi que pour les anomalies pouvant survenir en cours de réservation ou de traitement du billet.\n";
	$pdf->MultiCell(170, 5,$texte, 0, 'J', 1, 0, '', '', true);
	

	// ON PROTEGE LE FICHIER POUR EVITER LES MODIFICATIONS
	$permissions = array('modify', 'annot-forms', 'fill-forms', 'extract', 'assemble');
	$pdf->SetProtection($permissions);

	// ON EXPORTE LE FICHIER
	mkdir("export/".date("M_Y"));
	
	$pdf->Output('export/'.date("M_Y").'/billet_'.$uniqueID.'.pdf', 'F');
}



function uc_strtoupper_fr($chaine){
	$chaine = strtoupper($chaine);
	$chaine = strtr($chaine, “äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø”,”ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ”);
	return $chaine;
}

function dateFormat($date){
	date_default_timezone_set("Europe/Paris");
	setlocale(LC_TIME, 'fr_FR');
	return strftime('%d %B %Y',strtotime($date));
}*/


//============================================================+
// File name   : test_tcpdf-1.php
//
// Description : Generation du billet sciences po en pdf
//
// Author: Loic Horellou
//
// (c) Copyright:
//               Loic Horellou
//               www.syclo.fr
//               Fabien Raymondeau
//               www.fabien-raymondaud.net
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 */

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');


function createBillet($uniqueID,$titre,$date,$heure,$nom,$prenom,$statut,$organisateur,$adresse,$salle,$isHeadphones=false,$mentions){
	
	
		
	// ON CREE UN DOCUMENT AU FORMAT 18x11 cm
	$pdf = new TCPDF('H', 'mm', 'A4', true, 'UTF-8', false);
	//$pdf = new TCPDF('L', 'mm', array(180.000,110.000), true, 'UTF-8', false);
	
	// DECLARATION DES INFORMATIONS
	$pdf->SetCreator('Sciences Po - Sÿclo');
	$pdf->SetAuthor('Sciences Po - Sÿclo');
	$pdf->SetTitle('Billet - '.$titre);
	$pdf->SetSubject('Billet - '.$titre);
	$pdf->SetKeywords('Sciences Po, '.$titre.', billet, Sÿclo,');
	

	// REGLAGE DE LA PAGE
	$pdf->SetMargins(0,0,0);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->setCellPaddings(0,0,0,0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('helvetica', '', 10);
	$pdf->AddPage();

	
	// ON FORMATTE L'ID POUR AVOIR UN ESPACE APRES LE PREMIER CHIFFRE PUIS UN ESPACE TOUS LES 4
	$uniqueIDstr = strrev(implode(' ',str_split(strrev($uniqueID), 4)));
	
	$decalX = 15;
	$decalY = 20;
	
	// ON RECUPERE LE FICHIER ILLUSTRATOR QUI VA SERVIR DE FOND
	$pdf->ImageEps($file='billet.ai', $x=$decalX, $y=$decalY, $w=180, $h=110, $link='', $useBoundingBox=false, $align='', $palign='', $border=0, $fitonpage=false);
	
	
	//************** GAUCHE **************

	//TITRE
	$pdf->SetFont('helvetica', '', 14);
	$pdf->SetY(25+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(170, 5,uc_strtoupper_fr($titre), 0, 'L', 1, 0, '', '', true);

	
	//DATE
	$pdf->SetFont('helvetica', 'I', 10);
	$pdf->SetY(38+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(170, 5,dateFormat($date), 0, 'L', 1, 0, '', '', true);

	//NOM
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Nom :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,uc_strtoupper_fr($nom), 0, 'L', 1, 0, '', '', true);

	//PRENOM
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(49+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Prénom :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(49+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$prenom, 0, 'L', 1, 0, '', '', true);

	//N°
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(53+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'N° :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(53+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$uniqueIDstr, 0, 'L', 1, 0, '', '', true);

	//STATUT
	$pdf->SetFont('helvetica', '', 10);
	$pdf->SetY(57+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Statut :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetFont('helvetica', 'B', 10);
	$pdf->SetY(57+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$statut, 0, 'L', 1, 0, '', '', true);

	//ORGANISATEUR
	$pdf->SetFont('helvetica', '', 8);
	$pdf->SetY(65+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Organisateur :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetY(65+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$organisateur, 0, 'L', 1, 0, '', '', true);

	//ADRESSE
	$pdf->SetY(69+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	$pdf->MultiCell(20, 5,'Adresse :', 0, 'L', 1, 0, '', '', true);

	$pdf->SetY(69+$decalY);
	$pdf->setCellMargins(25+$decalX,0,0,0);
	$pdf->MultiCell(60, 5,$adresse, 0, 'L', 1, 0, '', '', true);	


	//************** DROITE **************

	//ECOUTEURS
	if($isHeadphones){
		$pdf->SetFont('helvetica', 'BI', 10);
		$pdf->SetY(10+$decalY);
		$pdf->setCellMargins(100+$decalX,0,0,0);
		$pdf->MultiCell(90, 5,uc_strtoupper_fr('{ Écouteurs réservés }'), 0, 'C', 1, 0, '', '', true);
	}
	

	//SALLE
	$pdf->SetFont('helvetica', 'B', 18);
	$pdf->SetY(45+$decalY);
	$pdf->setCellMargins(90+$decalX,0,0,0);
	$pdf->MultiCell(90, 5,uc_strtoupper_fr($salle), 0, 'C', 1, 0, '', '', true);

	// ON CREE LE CODE BARRE AU FORMAT 128B
	$pdf->setCellMargins(0,0,0,0);
	$pdf->SetY(62+$decalY);
	$pdf->SetFont('helvetica', '', 10);
	$style = array(
		'hpadding'		=> 'auto',
		'vpadding'		=> 'auto',
		'text'			=> true,
		'label'			=> $uniqueIDstr,
		'fontsize'		=> 8,
		'stretchtext'	=> 4
	);
	$pdf->write1DBarcode($uniqueID, 'C128B', $x=100+$decalX, $y=62+$decalY, $w=70, $h=20, $xres=0.4, $style, 'N');

	
	//INFORMATIONS COMPLEMENTAIRES
	$pdf->SetFont('helvetica', '', 6);
	$pdf->SetY(88+$decalY);
	$pdf->setCellMargins(5+$decalX,0,0,0);
	if($mentions!=""){
		$texte=utf8_encode(strip_tags(html_entity_decode($mentions)));
	}
	else{
		$texte = "INFORMATIONS COMPLÉMENTAIRES :\nLe billet sera contrôlé à l'entrée de l'événement. Nous vous conseillons vivement d'arriver au plus tard 15 minutes avant le début de l'événement. - Le jour de l'événement, Sciences Po décline toute responsabilité en cas de perte ou de vol du ticket - Pour vérifier la bonne qualité du billet, assurez-vous que les informations du billet, ainsi que le code barres sont bien lisibles. - Le billet est personnel et incessible. Lors des contrôles, vous devrez obligatoirement être munis d'une pièce d'identité, en cours de validité et avec photo : carte d'identité, carte d'étudiant, passeport, permis de conduire ou carte de séjour. - Le billet est uniquement valable pour l'événement, à la date et aux conditions y figurant. Dans les autres cas, ce titre sera considéré comme non valable. - Sciences Po décline toute responsabilité : pour les anomalies pouvant survenir en cours de réservation ou de traitement du billet. ";
	}

	$pdf->MultiCell(170, 5,$texte, 0, 'L', 1, 0, '', '', true);
	
	
	// ON PROTEGE LE FICHIER POUR EVITER LES MODIFICATIONS
	$permissions = array('modify', 'annot-forms', 'fill-forms', 'extract', 'assemble');
	$pdf->SetProtection($permissions);

	// ON EXPORTE LE FICHIER
	mkdir("export/".date("M_Y"));
	
	$pdf->Output('export/'.date("M_Y").'/billet_'.$uniqueID.'.pdf', 'F');
}



function uc_strtoupper_fr($chaine){
	$chaine = strtoupper($chaine);
	$chaine = strtr($chaine, “äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø”,”ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ”);
	return $chaine;
}

function dateFormat($date){
	date_default_timezone_set("Europe/Paris");
	setlocale(LC_TIME, 'fr_FR');
	return utf8_encode(strftime('%d %B %Y',strtotime($date)));
}
?>
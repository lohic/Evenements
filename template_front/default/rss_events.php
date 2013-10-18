<?php
//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_organisme.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
// ajout par loic - plus simple pour mettre les liens des images et du flux en absolu.
$URL =		'http://www.sciencespo.fr/evenements/';
$URLimg =	'http://www.sciencespo.fr/evenements/';

$organisme = new organisme();
$infosOrganisme=$organisme->get_organisme();

if($_GET['lang']=='en'){
	$lang = '_en';
	$lang_link = 'en';
}else{
	$lang='';
	$lang_link = 'fr';	
}

if(isset($_GET['cat']) && $_GET['cat']!=''){
	if(isset($_GET['mot']) && $_GET['mot']!=''){
		$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rel_evenement_keyword AS spre, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND rubrique_id=%s AND keyword_id=%s AND spre.evenement_id=spe.evenement_id AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY evenement_date", 
								func::GetSQLValueString($_GET['cat'], "int"),
								func::GetSQLValueString($_GET['mot'], "int"),
								func::GetSQLValueString($infosOrganisme['organisme_id'], "int"));
	}
	else{
		$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND rubrique_id=%s AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY evenement_date", 
								func::GetSQLValueString($_GET['cat'], "int"),
								func::GetSQLValueString($infosOrganisme['organisme_id'], "int"));
	}
}
else{
	if(isset($_GET['mot']) && $_GET['mot']!=''){
		$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rel_evenement_keyword AS spre, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND keyword_id=%s AND spre.evenement_id=spe.evenement_id AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY evenement_date", 
								func::GetSQLValueString($_GET['mot'], "int"),
								func::GetSQLValueString($infosOrganisme['organisme_id'], "int"));
	}
	else{
		$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY evenement_date", 
								func::GetSQLValueString($infosOrganisme['organisme_id'], "int"));
	}
}

$res = mysql_query($sql)or die(mysql_error());

$rss	='';
$rss	.= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$rss	.= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">'."\n";

$rss	.= '	<channel>'."\n";
$rss	.= '		<title>'.('Science Po - Événements').'</title>'."\n";
$rss	.= '		<description>'.('Flux RSS 2.0 des événements de Sciences Po').'</description>'."\n";
$rss	.= '		<atom:link href="'.$URL.'rss_events.php" rel="self" type="application/rss+xml" />'."\n";
$rss	.= '		<link>'.$URL.'</link>'."\n";


while($row = mysql_fetch_array($res)){
	$sqlsessions ="SELECT * FROM ".TB."sessions WHERE evenement_id='".$row['evenement_id']."'";
	$ressessions = mysql_query($sqlsessions) or die(mysql_error());
	$finEvenement=0;
	while($rowsession = mysql_fetch_array($ressessions)){
		if($rowsession['session_fin']>$finEvenement){
			$finEvenement = $rowsession['session_fin'];
		}
	}
	
	if($finEvenement>time()){
		$rss	.= '		<item>'."\n";
		$rss	.= '			<title>'.($row['evenement_titre'.$lang]).'</title>'."\n";
		if($row['evenement_image']!=""){
		$rss	.= '			<description><![CDATA['.($row['evenement_texte'.$lang]).'<br/><img src="'.$URLimg.'admin/upload/photos/evenement_'.$row['evenement_id'].'/grande-'.$row['evenement_image'].'?cache='.time().'" alt="'.$row['evenement_texte_image'].'" width="480" height="270" />]]></description>'."\n";
		}else{
		$rss	.= '			<description><![CDATA['.($row['evenement_texte'.$lang]).']]></description>'."\n";
		}
		$rss	.= '			<pubDate>'.date("r", $row['evenement_date']).'</pubDate>'."\n";
		$rss	.= '			<link>'.$URL.'?lang='.$lang_link.'&amp;id='.$row['evenement_id'].'</link>'."\n";
		$rss	.= '			<guid>'.$URL.'?lang='.$lang_link.'&amp;id='.$row['evenement_id'].'</guid>'."\n";
		$rss	.= '			<category><![CDATA['.htmlentities($row['rubrique_titre']).']]></category>'."\n";
		$rss	.= '		</item>'."\n";
	}
}

	

$rss	.= '	</channel>'."\n";
$rss	.= '</rss>'."\n";

echo $rss;


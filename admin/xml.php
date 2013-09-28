<?php 
// Creates the xml to be read by flash

// conect 
include('connect.php');

// functions library
include('functions.php');

// CONFIG  SERVER PATH //////////////////////////////////////////////////////////////
// set the server
//$server = "http://luiszuno.com/cms/";

// get the server
$server = "http://" . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

$server = substr($server, 0, (strlen($server) - 7) );
 
/////////////////////////////////////////////////////////////////////////////////////

// print XML
$output = '<?xml version="1.0" encoding="UTF-8" ?>'."\n"."\n";
$output .= '<sciencespo>'."\n"."\n";

// events query
$datePeriodYear = date("Ymd", mktime(0, 0, 0, date("m")-3, date("d"), date("Y") ) );
$sql = "SELECT * FROM albums WHERE status='1' AND dateFromFormat>".$datePeriodYear." ORDER BY dateFromFormat ASC";
$res = mysql_query($sql) or die(mysql_error());

while( $row = mysql_fetch_array( $res ) ){

	$output .= '<event>'."\n";
	$output .= '<event_id>'.$row['event_id'].'</event_id>'."\n";
	$output .= '<status>'.$row['status'].'</status>'."\n";
	$output .= '<displayHome>'.$row['displayHome'].'</displayHome>'."\n";
	$output .= '<dateFrom>'.$row['dateFrom'].'</dateFrom>'."\n";
	$output .= '<dateFromFormat>'.$row['dateFromFormat'].'</dateFromFormat>'."\n";
	$output .= '<dateHourFrom>'.$row['dateHourFrom'].'</dateHourFrom>'."\n";
	$output .= '<dateTo>'.$row['dateTo'].'</dateTo>'."\n";
	$output .= '<dateToFormat>'.$row['dateToFormat'].'</dateToFormat>'."\n";
	$output .= '<dateHourTo>'.$row['dateHourTo'].'</dateHourTo>'."\n";
	$output .= '<location><![CDATA['.utf8_decode($row['location']).']]></location>'."\n";
	$output .= '<location_EN><![CDATA['.utf8_decode($row['location_EN']).']]></location_EN>'."\n";
	$output .= '<locationCode><![CDATA['.utf8_decode($row['locationCode']).']]></locationCode>'."\n";
	$output .= '<locationCode_EN><![CDATA['.utf8_decode($row['locationCode_EN']).']]></locationCode_EN>'."\n";
	$output .= '<acces><![CDATA['.utf8_decode($row['acces']).']]></acces>'."\n";
	$output .= '<acces_EN><![CDATA['.utf8_decode($row['acces_EN']).']]></acces_EN>'."\n";
	$output .= '<organisateur><![CDATA['.utf8_decode($row['organisateur']).']]></organisateur>'."\n";
	$output .= '<organisateur_EN><![CDATA['.utf8_decode($row['organisateur_EN']).']]></organisateur_EN>'."\n";
	$output .= '<coOrganisateur><![CDATA['.utf8_decode($row['coOrganisateur']).']]></coOrganisateur>'."\n";
	$output .= '<coOrganisateur_EN><![CDATA['.utf8_decode($row['coOrganisateur_EN']).']]></coOrganisateur_EN>'."\n";
	$output .= '<langue>'.$row['langue'].'</langue>'."\n";
	$output .= '<rubrique>'.$row['rubrique'].'</rubrique>'."\n";
	$output .= '<type><![CDATA['.utf8_decode($row['type']).']]></type>'."\n";
	$output .= '<type_EN><![CDATA['.utf8_decode($row['type_EN']).']]></type_EN>'."\n";
	$output .= '<cadre><![CDATA['.utf8_decode($row['cadre']).']]></cadre>'."\n";
	$output .= '<cadre_EN><![CDATA['.utf8_decode($row['cadre_EN']).']]></cadre_EN>'."\n";
	$output .= '<title><![CDATA['.utf8_decode($row['title']).']]></title>'."\n";
	$output .= '<title_EN><![CDATA['.utf8_decode($row['title_EN']).']]></title_EN>'."\n";
	$output .= '<chapo><![CDATA['.utf8_decode($row['chapo']).']]></chapo>'."\n";
	$output .= '<chapo_EN><![CDATA['.utf8_decode($row['chapo_EN']).']]></chapo_EN>'."\n";
	$output .= '<description><![CDATA['.utf8_decode($row['description']).']]></description>'."\n";
	$output .= '<description_EN><![CDATA['.utf8_decode($row['description_EN']).']]></description_EN>'."\n";
	$output .= '<linkText><![CDATA['.utf8_decode($row['linkText']).']]></linkText>'."\n";
	$output .= '<link>'.$row['link'].'</link>'."\n";
	$output .= '<linkText_EN><![CDATA['.utf8_decode($row['linkText_EN']).']]></linkText_EN>'."\n";
	$output .= '<link_EN>'.$row['link_EN'].'</link_EN>'."\n";

	
	$sqlImage = mysql_query('SELECT COUNT(*) AS nb FROM images WHERE event_id = "'.$row['event_id'].'"');
	$resImage = mysql_fetch_array( $sqlImage );
	if ( $resImage['nb'] > 0 ) {
		$sqlImage = mysql_query('SELECT * FROM images WHERE event_id = "'.$row['event_id'].'"');
		$resImage = mysql_fetch_array( $sqlImage );
		$output .= '<image>'.$resImage['id'].'</image>'."\n";
	}
	$output .= '</event>'."\n";
	$output .= "\n";
}

$output .= '</sciencespo>'."\n";

// output to utf8
$output = utf8_encode($output);

// print
echo $output;
?>




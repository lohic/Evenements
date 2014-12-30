<?php
// connection to data base
include('connect.php');
require_once( REAL_LOCAL_PATH.'classe/ical/iCalcreator.class.php' );

$config = array( 'unique_id' => 'sciencespo.fr' );
  // set Your unique id
$v = new vcalendar( $config );
  // create a new calendar instance

$v->setProperty( 'method', 'PUBLISH' );
  // required of some calendar software
$v->setProperty( "x-wr-calname", "Calendrier" );
  // required of some calendar software
$v->setProperty( "X-WR-CALDESC", "Calendrier des evenements Sciences Po" );
  // required of some calendar software
$v->setProperty( "X-WR-TIMEZONE", "Europe/Stockholm" );
  // required of some calendar software

$sql = "SELECT * FROM sp_evenements WHERE evenement_id=".$_GET['id'];
$res = mysql_query($sql)or die(mysql_error());
$row = mysql_fetch_array($res);

$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
$ressessions = mysql_query($sqlsessions) or die(mysql_error());
$finEvenement=0;
while($rowsession = mysql_fetch_array($ressessions)){
	if($rowsession['session_fin']>$finEvenement){
		$finEvenement = $rowsession['session_fin'];
	}
}
$debutEvenement = $row['evenement_date'];

$resume = strip_tags($row['evenement_texte']); 
$resume = explode(" ",$resume);

$texte_resume="";

for($i = 0 ; $i < 15 ; $i++){
	if($i != 14){
		$texte_resume .= $resume[$i]." ";
	}
	else{
		$texte_resume .= $resume[$i]."... ";
	}
}

$sqlsession1 ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."' LIMIT 1";
$ressession1 = mysql_query($sqlsession1) or die(mysql_error());
$rowsession1 = mysql_fetch_array($ressession1);

if($rowsession1['session_lieu']!=-1){
	$sqllieu ="SELECT * FROM sp_lieux WHERE lieu_id='".$rowsession1['session_lieu']."'";
	$reslieu = mysql_query($sqllieu) or die(mysql_error());
	$rowlieu = mysql_fetch_array($reslieu);
	$rowsession1['session_lieu'] = utf8_encode($rowlieu['lieu_nom']);
}
else{
	$rowsession1['session_lieu'] = $rowsession1['session_adresse1'];
}

if($rowsession1['session_code_batiment']!=-1){
	$sqlcode ="SELECT * FROM sp_codes_batiments WHERE code_batiment_id='".$rowsession1['session_code_batiment']."'";
	$rescode = mysql_query($sqlcode) or die(mysql_error());
	$rowcode = mysql_fetch_array($rescode);
	$rowsession1['session_code_batiment'] = $rowcode['code_batiment_nom'];
}
else{
	$rowsession1['session_code_batiment'] = $rowsession1['session_adresse2'];
}


$lieu = $rowsession1['session_lieu'].", ".$rowsession1['session_code_batiment'];

$vevent = & $v->newComponent( 'vevent' );

if(date("i",$debutEvenement)<15){
	$minute = 0;
}
else{
	$minute = date("i",$debutEvenement);
}

if(date("i",$finEvenement)<15){
	$minuteFin = 0;
}
else{
	$minuteFin = date("i",$finEvenement);
}

  // create an event calendar component
$start = array( 'year'=>date("Y",$debutEvenement), 'month'=>date("n",$debutEvenement), 'day'=>date("j",$debutEvenement), 'hour'=>date("G",$debutEvenement), 'min'=>$minute, 'sec'=>0 );
$vevent->setProperty( 'dtstart', $start );
$end = array( 'year'=>date("Y",$finEvenement), 'month'=>date("n",$finEvenement), 'day'=>date("j",$finEvenement), 'hour'=>date("G",$finEvenement), 'min'=>$minuteFin, 'sec'=>0 );
$vevent->setProperty( 'dtend', $end );
$vevent->setProperty( 'LOCATION', $lieu );
  // property name - case independent
$vevent->setProperty( 'summary', $row['evenement_titre'] );
$vevent->setProperty( 'description', $texte_resume );
//$vevent->setProperty( 'attendee', 'attendee1@sciencespo.fr' );

$v->returnCalendar();
<?php

// appel de la classe
include 'feedcreator.class.php';

function makeRSSNewEventsFR(){

	// connection to data base
	$host = 'localhost';
	$user = 'events_user';
	$password = 'pF98dQ#';
	$database = 'evenements';

	$link = mysql_connect($host, $user, $password) or die("ERROR: ".mysql_error());
	mysql_select_db($database, $link);

	$rss = new UniversalFeedCreator(); 
	$rss->useCached();
	$rss->title = "SciencesPo Evénements";
	$rss->description = "Tous les événements de sciencesPo";

	//optional
	$rss->descriptionTruncSize = 500;
	$rss->descriptionHtmlSyndicated = true;

	$rss->link = 'http://capricorne.sciences-po.fr/evenements/';
	$rss->syndicationURL = 'http://capricorne.sciences-po.fr/evenements/rss_xml/sciencesPoEvents.xml';

	$datePeriodYear = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y") ) );
	$sql = "SELECT *, DATE_FORMAT(dateFromFormat, '%Y-%m-%d') as datePublication FROM albums WHERE status='1' AND dateFromFormat>".$datePeriodYear." ORDER BY dateFromFormat,dateHourFrom ASC";
	$res = mysql_query($sql) or die(mysql_error());

	while( $row = mysql_fetch_array($res) ) {
		$item = new FeedItem();
		$item->title = $row['title']."  ( ".$row['dateFrom']." - ".$row['dateHourFrom']." )";
		$item->link = 'http://capricorne.sciences-po.fr/evenements/index.html?ev='.$row['event_id'];
		$item->description = $row['chapo'].$row['description'];
		$newDate = substr($row['datePublication'], 0,4)."-".substr($row['datePublication'], 5, 2)."-".substr($row['datePublication'], 8,4);
		if ( $row['dateHourFrom'] != "JOURNEE") { 
			$newHour = substr($row['dateHourFrom'], 0,2).":".substr($row['dateHourFrom'], 3,2).":00+01:00";
		} else {
			$newHour = "08:00".":00+00:00";			
		}
		$item->date = $newDate."T".$newHour;
		$rss->addItem($item);
	}
	// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
	// MBOX, OPML, ATOM, ATOM0.3, HTML, JS
	$rss->saveFeed("RSS1.0", "./rss_xml/sciencesPoEvents.xml");
}

switch ($_REQUEST['op']) {

	case "newEventFR":
		makeRSSNewEventsFR();
		break;
}
?>
<?php
header('Content-type: text/html; charset=UTF-8');

/*
@ CONVERSION DES CHAINES ENVOYEES PAR LES FORMULAIRES
@
@
*/
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = ""){
	if (PHP_VERSION < 6) {
		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	}
	
	$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	
	switch ($theType) {
		case "text":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		break;    
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
		break;
		case "double":
			$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
		break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		break;
		case "boolean":
			$theValue = $theValue ? '1' : '0';
		break;
	}
	return $theValue;
}

$langues_evenement = array();
$langues_evenement ['33'] = "FR"; // français
$langues_evenement ['44'] = "EN"; // anglais
$langues_evenement ['86'] = "ZH"; // chinois
$langues_evenement ['49'] = "DE"; // allemand
$langues_evenement ['45'] = "DA"; // danois
$langues_evenement ['34'] = "ES"; // espagnol
$langues_evenement ['39'] = "IT"; // italian
$langues_evenement ['83'] = "JA"; // japonais
$langues_evenement ['48'] = "PL"; // polonais
$langues_evenement ['7']  = "RU"; // russe
$langues_evenement ['420'] = "CS";// tchèque

//var attendues
/*
event (avec ou sans id)
-> si id => fiche evenement
-> si pas id => liste d'evenements

year 	0000
month	00
organisme

lang (fr ou en)

inscrits
sessions

mettre en place un systeme login/token pour valider l'accès à l'API ???

*/

// connection to data base
include('../admin/connect.php');


/// SI ON ENVOIE UN ID D'EVENEMENT : ?event=

if(isset($_GET['event']) && empty($_GET['event'])){
		
		///// LISTE DES ORGANISMES
		$sql_event	=  "SELECT organisme_id, organisme_nom, organisme_url_front
						FROM sp_organismes";
		$sql_event_query = mysql_query($sql_event) or die(mysql_error());
		
		
		$temp = "";
		while ($organisme = mysql_fetch_assoc($sql_event_query)){
			$list['eventnew_'.$organisme['organisme_id'].'_fr'] = 'Événements '.$organisme['organisme_nom'].'';
			$list['eventnew_'.$organisme['organisme_id'].'_en'] = 'Événements '.$organisme['organisme_nom'].' Anglais';
			
			$temp .= sprintf("{ \"id\" : %d, \"nom\" : \"%s\", \"url\":\"%s\" },",$organisme['organisme_id'],
																					addslashes($organisme['organisme_nom']),
																					$organisme['organisme_url_front']);
		}
		
		$temp =  substr($temp,0,-1);
		
		$json_orga = sprintf("\"organisme\" : [
									%s
								]",$temp);
		
		
		////// LISTES DES EVENEMENTS DE L'ORGANISME

		$year  = empty($_GET['year'])?date('Y'):$_GET['year'];
		$month = empty($_GET['month'])?date('n'):$_GET['month'];
		$lang  = empty($_GET['lang'])?'fr':$_GET['lang'];
		
		$id_organisme = empty($_GET['id_organisme'])?1:$_GET['id_organisme'];
				
		$timestamp_start = mktime(0,0,0,$month,0,$year);
		$timestamp_end	 = mktime(0,0,0,$month+1,0,$year);
		
		$add = $lang != 'fr' ? '_'.$lang:'';
	
		$sql_liste_events		= sprintf('SELECT E.evenement_id AS id,
											E.evenement_titre'.$add.' AS titre,
											E.evenement_date AS date1 
											FROM sp_evenements AS E, sp_organismes AS O, sp_groupes AS G
											WHERE E.evenement_statut=3
											AND E.evenement_date >= %s
											AND E.evenement_date < %s
											AND E.evenement_groupe_id = G. groupe_id
											AND G.groupe_organisme_id = O.organisme_id
											AND O.organisme_id = %s
											ORDER BY E.evenement_date DESC',$timestamp_start,
																			$timestamp_end,
																			$id_organisme);
		
		$sql_liste_events_query	= mysql_query($sql_liste_events) or die(mysql_error());
		$liste_events			= '';

		while ($event = mysql_fetch_assoc($sql_liste_events_query)){
			$ladate = date('Y-m-d',$event['date1']);
			$liste_events 	.= sprintf("{ \"id\" : %d, \"titre\" : \"%s\", \"date\" : \"%s\" },", $event['id'],
																									htmlentities(utf8_decode($event['titre'])),
																									$ladate);
		}

		$liste_events =  substr($liste_events,0,-1);
		
		
		
		$json  = sprintf("{
			\"evenements\" : {
				\"organismes\" : {
					%s	
				},
				\"annee\" : %d,
				\"mois\" : %d,
				\"organisme_nom\" : \"%s\",
				\"organisme_id\" : %d,
				\"organisme_url\" : \"%s\",
				\"evenement\" : [%s]   
			}
		}",	$json_orga,
			$year,
			$month,
			'dir com',//$organisme,
			1,//$id_organisme,
			'http://',//$url_organisme,
			$liste_events);
		
		//echo $json;
	//}
	
		$test = json_decode($json);
		echo $json;
	
	
// SI ON VEUT LA LISTE DES EVENEMENTS : ?event&datemin=&datemax=
	
}else if(isset($_GET['event'])  && !empty($_GET['event']) ){
	//if($_POST['app'] == "AIR" || $_GET['app'] == "AIR"){
		// "liste des confs";
		
		$add	= $_GET['lang'] == 'en' ? '_en' : '';
		
		
		// info evenement
		$sql_event_info			= sprintf("	SELECT 			E.evenement_id AS id,
															COUNT(S.session_id) AS nbrsession,
															E.evenement_titre".$add." AS titre,			
															E.evenement_texte".$add." AS texte,
															E.evenement_resume".$add." AS resume,
															E.evenement_date AS date1,
															E.evenement_date AS date2,
															E.evenement_organisateur AS organisateur,
															E.evenement_coorganisateur AS coorganisateur,
															E.evenement_image AS image,
															R.rubrique_couleur AS couleur,
															O.organisme_url_front AS url_front
													FROM sp_evenements AS E, sp_rubriques AS R, sp_organismes AS O, sp_groupes AS G
													INNER JOIN sp_sessions AS S
															ON S.session_type_inscription = 2
													WHERE E.evenement_id= %s 
															AND S.session_statut_inscription = 1
															AND E.evenement_rubrique = R.rubrique_id
															AND E.evenement_groupe_id = G.groupe_id
															AND G.groupe_organisme_id = O.organisme_id
															AND S.evenement_id = E.evenement_id",GetSQLValueString($_GET['event'],'int'));
		
		$sql_event_info_query	= mysql_query($sql_event_info) or die(mysql_error());
		$event_info				= mysql_fetch_assoc($sql_event_info_query);
		
		$date_debut	= date("Y-m-d",$event_info['date1']);
		$date_fin	= date("Y-m-d",$event_info['date2']);
		
		$horaire_debut	= date("H:i:s",$event_info['date1']);
		$horaire_fin	= date("H:i:s",$event_info['date2']);
		
		$lieu	= isset($event_info['lieu'])?$event_info['lieu']:'';
		$titre	= $event_info['titre'];
		$url	= $event_info['url_front'];
		$couleur= $event_info['couleur'];
		$url_image	= 'http://www.sciencespo.fr/evenements/admin/upload/photos/evenement_'.$event_info['id'].'/'.$event_info['image'];
		
		
		// infos sessions
		$sql_event_info			= sprintf("SELECT 			S.session_id AS id,
															S.session_nom".$add." AS titre,		
															S.session_debut AS date1,	
															S.session_fin AS date2,	
															S.session_langue AS langue,
															L.lieu_nom AS lieu,
															S.session_code_batiment AS code_batiment,
															S.session_lien".$add." AS url,
															S.session_complement_type_inscription AS type_inscription
													FROM sp_sessions AS S
													LEFT JOIN sp_lieux AS L
														ON S.session_lieu = L.lieu_id
													WHERE S.evenement_id = %s",GetSQLValueString($_GET['event'],'int'));
		
		$sql_event_info_query	= mysql_query($sql_event_info) or die(mysql_error());
		//$event_info				= mysql_fetch_assoc($sql_event_info_query);
		
		//echo mysql_num_rows($sql_event_info_query);
		
		$sessions = "";
		
		while ($session = mysql_fetch_assoc($sql_event_info_query)){

			$date_debut	= date("Y-m-d",$session['date1']);
			$date_fin	= date("Y-m-d",$session['date2']);
			
			$horaire_debut	= date("H:i:s",$session['date1']);
			$horaire_fin	= date("H:i:s",$session['date2']);
			
			$code_langue = $langues_evenement[$session['langue']];

			$sessionjson = "
				{ \"id\":%d, \"titre\":\"%s\", \"date_debut\":\"%s\", \"date_fin\":\"%s\", \"horaire_debut\":\"%s\", \"horaire_fin\":\"%s\", \"code_langue\":\"%s\", \"lieu\":\"%s\", \"code_batiment\":\"%s\", \"url\":\"%s\", \"type_inscription\":\"%s\" },";

			$sessions .= sprintf($sessionjson,	$session['id'],
												htmlentities($session['titre']),
												$date_debut,
												$date_fin,
												$horaire_debut,
												$horaire_fin,
												$code_langue,
												htmlentities($session['lieu']),
												$session['code_batiment'],
												$session['url'],
												$session['type_inscription']);
		}
		$sessions =  substr($sessions,0,-1);
		
		$json = "{
	\"evenement\" : {
		\"id\":%d,
		\"titre\":\"%s\",
		\"date_debut\":\"%s\",
		\"date_fin\":\"%s\",
		\"horaire_debut\":\"%s\",
		\"horaire_fin\":\"%s\",
		\"organisateur\":\"%s\",
		\"organisateur_qualite\":\"%s\",
		\"coorganisateur\":\"%s\",
		\"coorganisateur_qualite\":\"%s\",
		\"url\":\"%s\",
		\"url_image\":\"%s\",
		\"couleur\":\"%s\",
		\"sessions\" : [%s]
	}
}";
		
		echo sprintf($json,	$event_info['id'],
							htmlentities($titre),
							$date_debut,
							$date_fin,
							$horaire_debut,
							$horaire_fin,
							htmlentities($event_info['organisateur']),
							htmlentities($organisateur_qualite),
							htmlentities($event_info['coorganisateur']),
							htmlentities($coorganisateur_qualite),
							$url,
							$url_image,
							$couleur,
							$sessions);	
	//}
}else{
	echo '<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Document sans nom</title>
</head>

<body><pre>';

	echo '<p>ok'."</br>\n";
	echo 'paramètres GET attendus :'."</br>\n";
	echo '- year			(defaut : '.date('Y').' - annee en cours)'."</br>\n";
	echo '- month			(defaut : '.date('n').' - mois en cours)'."</br>\n";
	echo '- id_organisme		(defaut : 1 - int | 1->dircom | 2->ceri | 6->picasso)'."</br>\n";
	echo '- lang 			(defaut : fr - fr,en)'."</br>\n";
	echo '- event			(si pas rempli renvoie la liste des événements - sinon renvoie les informations d\'un événement - int)'."</br>\n";
	echo "</br>\n";
	echo 'pour la liste des événements : <a href="http://www.sciencespo.fr/evenements/api/?event&month=6&year=2012">http://www.sciencespo.fr/evenements/api/?event&month=6&year=2012</a>'."</br>\n";
	echo 'pour un événement en particulier : <a href="http://www.sciencespo.fr/evenements/api/?event=1140&lang=en">http://www.sciencespo.fr/evenements/api/?event=1140&lang=en</a>'."</br>\n";
	echo 'ok</p>'."\n";
		
	echo '</pre></body>
</html>';
}


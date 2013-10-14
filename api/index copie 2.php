<?php
header('Content-type: text/html; charset=UTF-8');

include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_api.php');

$core = new core();




$langues_evenement = array();
$langues_evenement ['33']  = "FR"; // français
$langues_evenement ['44']  = "EN"; // anglais
$langues_evenement ['86']  = "ZH"; // chinois
$langues_evenement ['49']  = "DE"; // allemand
$langues_evenement ['45']  = "DA"; // danois
$langues_evenement ['34']  = "ES"; // espagnol
$langues_evenement ['39']  = "IT"; // italian
$langues_evenement ['83']  = "JA"; // japonais
$langues_evenement ['48']  = "PL"; // polonais
$langues_evenement ['7']   = "RU"; // russe
$langues_evenement ['420'] = "CS"; // tchèque




// connection to data base
include('../admin/connect.php');

$json = new stdClass();

// SI ON NE VEUX RECUPERER QUE LES INFORMATIONS D'UNE SESSION
if(isset($_GET['session']) && !empty($_GET['session']) && empty($_GET['inscrits']) ){
	$add	= (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : '';
	
	// informations de l'évenement
	$sql_event_info			= sprintf("	SELECT
											S.session_id			AS id,
											S.session_nom			AS titre,		
											S.session_debut			AS date1,	
											S.session_fin			AS date2,	
											S.session_langue		AS langue,
											L.lieu_nom%s			AS lieu,
											S.session_code_batiment AS code_batiment,
											S.session_lien%s		AS url,
											S.session_complement_type_inscription AS type_inscription,
											E.evenement_id			AS event_id,
											E.evenement_date		AS event_date,
											O.organisme_id			AS organisme_id
										FROM sp_sessions AS S
										LEFT JOIN sp_evenements AS E
											ON (S.evenement_id = E.evenement_id)
										LEFT JOIN sp_lieux AS L
											ON (S.session_lieu = L.lieu_id)
										LEFT JOIN sp_groupes AS G
											ON (E.evenement_groupe_id = G.groupe_id)
										LEFT JOIN sp_organismes AS O
											ON (G.groupe_organisme_id = O.organisme_id)
										WHERE S.session_id = %s",$add,$add,func::GetSQLValueString($_GET['session'],'int'));
	
	$sql_event_info_query	= mysql_query($sql_event_info) or die(mysql_error());
	$event_info				= mysql_fetch_assoc($sql_event_info_query);

	$json->session = new stdClass();

	$organisateur_qualite = "";
	$coorganisateur_qualite = "";

	$json->session->id 						= $event_info['id'];
	$json->session->titre 					= $event_info['titre'];
	$json->session->date_debut 				= date("Y-m-d",$event_info['date1']);
	$json->session->date_fin 				= date("Y-m-d",$event_info['date2']);
	$json->session->horaire_debut 			= date("H:i:s",$event_info['date1']);
	$json->session->horaire_fin 			= date("H:i:s",$event_info['date2']);
	//$json->session->organisateur 			= $event_info['organisateur'];
	//$json->session->organisateur_qualite 	= $organisateur_qualite;
	//$json->session->coorganisateur 			= $event_info['coorganisateur'];
	//$json->session->coorganisateur_qualite 	= $coorganisateur_qualite;
	$json->session->url 					= $event_info['url'];
	//$json->session->url_image 				= 'http://www.sciencespo.fr/evenements/admin/upload/photos/evenement_'.$event_info['event_id'].'/'.$event_info['image'];
	$json->session->type_inscription		= $event_info['type_inscription'];
	$json->session->event_id				= $event_info['event_id'];
	$json->session->event_date				= date("Y-m-d",$event_info['event_date']);
	$json->session->annee					= date("Y",$event_info['event_date']);
	$json->session->mois					= date("m",$event_info['event_date']);
	$json->session->organisme_id			= $event_info['organisme_id'];
	
	echo json_encode($json);
}else
// SI ON VEUT RECUPERER LES INFORMATIONS COMPLETES D'UNE SESSION
if(isset($_GET['session']) && !empty($_GET['session']) && $_GET['inscrits']!='true'){

}else
/// SI ON VEUT LA LISTE DES EVENEMENTS : ?event&datemin=&datemax=
if(isset($_GET['event']) && empty($_GET['event'])){

		$add	= (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : '';
		
		$json->evenements = new stdClass();

		///// LISTE DES ORGANISMES
		$sql_event	=  "SELECT organisme_id, organisme_nom, organisme_url_front
						FROM sp_organismes";
		$sql_event_query = mysql_query($sql_event) or die(mysql_error());
		

		$organismes = array();
		while ($organisme = mysql_fetch_assoc($sql_event_query)){
			$list['eventnew_'.$organisme['organisme_id'].'_fr'] = 'Événements '.$organisme['organisme_nom'].'';
			$list['eventnew_'.$organisme['organisme_id'].'_en'] = 'Événements '.$organisme['organisme_nom'].' Anglais';
			

			$orga = new stdClass();

			$orga->id = $organisme['organisme_id'];
			$orga->nom = $organisme['organisme_nom'];
			$orga->url = $organisme['organisme_url_front'];

			$organismes[] = $orga;

			if(!empty($_GET['id_organisme']) && $_GET['id_organisme'] == $organisme['organisme_id']){
				$json->evenements->organisme_nom 	= $organisme['organisme_id'];
				$json->evenements->organisme_id 	= $organisme['organisme_nom'];
				$json->evenements->organisme_url	= $organisme['organisme_url_front'];
			}

		}

		////// LISTES DES EVENEMENTS DE L'ORGANISME

		$year  = empty($_GET['year'])?date('Y'):$_GET['year'];
		$month = empty($_GET['month'])?date('n'):$_GET['month'];
		$lang  = empty($_GET['lang'])?'fr':$_GET['lang'];
		
		$id_organisme = empty($_GET['id_organisme'])?1:$_GET['id_organisme'];
				
		$timestamp_start = mktime(0,0,0,$month,0,$year);
		$timestamp_end	 = mktime(0,0,0,$month+1,0,$year);
	
	
		$sql_liste_events		= sprintf('SELECT E.evenement_id AS id,
											E.evenement_titre'.$add.' AS titre,
											E.evenement_date AS date1 ,
											S.session_id AS session_id,
											S.session_nom'.$add.' AS session_titre,
											S.session_debut AS session_date1
											FROM sp_evenements AS E, sp_organismes AS O, sp_groupes AS G, sp_sessions AS S
											WHERE E.evenement_statut=3
											AND E.evenement_date >= %s
											AND E.evenement_date < %s
											AND E.evenement_groupe_id = G. groupe_id
											AND G.groupe_organisme_id = O.organisme_id
											AND O.organisme_id = %s
											AND S.evenement_id = E.evenement_id
											ORDER BY E.evenement_date DESC,
											S.session_debut ASC',$timestamp_start,
																			$timestamp_end,
																			$id_organisme);
		
		$sql_liste_events_query	= mysql_query($sql_liste_events) or die(mysql_error());
		//$liste_events			= '';

		$liste_events = array();

		while ($eventdata = mysql_fetch_assoc($sql_liste_events_query)){

			if(! isset( $liste_events[$eventdata['id']] )) {
				$event = new stdClass();
				$event->id 		= $eventdata['id'];
				$event->titre 	= $eventdata['titre'];
				$event->date 	= date('Y-m-d', $eventdata['date1']);

				$session = new stdClass();
				$session->id	= $eventdata['session_id'];
				$session->titre = $eventdata['session_titre'];
				$session->date 	= date('Y-m-d', $eventdata['session_date1']);

				$event->sessions[$eventdata['session_id']] = $session;

				$liste_events[$eventdata['id']] = $event;
			}else{
				$session = new stdClass();
				$session->id	= $eventdata['session_id'];
				$session->titre = $eventdata['session_titre'];
				$session->date 	= date('Y-m-d', $eventdata['session_date1']);

				$liste_events[$eventdata['id']]->sessions[$eventdata['session_id']] = $session;
			}

		}
		
		//$liste_events =  substr($liste_events,0,-1);
		

		$json->evenements->organismes 		= $organismes;
		$json->evenements->annee	  		= $year;
		$json->evenements->mois		  		= $month;
		$json->evenements->evenement		= $liste_events;


		echo json_encode($json);
	

	
}else
/// SI ON ENVOIE UN ID D'EVENEMENT : ?event=
if(isset($_GET['event'])  && !empty($_GET['event']) ){

	$add	= (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : '';
	
	// informations de l'évenement
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
														AND S.evenement_id = E.evenement_id",func::GetSQLValueString($_GET['event'],'int'));
	
	$sql_event_info_query	= mysql_query($sql_event_info) or die(mysql_error());
	$event_info				= mysql_fetch_assoc($sql_event_info_query);

	$json->evenement = new stdClass();

	$organisateur_qualite = "";
	$coorganisateur_qualite = "";

	$json->isAuthenticated = false;

	$json->evenement->id 						= $event_info['id'];
	$json->evenement->titre 					= $event_info['titre'];
	$json->evenement->date_debut 				= date("Y-m-d",$event_info['date1']);
	$json->evenement->date_fin 					= date("Y-m-d",$event_info['date2']);
	$json->evenement->horaire_debut 			= date("H:i:s",$event_info['date1']);
	$json->evenement->horaire_fin 				= date("H:i:s",$event_info['date2']);
	$json->evenement->organisateur 				= $event_info['organisateur'];
	$json->evenement->organisateur_qualite 		= $organisateur_qualite;
	$json->evenement->coorganisateur 			= $event_info['coorganisateur'];
	$json->evenement->coorganisateur_qualite 	= $coorganisateur_qualite;
	$json->evenement->url 						= $event_info['url_front'];
	$json->evenement->url_image 				= 'http://www.sciencespo.fr/evenements/admin/upload/photos/evenement_'.$event_info['id'].'/'.$event_info['image'];
	$json->evenement->couleur 					= $event_info['couleur'];

	



	// information des session
	$sql_event_info			= sprintf("SELECT 			S.session_id AS id,
														S.session_nom".$add." AS titre,		
														S.session_debut AS date1,	
														S.session_fin AS date2,	
														S.session_langue AS langue,
														L.lieu_nom AS lieu,
														B.code_batiment_nom AS code_batiment_nom,
														S.session_lien".$add." AS url,
														S.session_complement_type_inscription AS type_inscription,
														S.session_adresse1 AS nom_adresse,
														S.session_adresse2 AS adresse
												FROM sp_sessions AS S
												LEFT JOIN sp_lieux AS L
													ON S.session_lieu = L.lieu_id
												LEFT JOIN sp_codes_batiments AS B
													ON S.session_code_batiment = B.code_batiment_id
												WHERE S.evenement_id = %s",func::GetSQLValueString($_GET['event'],'int'));
	
	$sql_event_info_query	= mysql_query($sql_event_info) or die(mysql_error());

	$sessions = array();

	while ($session = mysql_fetch_assoc($sql_event_info_query)){

		$sessionjson = new stdClass();

		$sessionjson->id 				= $session['id'];
		$sessionjson->titre 			= $session['titre'];
		$sessionjson->date_debut 		= date("Y-m-d",$session['date1']);
		$sessionjson->date_fin 			= date("Y-m-d",$session['date2']);
		$sessionjson->horaire_debut 	= date("H:i:s",$session['date1']);
		$sessionjson->horaire_fin 		= date("H:i:s",$session['date2']);
		$sessionjson->code_langue 		= $langues_evenement[$session['langue']];
		$sessionjson->code_batiment 	= utf8_encode($session['code_batiment_nom']);
		$sessionjson->lieu 				= utf8_encode($session['lieu']);
		$sessionjson->adresse_nom		= $session['nom_adresse'];
		$sessionjson->adresse 			= $session['adresse'];
		$sessionjson->url 				= $session['url'];
		$sessionjson->type_inscription 	= $session['type_inscription'];


		$sessions[] = $sessionjson;
	}

	
	$json->evenement->sessions = $sessions;

	echo json_encode($json);

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

*/
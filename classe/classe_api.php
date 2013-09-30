<?php


include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_core.php');
include_once(REAL_LOCAL_PATH.'classe/classe_spuser.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('classe_user.php');
//include_once('connexion_vars.php');


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
ou login via LDAP
*/

/**
 * Classe Api
 */
class Api {

	var $core;
	var $json;
	var $langues_evenement;
	var $isAuthenticated;
	
	/**
	 * [api description]
	 * @return [type] [description]
	 */
	function api(){	
		$this->core = new core();
		$this->json = new stdClass();

		$this->populate_langue();


		if($this->core->isAdmin && $this->core->userLevel<=3){
			// on est connecté
			//$this->core->user_info;
			$this->isAuthenticated = true;
		}else{
			$this->isAuthenticated = false;
		}

		$this->json->isAuthenticated = $this->isAuthenticated;
		$this->check_request();
		
		
		if(isset($_GET['event'])  && !empty($_GET['event']) ){
			$this->event_detail();

		}
		else
		if(isset($_GET['event']) && empty($_GET['event'])){
			$this->event_list();

		}
		else
		if(!empty($_GET['session']) ){
			$this->session_detail();

		}

		// on vérifie si une variable a été passée en paramètre
		// si oui on sort le JSON
		if($this->check_request()){
			$this->json_output();
		}
		// si non on affiche le résumé des commandes
		else{
			$this->api_home();
		}

	}

	/**
	 * Vérifie qu'on a passé des paramètres en requêtes, autre que les cookies de sessions
	 * @return boolean 		true si des paramètres ont étés passés, false dans le cas contraire
	 */
	function check_request(){
		$isRequestVar = false;
		foreach($_REQUEST as $key=>$value){
			if($key == 'event' || $key == 'session' || $key == 'login' || $key == 'logout'){
				$isRequestVar = true;

				break;
			}
		}
		return $isRequestVar;
	}

	/**
	 * sert à encoder le json et à le retourner sous forme de chaîne
	 * @return json		une chaîne encodée au format json
	 */
	function json_output(){
		echo json_encode($this->json);
	}

	/**
	 * pour créer le tableau avec les codes langues
	 */
	function populate_langue(){
		$this->langues_evenement = array();
		$this->langues_evenement ['33']  = "FR"; // français
		$this->langues_evenement ['44']  = "EN"; // anglais
		$this->langues_evenement ['86']  = "ZH"; // chinois
		$this->langues_evenement ['49']  = "DE"; // allemand
		$this->langues_evenement ['45']  = "DA"; // danois
		$this->langues_evenement ['34']  = "ES"; // espagnol
		$this->langues_evenement ['39']  = "IT"; // italian
		$this->langues_evenement ['83']  = "JA"; // japonais
		$this->langues_evenement ['48']  = "PL"; // polonais
		$this->langues_evenement ['7']   = "RU"; // russe
		$this->langues_evenement ['420'] = "CS"; // tchèque
	}

	/**
	 * [update_inscrit description]
	 * @return [type] [description]
	 */
	function update_inscrit(){

	}

	/**
	 * [session_detail description]
	 * @return [type] [description]
	 */
	function session_detail(){
		$add	= (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : '';

		$sql_session_info			= sprintf("	SELECT S.session_id AS id,
												S.session_nom".$add." AS titre,		
												S.session_debut AS date1,	
												S.session_fin AS date2,	
												S.session_langue AS langue,
												L.lieu_nom AS lieu,
												B.code_batiment_nom AS code_batiment_nom,
												S.session_complement_type_inscription AS type_inscription,
												S.session_adresse1 AS nom_adresse,
												S.session_adresse2 AS adresse,
												S.session_statut_inscription AS statut_inscription,
												S.session_places_internes_totales AS places_internes_totales,
												S.session_places_internes_prises AS places_internes_prises,
												S.session_places_externes_totales AS places_externes_totales,
												S.session_places_externes_prises AS places_externes_prises,
												S.session_statut_visio AS statut_visio,
												S.session_places_internes_totales_visio AS places_internes_totales_visio,
												S.session_places_internes_prises_visio AS places_internes_prises_visio,
												S.session_places_externes_totales_visio AS places_externes_totales_visio,
												S.session_places_externes_prises_visio AS places_externes_prises_visio,
												S.session_places_enregistrees AS places_enregistrees,
												S.session_places_enregistrees_visio AS places_enregistrees_visio
											FROM sp_sessions AS S
												LEFT JOIN sp_lieux AS L
													ON S.session_lieu = L.lieu_id
												LEFT JOIN sp_codes_batiments AS B
													ON S.session_code_batiment = B.code_batiment_id
											WHERE S.session_id = %s",func::GetSQLValueString($_GET['session'],'int'));

		$sql_session_info_query	= mysql_query($sql_session_info) or die(mysql_error());
		$session_info			= mysql_fetch_assoc($sql_session_info_query);

		$this->json->session = new stdClass();

		$this->json->session->id 					= $session_info['id'];
		$this->json->session->titre 				= $session_info['titre'];
		$this->json->session->date_debut 			= date("Y-m-d",$session_info['date1']);
		$this->json->session->date_fin 				= date("Y-m-d",$session_info['date2']);
		$this->json->session->horaire_debut 		= date("H:i:s",$session_info['date1']);
		$this->json->session->horaire_fin 			= date("H:i:s",$session_info['date2']);
		$this->json->session->lieu 					= $session_info['lieu'];
		$this->json->session->nom_adresse 			= $session_info['nom_adresse'];
		$this->json->session->adresse 				= $session_info['adresse'];
		$this->json->session->code_batiment_nom 	= $session_info['code_batiment_nom'];

		if($this->isAuthenticated){

			$this->json->session->statut_inscription 			= $session_info['statut_inscription'];
			$this->json->session->places_internes_totales 		= $session_info['places_internes_totales'];
			$this->json->session->places_internes_prises 		= $session_info['places_internes_prises'];
			$this->json->session->places_externes_totales 		= $session_info['places_externes_totales'];
			$this->json->session->places_externes_prises 		= $session_info['places_externes_prises'];
			$this->json->session->statut_visio 					= $session_info['statut_visio'];
			$this->json->session->places_internes_totales_visio = $session_info['places_internes_totales_visio'];
			$this->json->session->places_internes_prises_visio 	= $session_info['places_internes_prises_visio'];
			$this->json->session->places_externes_totales_visio = $session_info['places_externes_totales_visio'];
			$this->json->session->places_externes_prises_visio 	= $session_info['places_externes_prises_visio'];
			$this->json->session->places_enregistrees 			= $session_info['places_enregistrees'];
			$this->json->session->places_enregistrees_visio 	= $session_info['places_enregistrees_visio'];


			$this->json->liste_inscrits = array();

			$sql_inscrits_info			= sprintf("SELECT I.inscrit_id AS id,
													I.inscrit_nom AS nom,
													I.inscrit_prenom AS prenom,
													I.inscrit_entreprise AS entreprise,
													I.inscrit_fonction AS fonction,
													I.inscrit_type_inscription AS type_inscription,
													I.inscrit_casque AS casque,
													I.inscrit_est_venu AS est_venu,
													I.inscrit_unique_id AS unique_id,
													I.inscrit_date_scan AS date_scan
													FROM sp_inscrits AS I
													WHERE I.inscrit_session_id = %s
													ORDER BY nom ASC",func::GetSQLValueString($_GET['session'],'int'));
			$sql_inscrits_info_query	= mysql_query($sql_inscrits_info) or die(mysql_error());

			$this->json->last_scan = 0;

			while ($inscrits_info = mysql_fetch_assoc($sql_inscrits_info_query)){


				$inscritJson = new stdClass();
				$inscritJson->id 				= $inscrits_info['id'];
				$inscritJson->nom 				= $inscrits_info['nom'];
				$inscritJson->prenom 			= $inscrits_info['prenom'];
				$inscritJson->entreprise 		= $inscrits_info['entreprise'];
				$inscritJson->fonction 			= $inscrits_info['fonction'];
				$inscritJson->type_inscription 	= $inscrits_info['type_inscription'];
				$inscritJson->casque 			= $inscrits_info['casque'];
				$inscritJson->est_venu 			= $inscrits_info['est_venu'];
				$inscritJson->unique_id 		= $inscrits_info['unique_id'];
				$inscritJson->date_scan 		= $inscrits_info['date_scan'];

				if(intval($inscrits_info['date_scan']) > $this->json->last_scan){
					$this->json->last_scan = $inscrits_info['date_scan'];
				}


				$this->json->liste_inscrits[$inscrits_info['id']] = $inscritJson;
			}
		}
	}


	/**
	 * [event_detail description]
	 * @return [type] [description]
	 */
	function event_detail(){

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

		$this->json->evenement = new stdClass();

		$organisateur_qualite = "";
		$coorganisateur_qualite = "";


		$this->json->evenement->id 						= $event_info['id'];
		$this->json->evenement->titre 					= $event_info['titre'];
		$this->json->evenement->date_debut 				= date("Y-m-d",$event_info['date1']);
		$this->json->evenement->date_fin 				= date("Y-m-d",$event_info['date2']);
		$this->json->evenement->horaire_debut 			= date("H:i:s",$event_info['date1']);
		$this->json->evenement->horaire_fin 			= date("H:i:s",$event_info['date2']);
		$this->json->evenement->organisateur 			= $event_info['organisateur'];
		$this->json->evenement->organisateur_qualite 	= $organisateur_qualite;
		$this->json->evenement->coorganisateur 			= $event_info['coorganisateur'];
		$this->json->evenement->coorganisateur_qualite 	= $coorganisateur_qualite;
		$this->json->evenement->url 					= $event_info['url_front'];
		$this->json->evenement->url_image 				= 'http://www.sciencespo.fr/evenements/admin/upload/photos/evenement_'.$event_info['id'].'/'.$event_info['image'];
		$this->json->evenement->couleur 				= $event_info['couleur'];


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
			$sessionjson->code_langue 		= $this->langues_evenement[$session['langue']];
			$sessionjson->code_batiment 	= utf8_encode($session['code_batiment_nom']);
			$sessionjson->lieu 				= utf8_encode($session['lieu']);
			$sessionjson->adresse_nom		= $session['nom_adresse'];
			$sessionjson->adresse 			= $session['adresse'];
			$sessionjson->url 				= $session['url'];
			$sessionjson->type_inscription 	= $session['type_inscription'];


			$sessions[$session['id']] = $sessionjson;
		}
		
		$this->json->evenement->sessions = $sessions;
	}

	/**
	 * [event_list description]
	 * @return [type] [description]
	 */
	function event_list(){

		$this->json->evenements = new stdClass();


		$add	= (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : '';

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
				$this->json->evenements->organisme_nom 	= $organisme['organisme_id'];
				$this->json->evenements->organisme_id 	= $organisme['organisme_nom'];
				$this->json->evenements->organisme_url	= $organisme['organisme_url_front'];
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
											S.session_debut ASC',	$timestamp_start,
																	$timestamp_end,
																	$id_organisme);
		
		$sql_liste_events_query	= mysql_query($sql_liste_events) or die(mysql_error());

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
		

		$this->json->evenements->organismes 	= $organismes;
		$this->json->evenements->annee	  		= $year;
		$this->json->evenements->mois		  	= $month;
		$this->json->evenements->evenement		= $liste_events;
	}

	/**
	 * [home_api description]
	 * @return [type] [description]
	 */
	function api_home(){
		echo '<!doctype html>
		<html>
		<head>
		<meta charset="UTF-8">
		<title>Api Sciences po événements</title>
		</head>

		<body><pre>';

		//echo '<p>ok'."</br>\n";
		echo 'paramètres GET attendus :'."</br>\n";
		echo '- year			(defaut : '.date('Y').' - annee en cours)'."</br>\n";
		echo '- month			(defaut : '.date('n').' - mois en cours)'."</br>\n";
		echo '- id_organisme		(defaut : 1 - int | 1->dircom | 2->ceri | 6->picasso)'."</br>\n";
		echo '- lang 			(defaut : fr - fr,en)'."</br>\n";
		echo '- event			(si pas rempli renvoie la liste des événements - sinon renvoie les informations d\'un événement - int)'."</br>\n";
		echo '- session		(defaut : NULL - id de la session dont on souhaite récupérer les informations)'."</br>\n";
		echo "</br>\n";
		echo 'paramètres POST attendus :'."</br>\n";
		echo '- logout		(defaut : false)'." - pour se déconnecter de l'API</br>\n";
		echo "</br>\n";
		echo 'pour la liste des événements :     <a href="'.ABSOLUTE_URL.'api/?event&month='.date('n').'&year='.date('Y').'">'.ABSOLUTE_URL.'api/?event&month='.date('n').'&year='.date('Y').'</a>'."</br>\n";
		echo 'pour un événement en particulier : <a href="'.ABSOLUTE_URL.'api/?event=609&lang=en">'.ABSOLUTE_URL.'api/?event=609&lang=en</a>'."</br>\n";
		echo 'pour les détails d\'une session :   <a href="'.ABSOLUTE_URL.'api/?session=654">'.ABSOLUTE_URL.'api/?session=654</a>'."</br>\n";
		echo '(si on est connecté avec un compte administrateur, les détails liés aus inscriptions et la liste des inscrits sont renvoyés)'."</br>\n";
		echo '</p>'."\n";
			
		echo '</pre></body></html>';
	}

}
<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
include_once(REAL_LOCAL_PATH.'classe/classe_organisme.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');

include_once(REAL_LOCAL_PATH.'classe/class.phpmailer.php');
include_once(REAL_LOCAL_PATH.'classe/class.smtp.php');
include_once(REAL_LOCAL_PATH.'classe/classe_billet.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Session {
	
	var $evenement_db	= NULL;
	var $id				= NULL;
	// sert à mémoriser si l'updater a été appelé
	// la variable est statique, ainsi elle sera valable quel que soit le nombre de fois ou on appelle la classe.
	//  ainsi toutes les fonctions d'insertion, mise à jour suppression seront appelées au moment de la création de la classe
	static $updated		= false;
	
	/**
	* GESTION DES EVENEMENTS
	*
	*
	*/
	function session($_array_val=NULL, $_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}


	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets
		//
		// ici on peut aussi normaliser les données à l'aide de :
		// func::GetSQLValueString($valeur, 'int');
		// func::GetSQLValueString($valeur, 'text');
		// ...etc		
		
		if(isset($_POST['update']) && $_POST['update'] == 'update'){
			create_session($_array_val,$_id);
		}

		if(isset($_POST['update']) && $_POST['update'] == 'delete'){
			delete_session($_id);
		}

		// ...etc
		// 

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}


	
	/**
	* create_session creation ou modification d'une session
	* @param $_array_val
	* @param $_id
	*/
	function create_session($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."sessions SET session_nom=%s, session_nom_en=%s, session_debut=%s, session_fin=%s, 
										session_langue=%s, session_lieu=%s, session_code_batiment=%s, session_lien=%s, session_lien_en=%s, 
										session_texte_lien=%s, session_texte_lien_en=%s, session_type_inscription=%s, 
										session_complement_type_inscription=%s, session_statut_inscription=%s, session_places_internes_totales=%s, 
										session_places_externes_totales=%s, session_statut_visio=%s, session_places_internes_totales_visio=%s, 
										session_places_externes_totales_visio=%s, session_adresse1=%s, session_adresse2=%s, 
										session_code_externe=%s, session_traduction=%s, session_editeur_id=%s, session_editeur_ip=%s WHERE id=%s",
													func::GetSQLValueString($_array_val['session_nom'], "int"),
													func::GetSQLValueString($_array_val['session_nom_en'], "text"),
													func::GetSQLValueString($_array_val['session_debut'], "text"),
													func::GetSQLValueString($_array_val['session_fin'], "text"),
													func::GetSQLValueString($_array_val['session_langue'], "text"),
													func::GetSQLValueString($_array_val['session_lieu'], "text"),
													func::GetSQLValueString($_array_val['session_code_batiment'], "text"),
													func::GetSQLValueString($_array_val['session_lien'], "text"),
													func::GetSQLValueString($_array_val['session_lien_en'], "text"),
													func::GetSQLValueString($_array_val['session_texte_lien'], "text"),
													func::GetSQLValueString($_array_val['session_texte_lien_en'], "text"),
													func::GetSQLValueString($_array_val['session_type_inscription'], "int"),
													func::GetSQLValueString($_array_val['session_complement_type_inscription'], "text"),
													func::GetSQLValueString($_array_val['session_statut_inscription'], "int"),
													func::GetSQLValueString($_array_val['session_places_internes_totales'], "int"),
													func::GetSQLValueString($_array_val['session_places_externes_totales'], "text"),
													func::GetSQLValueString($_array_val['session_statut_visio'], "int"),
													func::GetSQLValueString($_array_val['session_places_internes_totales_visio'], "int"),
													func::GetSQLValueString($_array_val['session_places_externes_totales_visio'], "int"),
													func::GetSQLValueString($_array_val['session_adresse1'], "int"),
													func::GetSQLValueString($_array_val['session_adresse2'], "int"),
													func::GetSQLValueString($_array_val['session_code_externe'], "int"),
													func::GetSQLValueString($_array_val['session_traduction'], "int"),
													func::GetSQLValueString($_array_val['session_editeur_id'], "int"),
													func::GetSQLValueString($_array_val['session_editeur_ip'], "int"),
													func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());
			
			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."sessions (session_nom, session_nom_en, session_debut, session_fin, session_langue, 
										session_lieu, session_code_batiment, session_lien, session_lien_en, session_texte_lien, 
										session_texte_lien_en, session_type_inscription, session_complement_type_inscription, 
										session_statut_inscription, session_places_internes_totales, session_places_externes_totales, 
										session_statut_visio, session_places_internes_totales_visio, session_places_externes_totales_visio, 
										session_adresse1, session_adresse2, session_code_externe, session_traduction, session_editeur_id, 
										session_editeur_ip) 
										VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
													func::GetSQLValueString($_array_val['session_nom'], "int"),
													func::GetSQLValueString($_array_val['session_nom_en'], "text"),
													func::GetSQLValueString($_array_val['session_debut'], "text"),
													func::GetSQLValueString($_array_val['session_fin'], "text"),
													func::GetSQLValueString($_array_val['session_langue'], "text"),
													func::GetSQLValueString($_array_val['session_lieu'], "text"),
													func::GetSQLValueString($_array_val['session_code_batiment'], "text"),
													func::GetSQLValueString($_array_val['session_lien'], "text"),
													func::GetSQLValueString($_array_val['session_lien_en'], "text"),
													func::GetSQLValueString($_array_val['session_texte_lien'], "text"),
													func::GetSQLValueString($_array_val['session_texte_lien_en'], "text"),
													func::GetSQLValueString($_array_val['session_type_inscription'], "int"),
													func::GetSQLValueString($_array_val['session_complement_type_inscription'], "text"),
													func::GetSQLValueString($_array_val['session_statut_inscription'], "int"),
													func::GetSQLValueString($_array_val['session_places_internes_totales'], "int"),
													func::GetSQLValueString($_array_val['session_places_externes_totales'], "text"),
													func::GetSQLValueString($_array_val['session_statut_visio'], "int"),
													func::GetSQLValueString($_array_val['session_places_internes_totales_visio'], "int"),
													func::GetSQLValueString($_array_val['session_places_externes_totales_visio'], "int"),
													func::GetSQLValueString($_array_val['session_adresse1'], "int"),
													func::GetSQLValueString($_array_val['session_adresse2'], "int"),
													func::GetSQLValueString($_array_val['session_code_externe'], "int"),
													func::GetSQLValueString($_array_val['session_traduction'], "int"),
													func::GetSQLValueString($_array_val['session_editeur_id'], "int"),
													func::GetSQLValueString($_array_val['session_editeur_ip'], "int"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			

			return $_id;
		}	
	}

	/**
	* delete_session suppression d'une session
	* @param $_id
	* @param $_event_id
	*/
	function delete_session($_id=NULL, $_event_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$sqlcountsessions = mysql_query("SELECT COUNT(*) AS nb FROM sp_sessions WHERE evenement_id='".$_event_id."'");
			$rescountsessions = mysql_fetch_array($sqlcountsessions);
			if($rescountsessions['nb'] > 1){
				$sql ="DELETE FROM ".TB."sessions WHERE session_id = '".$_id."'";
				$res = mysql_query($sql) or die(mysql_error());
			}
		}
	}

	/**
	* get_session récupère une session en fonction de l'id d'un événement
	* @param $_id => id de l'événement
	* @return $rowSession l'enregistrement de la session en base
	*/
	function get_session($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$sqlSession = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($_id, "int"));
			$resSession = mysql_query($sqlSession) or die(mysql_error());
			$rowSession = mysql_fetch_array($resSession);
			return $rowSession;
		}
	}

	/**
	* get_sessions récupère toutes les sessions d'un événement en fonction de l'id d'un événement
	* @param $_id => id de l'événement
	* @return $rowSession l'enregistrement de la session en base
	*/
	function get_sessions($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$sqlSessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($_id, "int"));
			$resSessions = mysql_query($sqlSessions) or die(mysql_error());
			return $resSessions;
		}
	}

	/**
	* get_nb_sessions récupère le nombre de sessions d"un événement
	* @param $_id => id de l'événement
	* @return $rescountsessions['nb'] le nombre de sessions
	*/
	function get_nb_sessions($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$sqlcountsession = sprintf("SELECT COUNT(*) AS nb FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($_id, "int"));
			$sqlcountsessions = mysql_query($sqlcountsession) or die(mysql_error());
			$rescountsessions = mysql_fetch_array($sqlcountsessions);
			return $rescountsessions['nb'];
		}
	}

	/**
	* get_horaires_session récupère les horaires d'une session
	* @param $debut => debut de la session
	* @param $fin => fin de la session
	* @return $chaine => la chaine indiquant les horaires de la session
	*/
	function get_horaires_session($debut, $fin){

		$debut  =   new DateTime($debut);
		$fin  =   new DateTime($fin);

		setlocale(LC_ALL, 'fr_FR');
		$jourDebut = $debut->format('l');
		$heureDebut = $debut->format('H:i');
		$heureFin = $fin->format('H:i');

		$chaine = $jourDebut.' de '.$heureDebut.' à '.$heureFin;

		return $chaine;
	}
	
	
	/**
	* affiche_statut_inscription teste si la session est ouverte à l'inscription ou si elle est complète
	* @param $session => enregistrement d'une session passée en paramètre
	* @param $evenement => enregistrement d'un événément passé en paramètre
	* @param $sinscrire => texte traduit selon la langue indiquant si l'on peut s'inscrire
	* @param $complet => texte traduit selon la langue indiquant si l'événement est complet
	* @param $couleur => couleur de la rubrique à afficher en background de l'icône s'inscrire
	* @return $affichage la chaîne HTML à afficher
	*/
	function affiche_statut_inscription($session=NULL, $evenement=NULL, $sinscrire="S'INSCRIRE", $complet="COMPLET", $couleur="#000"){
		$this->evenement_db->connect_db();
		$affichage="";
		$organisme = new organisme();
		if($session['session_type_inscription']==2 && ($session['session_statut_inscription']==1||$session['session_statut_visio']==1)){ 
			$nbSessions= $this->get_nb_sessions($evenement['evenement_id']);
			if($nbSessions==1){
				$totalPlacesInternes = $session['session_places_internes_totales'] + $session['session_places_internes_totales_visio'];
				$totalPrisesInternes = $session['session_places_internes_prises'] + $session['session_places_internes_prises_visio'];
				$totalPlacesExternes = $session['session_places_externes_totales'] + $session['session_places_externes_totales_visio'];
				$totalPrisesExternes = $session['session_places_externes_prises'] + $session['session_places_externes_prises_visio'];
				if($totalPlacesInternes > $totalPrisesInternes || ($totalPlacesExternes > $totalPrisesExternes && $evenement['evenement_externe']==1)){  
					if($totalPlacesExternes > $totalPrisesExternes && $evenement['evenement_externe']==1){
						$affichage = '<a href="#" class="sinscrire bit_big" id="avec_code"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
					}
					else{
						$affichage = '<a href="#" class="sinscrire bit_big" id="sans_code"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
					}
				}
				else{
					if($totalPlacesInternes!=0 && ($totalPlacesExternes==0 || ($totalPlacesExternes !=0 && $evenement['evenement_externe']==1)) ){
						$affichage = '<span class="complet">'.$complet.'</span>';
					}
				}
			}
			else{
				$resSessions = $this->get_sessions($evenement['evenement_id']);
				$estcomplet = true;
				$totalPlacesGeneral = 0;
				while($rowSessions = mysql_fetch_array($resSessions)){
					$totalPlacesInternes = $rowSessions['session_places_internes_totales'] + $rowSessions['session_places_internes_totales_visio'];
					$totalPrisesInternes = $rowSessions['session_places_internes_prises'] + $rowSessions['session_places_internes_prises_visio'];
					$totalPlacesExternes = $rowSessions['session_places_externes_totales'] + $rowSessions['session_places_externes_totales_visio'];
					$totalPrisesexternes = $rowSessions['session_places_externes_prises'] + $rowSessions['session_places_externes_prises_visio'];
					$totalPlacesGeneral = $totalPlacesGeneral + $totalPlacesInternes + $totalPlacesExternes;
					if($totalPlacesInternes > $totalPrisesInternes || ($totalPlacesExternes > $totalPrisesexternes && $evenement['evenement_externe']==1)){
						$estcomplet=false;
					}
				}
				if($estcomplet==false){
					if($evenement['evenement_externe']==1){
						$affichage = '<a href="#" class="sinscrire_multiple bit_big" id="avec_code"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
					}
					else{
						$affichage = '<a href="#" class="sinscrire_multiple bit_big" id="sans_code"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';					
					}	
				}
				else{
					if($totalPlacesGeneral!=0){
						$affichage = '<span class="complet">'.$complet.'</span>';
					}
				}
			}
		}
		return $affichage;
	}

	/**
	* make_inscription réalise une inscription interne à un événement avec une session unique
	* @param $_id => id de la session
	* @param $login => login LDAP passé par le formulaire
	* @param $password => mot de passe LDAP passé par le formulaire
	* @param $titre => titre de l'événement passé par le formulaire
	* @param $date => date de l'événement passé par le formulaire
	* @param $lieu => lieu de l'événement passé par le formulaire
	* @param $casque => indique si l'inscrit a coché l'option casque
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function make_inscription($_id, $login, $password, $titre, $date, $lieu, $casque){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		session_start();

		$erreurLDAP = "";
		$erreurSessionComplete = "";
		$erreurDejaInscrit = "";
		$confirmation = "";

		$inscriptionOK = "";
		$dejaInscrit = "";
		$completeDerniereMinute = "";
		$alerteInterne = "";
		$champVide = "";

		if(isset($login) && isset($password) && $login!="" && $password!=""){
			$infosEtudiant = func::connectLDAP($login,$password);
			
			switch ($infosEtudiant->info){
				case "login error" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
				case "no connexion" : $erreurLDAP="Impossible de se connecter au serveur d'identification pour le moment."; break;
				case "no login" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
				default : $erreurLDAP=""; break;
			}

			if($erreurLDAP==""){
				$_SESSION['nomSP'] = $infosEtudiant->nom;
				$_SESSION['prenomSP'] = $infosEtudiant->prenom;
				$_SESSION['mailSP'] = $infosEtudiant->email;
				$_SESSION['typeSP'] = $infosEtudiant->type;
			}
		}

		if($login=="" || $password==""){
			$champVide = "Tous les champs marqués d'une * doivent être remplis.";
		}

		$sqlSession = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND sps.session_id =%s", func::GetSQLValueString($_id, "int"));
		$resSession = mysql_query($sqlSession) or die(mysql_error());
		$rowSession = mysql_fetch_array($resSession);

		if(isset($_SESSION['nomSP'])){
			$testVisio = false;
			if($rowSession['session_statut_inscription']==1){
				$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
				if($estComplete){
					$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_internes_totales_visio", "session_places_internes_prises_visio");
					if(!$estComplete){
						$affichageRecap = "Retransmission";
						$type_inscription = "visio interne";
						$testVisio = true;
					}
				}
				else{
					$affichageRecap = "Amphithéâtre";
					$type_inscription = "amphi interne";
				}
			}
			else{
				$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_internes_totales_visio", "session_places_internes_prises_visio");
				$affichageRecap = "Retransmission";
				$type_inscription = "visio interne";
			}
			if($estComplete){
				$completeDerniereMinute = "La dernière place pour la conférence ".$rowSession['session_nom']." vient malheureusement d'être réservée.";
			}
			else{
				
				$testDejaInscrit = $this->deja_inscrit($_SESSION['mailSP'],$rowSession['session_id']);
				if(!$testDejaInscrit){
					$dateInscription = time();
					$dateTime = date('Y-m-d H:i:s');
					
					$sqlinsert ="INSERT INTO ".TB."inscrits VALUES ('', '".$rowSession['evenement_id']."', '".$rowSession['session_id']."', '".addslashes($_SESSION['nomSP'])."', '".addslashes($_SESSION['prenomSP'])."', '".addslashes($_SESSION['mailSP'])."', 'Sciences Po', '".$_SESSION['typeSP']."', '".$type_inscription."','".$casque."','','','".$dateInscription."','".$dateTime."','')"; 
					$resinsert = mysql_query($sqlinsert) or die(mysql_error());
					$lastIdInsert = mysql_insert_id(); 

					if($rowSession['session_statut_inscription']==1 && !$testVisio){				
						$this->incremente_nb_inscrits($rowSession['session_id'], "session_places_internes_prises");
						
						if($rowSession['session_lieu']!=-1){							
							$sqlLieu =sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", func::GetSQLValueString($rowSession['session_lieu'], "int"));
							$resLieu = mysql_query($sqlLieu) or die(mysql_error());
							$rowLieu = mysql_fetch_array($resLieu);
							$endroit = $rowLieu['lieu_nom'];
						}
						else{
							$endroit = $rowSession['session_adresse1'];
						}
						$endroitMessage = 0;
					}
					else{
						$this->incremente_nb_inscrits($rowSession['session_id'], "session_places_internes_prises_visio");
						$endroit = "Retransmission";
						$endroitMessage = 1;
					}

					$sqlcountinscrit = sprintf("SELECT COUNT(*) AS nb FROM ".TB."inscrits WHERE inscrit_session_id=%s", func::GetSQLValueString($rowSession['session_id'], "int"));
					$sqlcountinscrits = mysql_query($sqlcountinscrit) or die(mysql_error());
					$rescountinscrits = mysql_fetch_array($sqlcountinscrits);
					
					$uniqueId = func::uniqueID($rowSession['session_id'], $rescountinscrits['nb']);
		            
					$sqlupdate = sprintf("UPDATE ".TB."inscrits SET inscrit_unique_id=%s WHERE inscrit_id =%s", 
												func::GetSQLValueString($uniqueId, "text"),
												func::GetSQLValueString($lastIdInsert, "int"));

					mysql_query($sqlupdate) or die(mysql_error());
					$dateBillet=date("d/m/Y", $rowSession['session_debut']);
					$dateMail=date("d/m/Y", $rowSession['session_debut']);
					$heureDebut = date("H:i", $rowSession['session_debut']);
					$heureFin = date("H:i", $rowSession['session_fin']);
					
					if($casque==1){
						$casque = true;
					}
					else{
						$casque = false;
					}
					
					$sqlBan = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
					$resBan = mysql_query($sqlBan)or die(mysql_error());
					$rowBan = mysql_fetch_array($resBan);
					
					if($rowBan['organisme_mentions']==""){
						$mentions = "";
					}
					else{
						$mentions = $rowBan['organisme_mentions'];
					}
					
					if($rowBan['organisme_image_billet']==""){
						$cheminImageBillet=REAL_LOCAL_PATH.'admin/upload/billet/defaut.png';
					}
					else{
						$cheminImageBillet=REAL_LOCAL_PATH.'admin/upload/billet/'.$rowBan['organisme_id'].'/'.$rowBan['organisme_image_billet'];
					}
					
					$billet = new billet($uniqueId, $rowBan['organisme_couleur'], $rowSession['session_nom'], $dateBillet, $heureDebut, $code_langues_evenement[$rowSession['session_langue']], $_SESSION['nomSP'], $_SESSION['prenomSP'], 'interne', $endroit, $casque, $rowSession['session_adresse2'], $rowSession['evenement_organisateur'], $cheminImageBillet, $rowBan['organisme_url_image']);

					$sessionBillet = $rowSession['session_nom'];
				
					func::envoiMail($sessionBillet, $billet->HTMLticket, $billet->PDFurl, $billet->passbookFile, $uniqueId, $_SESSION['mailSP']);
					
					func::envoiAlerte($rowSession['session_id']);

					if($rowSession['session_statut_inscription']==1 && !$testVisio){
						$estCompleteBis = $this->test_session_complete($rowSession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
						if($estCompleteBis){
							$estCompleteBis = $this->test_session_complete($rowSession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
							if($estCompleteBis){
								$totale=true;
								$this->bascule_inscription($rowSession['session_id'], $totale);
							}
							else{
								$totale=false;
								$this->bascule_inscription($rowSession['session_id'], $totale);
							}
						}
					}

					$inscriptionOK = true;
				}
				else{
					$dejaInscrit=true;
				}
			}

			$differenceInterneAmphi = $rowSession['session_places_internes_totales'] - $rowSession['session_places_internes_prises'];

			if($rowSession['session_statut_inscription']==1){
				if($differenceInterneAmphi==0){
					$alerteInterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
					nous vous proposons de vous inscrire à la retransmission en direct.</p>";
				}
			}
			else{
				$alerteInterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
				nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			}
		}

		$casque="";

		if($rowSession['session_traduction']==1){
			$casque=true;
		}

		$retour->titre_bloc 	= "Vous êtes bien inscrit à l'événement";
		$retour->session_id 	= $rowSession['session_id'];
		$retour->titre 	= $titre;
		$retour->date 	= $date;
		$retour->lieu 	= $lieu;
		$retour->infos_inscription = "Vos informations d'inscription sont les suivantes :";
		$retour->nom  = $_SESSION['nomSP'];
		$retour->prenom  = $_SESSION['prenomSP'];
		$retour->type_inscription = $affichageRecap;
		$retour->numero = $uniqueId;
		$retour->important = "<strong>IMPORTANT :</strong> Un mail contenant un billet au format .pdf vous a été envoyé à l'adresse ".$_SESSION['mailSP'].". <strong>Veuillez imprimer le billet et vous présenter à l'accueil à l'adresse spécifiée.</strong>";
		$retour->casque = $casque;
		$retour->alerteInterne = $alerteInterne;
		$retour->erreurLDAP = $erreurLDAP;
		$retour->inscriptionOK = $inscriptionOK;
		$retour->completeDerniereMinute = $completeDerniereMinute;
		$retour->champVide = $champVide;

		session_unset();
		return json_encode($retour);
	}

	/**
	* make_inscription_externe réalise une inscription externe à un événement avec une session unique
	* @param $_id => id de la session
	* @param $nom => nom envoyé par le formulaire
	* @param $prenom => prénom envoyé par le formulaire
	* @param $mail => adresse mail envoyée par le formulaire
	* @param $entreprise => entreprise envoyée par le formulaire
	* @param $fonction => fonction envoyée par le formulaire
	* @param $casque => indique si l'inscrit a coché l'option casque
	* @param $titre => titre de l'événement passé par le formulaire
	* @param $date => date de l'événement passé par le formulaire
	* @param $lieu => lieu de l'événement passé par le formulaire
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function make_inscription_externe($_id, $nom, $prenom, $mail, $entreprise, $fonction, $casque, $titre, $date, $lieu){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		session_start();

		$erreurSessionComplete = "";
		$erreurDejaInscrit = "";
		$erreurChamps = "";
		$confirmation = "";

		$inscriptionOK = "";
		$dejaInscrit = "";
		$completeDerniereMinute = "";
		$alerteExterne = "";

		$sqlSession = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND sps.session_id =%s", func::GetSQLValueString($_id, "int"));
		$resSession = mysql_query($sqlSession) or die(mysql_error());
		$rowSession = mysql_fetch_array($resSession);

		if(isset($nom)){
			$testVisio = false;
			$erreurChampsTest = func::testeChamps($nom, $prenom, $mail);

			if($erreurChampsTest){
				if($rowSession['session_statut_inscription']==1){
					$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
					if($estComplete){
						$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_externes_totales_visio", "session_places_externes_prises_visio");
						if(!$estComplete){
							$affichageRecap = "Retransmission";
							$type_inscription = "visio externe";
							$testVisio = true;
						}
					}
					else{
						$affichageRecap = "Amphithéâtre";
						$type_inscription = "amphi externe";
					}
				}
				else{
					$estComplete = $this->test_session_complete($rowSession['session_id'], "session_places_externes_totales_visio", "session_places_externes_prises_visio");
					$affichageRecap = "Retransmission";
					$type_inscription = "visio externe";
				}
				
				if($estComplete){
					$completeDerniereMinute .= "La dernière place pour la conférence ".$rowSession['session_nom']." vient malheureusement d'être réservée.";
				}
				else{
					$testDejaInscrit = $this->deja_inscrit($mail,$rowSession['session_id']);
					if(!$testDejaInscrit){
						$dateInscription = time();
						$dateTime = date('Y-m-d H:i:s');

						$sqlinsert ="INSERT INTO ".TB."inscrits VALUES ('', '".$rowSession['evenement_id']."', '".$rowSession['session_id']."', '".addslashes($nom)."', '".addslashes($prenom)."', '".addslashes($mail)."', '".addslashes($entreprise)."', '".addslashes($fonction)."', '".$type_inscription."','".$casque."','','','".$dateInscription."','".$dateTime."','')";
						$resinsert = mysql_query($sqlinsert) or die(mysql_error());
						$lastIdInsert = mysql_insert_id(); 
						if($rowSession['session_statut_inscription']==1 && !$testVisio){				
							$this->incremente_nb_inscrits($rowSession['session_id'], "session_places_externes_prises");
						
							if($rowSession['session_lieu']!=-1){ 
								$sqlLieu =sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", func::GetSQLValueString($rowSession['session_lieu'], "int"));
								$resLieu = mysql_query($sqlLieu) or die(mysql_error());
								$rowLieu = mysql_fetch_array($resLieu);
								$endroit = $rowLieu['lieu_nom'];
							}
							else{
								$endroit = $rowSession['session_adresse1'];
							}
							$endroitMessage = 0;
						}
						else{
							$this->incremente_nb_inscrits($rowSession['session_id'], "session_places_externes_prises_visio");
							$endroit = "Retransmission";
							$endroitMessage = 1;
						}
					    
						$sqlcountinscrit = sprintf("SELECT COUNT(*) AS nb FROM ".TB."inscrits WHERE inscrit_session_id=%s", func::GetSQLValueString($rowSession['session_id'], "int"));
						$sqlcountinscrits = mysql_query($sqlcountinscrit) or die(mysql_error());
						$rescountinscrits = mysql_fetch_array($sqlcountinscrits);


						$uniqueId = func::uniqueID($rowSession['session_id'], $rescountinscrits['nb']);
	                    
						$sqlupdate = sprintf("UPDATE ".TB."inscrits SET inscrit_unique_id=%s WHERE inscrit_id =%s", 
													func::GetSQLValueString($uniqueId, "text"),
													func::GetSQLValueString($lastIdInsert, "int"));
						mysql_query($sqlupdate) or die(mysql_error());

						$dateBillet=date("d/m/Y", $rowSession['session_debut']);
						$dateMail=date("d/m/Y", $rowSession['session_debut']);
						$heureDebut = date("H:i", $rowSession['session_debut']);
						$heureFin = date("H:i", $rowSession['session_fin']);
			
						if($casque==1){
							$casque = true;
						}
						else{
							$casque = false;
						}
	                    
						$sqlBan = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
						$resBan = mysql_query($sqlBan)or die(mysql_error());
						$rowBan = mysql_fetch_array($resBan);

						if($rowBan['organisme_mentions']==""){
							$mentions = "";
						}
						else{
							$mentions = $rowBan['organisme_mentions'];
						}
						if($rowBan['organisme_image_billet']==""){
							$cheminImageBillet=REAL_LOCAL_PATH.'admin/upload/billet/defaut.png';
						}
						else{
							$cheminImageBillet=REAL_LOCAL_PATH.'admin/upload/billet/'.$rowBan['organisme_id'].'/'.$rowBan['organisme_image_billet'];
						}
						$billet = new billet($uniqueId, $rowBan['organisme_couleur'], $rowSession['session_nom'], $dateBillet, $heureDebut, $code_langues_evenement[$rowSession['session_langue']], $nom, $prenom, 'externe', $endroit, $casque, $rowSession['session_adresse2'], $rowSession['evenement_organisateur'], $cheminImageBillet, $rowBan['organisme_url_image']);

						$sessionBillet = $rowSession['session_nom'];
					
						func::envoiMail($sessionBillet, $billet->HTMLticket, $billet->PDFurl, $billet->passbookFile, $uniqueId, $mail);
						func::envoiAlerte($rowSession['session_id']);
					
						if($rowSession['session_statut_inscription']==1 && !$testVisio){
							$estCompleteBis = $this->test_session_complete($rowSession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
							if($estCompleteBis){
								$estCompleteBis = $this->test_session_complete($rowSession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
								if($estCompleteBis){
									$totale=true;
									$this->basculeInscription($rowSession['session_id'], $totale);
								}
								else{
									$totale=false;
									$this->basculeInscription($rowSession['session_id'], $totale);
								}
							}
						}
					
						$inscriptionOK = true;					
					}
					else{
						$dejaInscrit=true;
					}
				}
				
			}
			else{
				$erreurChamps = "Les informations que vous avez fournies ne permettent pas de vous identifier.";
			}

			$differenceExterneAmphi = $rowSession['session_places_externes_totales'] - $rowSession['session_places_externes_prises'];

			if($rowSession['session_statut_inscription']==1){
				if($differenceExterneAmphi==0){
					$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
					nous vous proposons de vous inscrire à la retransmission en direct.</p>";
				}
			}
			else{
				$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
				nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			}
		}

		$casque="";

		if($rowSession['session_traduction']==1){
			$casque=true;
		}

		$retour->titre_bloc 	= "Vous êtes bien inscrit à l'événement";
		$retour->session_id 	= $rowSession['session_id'];
		$retour->titre 	= $titre;
		$retour->date 	= $date;
		$retour->lieu 	= $lieu;
		$retour->infos_inscription = "Vos informations d'inscription sont les suivantes :";
		$retour->nom  = $nom;
		$retour->prenom  = $prenom;
		$retour->type_inscription = $affichageRecap;
		$retour->numero = $uniqueId;
		$retour->important = "<strong>IMPORTANT :</strong> Un mail contenant un billet au format .pdf vous a été envoyé à l'adresse ".$mail.". <strong>Veuillez imprimer le billet et vous présenter à l'accueil à l'adresse spécifiée.</strong>";
		$retour->casque = $casque;
		$retour->alerteExterne = $alerteExterne;
		$retour->erreurChamps = $erreurChamps;
		$retour->dejaInscrit = $dejaInscrit;
		$retour->inscriptionOK = $inscriptionOK;
		$retour->completeDerniereMinute = $completeDerniereMinute;

		session_unset();
		return json_encode($retour);
	}


	/**
	* test_session_complete teste si la session passée en paramètre est complète ou non
	* @param $_id => id de la session
	* @param $totales => nombre de places total
	* @param $prises => nombre de places prises
	* @return boolean => retourne true si la session est complète
	*/
	function test_session_complete($_id, $totales, $prises){
		$sql = sprintf("SELECT ".$totales." AS spt, ".$prises." AS spp FROM ".TB."sessions WHERE session_id =%s", 
									func::GetSQLValueString($_id, "int"));
		$res=mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);
		if($row['spp']>=$row['spt']){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	* deja_inscrit teste si la session passée en paramètre est complète ou non
	* @param $mail => mail de l'inscrit
	* @param $_id => identifiant de la session testée
	* @return boolean => retourne true si la personne est déjà inscrite à cette session
	*/
	function deja_inscrit($mail, $_id){ 
		$sql = sprintf("SELECT * FROM ".TB."inscrits WHERE inscrit_mail =%s AND inscrit_session_id=%s", 
									func::GetSQLValueString($mail, "text"),
									func::GetSQLValueString($_id, "int"));
		
		$res=mysql_query($sql) or die(mysql_error());
		// return the number of images
		if(mysql_num_rows($res)==0){
			return false;
		}
		else{
			return true;
		}
	}


	/**
	* incremente_nb_inscrits augmente le nombre d'inscrits à la session
	* @param $_id => identifiant de la session
	* @param $champ => champ à incrémenter
	*/
	function incremente_nb_inscrits($_id, $champ){
		$sql = sprintf("UPDATE sp_sessions SET ".$champ."=".$champ."+1 WHERE session_id =%s",
		 							func::GetSQLValueString($_id, "int"));
		$res=mysql_query($sql) or die(mysql_error());
	}

	/**
	* bascule_inscription bascule le statut d'une inscription
	* @param $_id => identifiant de la session
	* @param $totale => si vaut true bascule l'inscription en amphi indisponible et la visio en dispo, sinon seulement la visio en dispo
	*/
	function bascule_inscription($_id, $totale){
		if($totale){ 
			$sql = sprintf("UPDATE sp_sessions SET session_statut_inscription=0, session_statut_visio=1 WHERE session_id =%s", 
										func::GetSQLValueString($_id, "int"));
		}
		else{
			$sql = sprintf("UPDATE sp_sessions SET session_statut_visio=1 WHERE session_id =%s", 
										func::GetSQLValueString($_id, "int"));
		}
		$res=mysql_query($sql) or die(mysql_error());
	}


	
	function get_default_template($_template=NULL,$_image=NULL){
		// exemple pour le fonctionnement d'un template
		// 
		// 
		$contents = "<!--debut du contenu-->";
		$contents .= '<link rel="template_front/'.$_template.'/style.css">';

		$image = $_image;

		ob_start();

		include(REAL_LOCAL_PATH.'template_front/'.$_template.'/index.php');

		$contents .= ob_get_contents();

		ob_end_clean();

		return $contents;

	}
}
	
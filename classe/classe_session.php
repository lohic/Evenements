<?php

include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe_fonctions.php');
include_once('classe_organisme.php');
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
		func::GetSQLValueString($valeur, 'int');
		func::GetSQLValueString($valeur, 'text');
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
					$urlFront = $organisme->get_URL_front_from_group($evenement['evenement_groupe_id']);
					if($totalPlacesExternes > $totalPrisesExternes && $evenement['evenement_externe']==1){
						//$affichage = '<a href="'.$urlFront.'inscription/inscription.php?id='.$session['session_id'].'&amp;codeExterne='.$session['session_code_externe'].'" class="sinscrire bit_big" target="_blank"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
						$affichage = '<a href="#" class="sinscrire bit_big"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';

					}
					else{
						//$affichage = '<a href="'.$urlFront.'inscription/inscription.php?id='.$session['session_id'].'" class="sinscrire bit_big" target="_blank"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
						$affichage = '<a href="#" class="sinscrire bit_big"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
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
					$urlFront = $organisme->get_URL_front_from_group($evenement['evenement_groupe_id']);
					if($evenement['evenement_externe']==1){
						//$affichage = '<a href="'.$urlFront.'inscription/inscription_multiple.php?id='.$evenement['evenement_id'].'&amp;codeExterne='.$session['session_code_externe'].'" class="sinscrire bit_big" target="_blank"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
						$affichage = '<a href="#" class="sinscrire bit_big"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
					}
					else{
						//$affichage = '<a href="'.$urlFront.'inscription/inscription_multiple.php?id='.$evenement['evenement_id'].'" class="sinscrire bit_big" target="_blank"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';
						$affichage = '<a href="#" class="sinscrire bit_big"><span style="background-color:'.$couleur.'"></span>'.$sinscrire.'</a>';					
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
	
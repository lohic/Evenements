<?php

include_once('../vars/config.php');
include_once('classe_connexion.php');
include_once('classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Evenement {
	
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
	function evenement($_array_val=NULL, $_id=NULL){
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
			create_event($_array_val,$_id);
		}

		if(isset($_POST['update']) && $_POST['update'] == 'delete'){
			delete_event($_id);
		}



		// ...etc
		// 

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}


	
	/**
	* create_event creation ou modification d'un evenement
	* @param $_array_val
	* @param $_id
	*/
	function create_event($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."evenements SET evenement_statut=%s, evenement_titre=%s, evenement_titre_en=%s, 
										evenement_resume=%s, evenement_resume_en=%s, evenement_texte=%s, evenement_texte_en=%s, 
										evenement_organisateur=%s, evenement_organisateur_en=%s, evenement_coorganisateur=%s, 
										evenement_coorganisateur_en=%s, evenement_rubrique=%s, evenement_image=%s, evenement_facebook=%s, 
										evenement_editeur_id=%s, evenement_editeur_ip=%s, evenement_externe=%s WHERE id=%s",
													func::GetSQLValueString($_array_val['evenement_statut'], "int"),
													func::GetSQLValueString($_array_val['evenement_titre'], "text"),
													func::GetSQLValueString($_array_val['evenement_titre_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_resume'], "text"),
													func::GetSQLValueString($_array_val['evenement_resume_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_texte'], "text"),
													func::GetSQLValueString($_array_val['evenement_texte_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_organisateur'], "text"),
													func::GetSQLValueString($_array_val['evenement_organisateur_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_coorganisateur'], "text"),
													func::GetSQLValueString($_array_val['evenement_coorganisateur_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_rubrique'], "int"),
													func::GetSQLValueString($_array_val['evenement_image'], "text"),
													func::GetSQLValueString($_array_val['evenement_facebook'], "int"),
													func::GetSQLValueString($_array_val['evenement_editeur_id'], "int"),
													func::GetSQLValueString($_array_val['evenement_editeur_ip'], "text"),
													func::GetSQLValueString($_array_val['evenement_externe'], "int"),
													func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());
			
			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."evenements (evenement_statut, evenement_titre, evenement_titre_en, evenement_resume, 
										evenement_resume_en, evenement_texte, evenement_texte_en, evenement_organisateur, 
										evenement_organisateur_en, evenement_coorganisateur, evenement_coorganisateur_en, evenement_rubrique, 
										evenement_image, evenement_facebook, evenement_editeur_id, evenement_editeur_ip, evenement_externe) 
										VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
													func::GetSQLValueString($_array_val['evenement_statut'], "int"),
													func::GetSQLValueString($_array_val['evenement_titre'], "text"),
													func::GetSQLValueString($_array_val['evenement_titre_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_resume'], "text"),
													func::GetSQLValueString($_array_val['evenement_resume_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_texte'], "text"),
													func::GetSQLValueString($_array_val['evenement_texte_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_organisateur'], "text"),
													func::GetSQLValueString($_array_val['evenement_organisateur_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_coorganisateur'], "text"),
													func::GetSQLValueString($_array_val['evenement_coorganisateur_en'], "text"),
													func::GetSQLValueString($_array_val['evenement_rubrique'], "int"),
													func::GetSQLValueString($_array_val['evenement_image'], "text"),
													func::GetSQLValueString($_array_val['evenement_facebook'], "int"),
													func::GetSQLValueString($_array_val['evenement_editeur_id'], "int"),
													func::GetSQLValueString($_array_val['evenement_editeur_ip'], "text"),
													func::GetSQLValueString($_array_val['evenement_externe'], "int"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			

			return $_id;
		}	
	}

	/**
	* delete_event suppression d'un evenement
	* @param $_id
	*/
	function delete_event($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$deleteEventSQL ="DELETE FROM ".TB."evenements WHERE evenement_id = '".$_id."'";
			$delete_event_query = mysql_query($deleteEventSQL) or die(mysql_error());
			$deleteSessionsSQL ="DELETE FROM ".TB."sessions WHERE evenement_id = '".$_id."'";
			$delete_session_query = mysql_query($deleteSessionsSQL) or die(mysql_error());
		}
	}

	/**
	* get_events_organism récupération des événements d'un organisme à venir pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_events_organism($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauDesEvents=array();

			$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."sessions AS sps, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s  AND session_fin_datetime >=NOW() AND spg.groupe_id=spr.rubrique_groupe_id AND sps.evenement_id=spe.evenement_id GROUP BY spe.evenement_id", 
							func::GetSQLValueString($_id, "int"));
			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauDesEvents[] = $row['evenement_id']; 
			}
			return $tableauDesEvents;
		}
	}

	/**
	* get_events_partages récupération des événements partagés à venir pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_events_partages($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauDesEvents=array();
		    $aujourdhui = date("Y-m-d").' 00:00:00';
			
			$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rel_evenement_rubrique as spre, ".TB."groupes as spg, ".TB."sessions AS sps WHERE spe.evenement_statut=3 AND evenement_datetime >=%s  AND spre.evenement_id=spe.evenement_id AND spg.groupe_id=spre.groupe_id AND spg.groupe_organisme_id=%s AND sps.evenement_id=spe.evenement_id GROUP BY spe.evenement_id", 
									func::GetSQLValueString($aujourdhui, "text"),
									func::GetSQLValueString($_id, "int"));

			$res = mysql_query($sql)or die(mysql_error());
		  
		  	while($row = mysql_fetch_array($res)){ 
				$tableauDesEvents[] = $row['evenement_id']; 
			}
			return $tableauDesEvents;
		}
	}
	
	/**
	* get_title affiche le titre d'un événement sur le front office en fonction de la langue
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function get_title($row, $lang){
		if($lang=="fr"){
			$titre = $row['evenement_titre'];
		}
		else{
			$titre = $row['evenement_titre_en'];
		}

		return $titre;
	}

	/**
	* get_organisateur affiche l'organisateur d'un événement sur le front office en fonction de la langue
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function get_organisateur($row, $lang){
		if($lang=="fr"){
			$organisateur = $row['evenement_organisateur'];
		}
		else{
			$organisateur = $row['evenement_organisateur_en'];
		}

		return $organisateur;
	}

	/**
	* affiche_resume affiche le résumé d'un événement sur le front office en fonction de la langue et retourne le résumé facebook
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function affiche_resume($row, $lang){
		if($lang=="fr"){
			$resumeGeneral = explode(" ",strip_tags($row['evenement_texte']));
			$resume = explode(" ",strip_tags($row['evenement_texte'],'<br>'));
		}
		else{
			$resumeGeneral = explode(" ",strip_tags($row['evenement_texte_en']));
			$resume = explode(" ",strip_tags($row['evenement_texte_en'],'<br>'));
		}

		$resumeFacebook = "";
		$borne = 15;
		$bornefacebook = 15;
		if (count($resume)<15){
			$borne = count($resume);
		}
		
		if (count($resumeGeneral)<15){
			$bornefacebook = count($resumeGeneral);
		}

		if($row['evenement_image']==""){
			$borne=60; 
			if (count($resume)<60){
				$borne = count($resume);
			}
		}
		
		if($row['evenement_image']==""){
			$bornefacebook=60; 
			if (count($resumeGeneral)<60){
				$bornefacebook = count($resumeGeneral);
			}
		}

		for($i = 0 ; $i < $borne ; $i++){
			if($i != ($borne-1)){
				echo $resume[$i]." ";
			}
			else{
				echo $resume[$i]."... &nbsp;";
			}
		}

		for($i = 0 ; $i < $bornefacebook ; $i++){
			if($i != ($bornefacebook-1)){
				str_replace('"','', $resumeGeneral[$i]);
				$resumeFacebook .= $resumeGeneral[$i]." ";
			}
			else{
				str_replace('"','', $resumeGeneral[$i]);
				$resumeFacebook .= $resumeGeneral[$i]."... &nbsp;";
			}
		}

		return $resumeFacebook;
	}

	/**
	* get_fin_event récupère la date de fin d'événement au format datetime
	* @param $_id => id de l'événement
	*/
	function get_fin_event($_id=1){
		$this->evenement_db->connect_db();
		$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($_id, "int"));
		$ressessions = mysql_query($sqlsessions) or die(mysql_error());
		$finEvenement="1980-01-01 00:00:00";
		while($rowsession = mysql_fetch_array($ressessions)){
			if($rowsession['session_fin_datetime']>$finEvenement){
				$finEvenement = $rowsession['session_fin_datetime'];
			}
		}
		return $finEvenement;
	}

	/**
	* get_events_months récupération des différents mois des événements à venir du front office 
	* @param $evenements => tableau contenant l'ensemble des id des événements publiés à venir
	*/
	function get_events_months($evenements){
		$this->evenement_db->connect_db();
		$objDateTime = date("Y-m-d H:i:s", time());
		$tableauTest = array();
		$tableauMois = array();
		$indice = 0;
		foreach($evenements as $evenement){
			$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($evenement, "int"));
			$ressessions = mysql_query($sqlsessions) or die(mysql_error());
			$finEvenement="1980-01-01 00:00:00";
			while($rowsession = mysql_fetch_array($ressessions)){
				if($rowsession['session_fin_datetime']>$finEvenement){
					$finEvenement = $rowsession['session_fin_datetime'];
				}
			}
			if($finEvenement>$objDateTime){
				$sql = sprintf("SELECT EXTRACT(YEAR_MONTH FROM evenement_datetime) as leTest, EXTRACT(MONTH FROM evenement_datetime) as leMois, EXTRACT(YEAR FROM evenement_datetime) as lAnnee FROM ".TB."evenements WHERE evenement_id=%s", 
							func::GetSQLValueString($evenement, "int"));
				$res = mysql_query($sql)or die(mysql_error());
				$row = mysql_fetch_array($res);

				if(!in_array($row['leTest'], $tableauTest)){
					$tableauMois[$indice]['mois'] = $row['leMois'];
					$tableauMois[$indice]['annee'] = $row['lAnnee'];
					$tableauMois[$indice]['unique'] = $row['leTest'];
					$tableauTest[]=$row['leTest'];
					$indice++;
				}
			}
		}
		return $tableauMois;
	}

	/**
	* get_event_unique_month récupére le mois unique (année+mois) d'un événement
	* @param $_id => id de l'événement
	*/
	function get_event_unique_month($_id){
		$this->evenement_db->connect_db();
		$sql = sprintf("SELECT EXTRACT(YEAR_MONTH FROM evenement_datetime) as moisUnique FROM ".TB."evenements WHERE evenement_id=%s", 
							func::GetSQLValueString($_id, "int"));
		$res = mysql_query($sql)or die(mysql_error());
		$row = mysql_fetch_array($res);

		return $row['moisUnique'];
	}


	/**
	* get_event_infos récupére les infos pour le détail d'un événement
	* @param $_id => id de l'événement
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos($_id){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$sql = sprintf("SELECT evenement_titre FROM ".TB."evenements WHERE evenement_id=%s", 
							func::GetSQLValueString($_id, "int"));

		$res = mysql_query($sql)or die(mysql_error());
		$row = mysql_fetch_array($res);
		
		$retour->titre 	= $row['evenement_titre'];
		return json_encode($retour);
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
	
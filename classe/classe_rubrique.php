<?php

include_once('../vars/config.php');
include_once('classe_connexion.php');
include_once('classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Rubrique {
	
	var $evenement_db	= NULL;
	var $id				= NULL;
	// sert à mémoriser si l'updater a été appelé
	// la variable est statique, ainsi elle sera valable quel que soit le nombre de fois ou on appelle la classe.
	//  ainsi toutes les fonctions d'insertion, mise à jour suppression seront appelées au moment de la création de la classe
	static $updated		= false;
	
	/**
	* GESTION DES RUBRIQUES
	*
	*
	*/
	function rubrique($_array_val=NULL, $_id=NULL){
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
	* get_rubrique_event récupération des infos de rubrique d'un événement partagé pour le front
	* @param $_id => id de l'evenement
	*/
	function get_rubrique_event_partage($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$sqlRubrique = sprintf("SELECT * FROM ".TB."rel_evenement_rubrique AS spre, ".TB."rubriques AS spr WHERE spre.rubrique_id=spr.rubrique_id AND spre.evenement_id=%s", GetSQLValueString($_id, "int"));
			$resRubrique = mysql_query($sqlRubrique)or die(mysql_error());
			$rowRubrique = mysql_fetch_array($resRubrique);
			return $rowRubrique;
		}
	}

	/**
	* get_rubriques_organism récupération des rubriques des événements à venir d'un organisme pour le front office
	* @param $_id => id de l'organisme
	* @param $debut => date du jour
	*/
	function get_rubriques_organism($_id=1,$debut){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauRubriques=array();
			$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."sessions AS sps, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_id=sps.evenement_id AND spe.evenement_statut=3 AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s  AND session_fin >=%s AND spg.groupe_id=spr.rubrique_groupe_id GROUP BY spe.evenement_rubrique", 
									func::GetSQLValueString($_id, "int"),
									func::GetSQLValueString($debut, "int"));
			
			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauRubriques[]=$row['rubrique_id'];
			}
			return $tableauRubriques;
		}
	}

	/**
	* get_rubriques_partages récupération des rubriques des événements partagés à venir d'un organisme pour le front office
	* @param $_id => id de l'organisme
	* @param $debut => date du jour
	*/
	function get_rubriques_partages($_id=1,$debut){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauRubriques=array();
		    $sql = sprintf("SELECT spre.rubrique_id FROM ".TB."evenements AS spe, ".TB."sessions AS sps, ".TB."rel_evenement_rubrique as spre, ".TB."groupes as spg WHERE spe.evenement_id=sps.evenement_id AND spe.evenement_statut=3 AND session_fin >=%s  AND spre.evenement_id=spe.evenement_id AND spg.groupe_id=spre.groupe_id AND spg.groupe_organisme_id=%s GROUP BY spre.rubrique_id", 
									func::GetSQLValueString($debut, "int"),
									func::GetSQLValueString($_id, "int")
									);

			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauRubriques[]=$row['rubrique_id'];
			}
			return $tableauRubriques;
		}
	}
}
	
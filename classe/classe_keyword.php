<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Keyword {
	
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
	function keyword($_array_val=NULL, $_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}


	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets	
		if(isset($_array_val['update']) && ($_array_val['update'] == 'update' || $_array_val['update'] == 'create')){
			$this->create_keyword($_array_val,$_id);
		}

		if(isset($_array_val['update']) && $_array_val['update'] == 'delete'){
			$this->delete_keyword($_id);
		}

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}

	/**
	* create_keyword creation ou modification d'un mot-clé
	* @param $_array_val
	* @param $_id
	*/
	function create_keyword($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();
		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."keywords SET keyword_nom=%s WHERE keyword_id=%s",
											func::GetSQLValueString($_array_val['keyword_nom'], "text"),
											func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."keywords (keyword_nom, keyword_organisme_id) VALUES (%s, %s)",
											func::GetSQLValueString($_array_val['keyword_nom'], "text"),
											func::GetSQLValueString($_array_val['organisme_id'], "int"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			return $_id;
		}	
	}

	/**
	* delete_keyword suppression d'un mot-clé
	* @param $_id
	*/
	function delete_keyword($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$deleteKeywordSQL ="DELETE FROM ".TB."keywords WHERE keyword_id = '".$_id."'";
			$delete_keyword_query = mysql_query($deleteKeywordSQL) or die(mysql_error());
		}
	}

	/**
	* get_keywords_organism récupération des rubriques des événements à venir d'un organisme pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_keywords_organism($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauKeywords=array();
			$sql = sprintf("SELECT spk.keyword_id FROM ".TB."evenements AS spe, ".TB."rel_evenement_keyword AS sprk, ".TB."sessions AS sps, ".TB."keywords AS spk, ".TB."groupes AS spg WHERE spe.evenement_id = sps.evenement_id AND sprk.evenement_id=spe.evenement_id AND spe.evenement_statut=3 AND sprk.keyword_id=spk.keyword_id AND spg.groupe_organisme_id=%s  AND session_fin_datetime >=NOW() GROUP BY spk.keyword_id", 
									func::GetSQLValueString($_id, "int"));
			
			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauKeywords[]=$row['keyword_id'];
			}
			return $tableauKeywords;
		}
	}

	/**
	* get_keywords_partages récupération des rubriques des événements partagés à venir d'un organisme pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_keywords_partages($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauKeywords=array();
		    $sql = sprintf("SELECT spk.keyword_id FROM ".TB."evenements AS spe, ".TB."rel_evenement_keyword AS sprk, ".TB."sessions AS sps, ".TB."keywords AS spk, ".TB."rel_evenement_rubrique as spre, ".TB."groupes as spg WHERE spe.evenement_id = sps.evenement_id AND sprk.evenement_id=spe.evenement_id AND sprk.keyword_id=spk.keyword_id AND spe.evenement_statut=3 AND session_fin_datetime >=NOW()  AND spre.evenement_id=spe.evenement_id AND spg.groupe_id=spre.groupe_id AND spg.groupe_organisme_id=%s GROUP BY spk.keyword_id", 
									func::GetSQLValueString($_id, "int")
									);

			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauKeywords[]=$row['keyword_id'];
			}
			return $tableauKeywords;
		}
	}
}
	
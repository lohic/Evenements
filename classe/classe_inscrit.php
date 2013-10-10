<?php

include_once('../vars/config.php');
include_once('classe_connexion.php');
include_once('classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Inscrit {
	
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
	function inscrit($_array_val=NULL, $_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}


	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets	
		
		if(isset($_array_val['update']) && ($_array_val['update'] == 'update' || $_array_val['update'] == 'create')){
			$this->create_inscrit($_array_val,$_id);
		}

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}

	/**
	* create_keyword creation ou modification d'un mot-clé
	* @param $_array_val
	* @param $_id
	*/
	function create_inscrit($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();
		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."inscrits SET keyword_nom=%s WHERE keyword_id=%s",
											func::GetSQLValueString($_array_val['keyword_nom'], "text"),
											func::GetSQLValueString($_id,"int"));
			$update_query	= mysql_query($updateSQL) or die(mysql_error());			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."inscrits (keyword_nom) VALUES (%s)",
											func::GetSQLValueString($_array_val['keyword_nom'], "text"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			return $_id;
		}	
	}
}
	
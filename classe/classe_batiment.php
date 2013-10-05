<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Batiment {
	
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
	function batiment($_array_val=NULL, $_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}


	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets	
		
		if(isset($_array_val['update']) && ($_array_val['update'] == 'update' || $_array_val['update'] == 'create')){
			$this->create_batiment($_array_val,$_id);
		}

		if(isset($_array_val['update']) && $_array_val['update'] == 'delete'){
			$this->delete_batiment($_id);
		}

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}

	/**
	* create_batiment creation ou modification d'un batîment
	* @param $_array_val
	* @param $_id
	*/
	function create_batiment($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();
		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."codes_batiments SET code_batiment_nom=%s, code_batiment_adresse=%s, code_batiment_editeur_ip=%s, code_batiment_editeur_id=%s WHERE code_batiment_id=%s",
											func::GetSQLValueString($_array_val['code_batiment_nom'], "text"),
											func::GetSQLValueString($_array_val['code_batiment_adresse'], "text"),
											func::GetSQLValueString($_SERVER["REMOTE_ADDR"], "text"),
											func::GetSQLValueString($_SESSION['id'], "int"),
											func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."codes_batiments (code_batiment_nom, code_batiment_adresse, code_batiment_organisme, code_batiment_editeur_ip, code_batiment_editeur_id) VALUES (%s, %s, %s, %s, %s)",
											func::GetSQLValueString($_array_val['code_batiment_nom'], "text"),
											func::GetSQLValueString($_array_val["code_batiment_adresse"], "text"),
											func::GetSQLValueString($_array_val['organisme_id'], "int"),
											func::GetSQLValueString($_SERVER["REMOTE_ADDR"], "text"),
											func::GetSQLValueString($_SESSION['id'], "int"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			return $_id;
		}	
	}

	/**
	* delete_batiment suppression d'un bâtiment
	* @param $_id
	*/
	function delete_batiment($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$deleteBatimentSQL ="DELETE FROM ".TB."codes_batiments WHERE code_batiment_id = '".$_id."'";
			$delete_batiment_query = mysql_query($deleteBatimentSQL) or die(mysql_error());
		}
	}
}
	
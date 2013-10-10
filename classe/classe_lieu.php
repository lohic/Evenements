<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Lieu {
	
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
	function lieu($_array_val=NULL, $_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}


	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets	
		
		if(isset($_array_val['update']) && ($_array_val['update'] == 'update' || $_array_val['update'] == 'create')){
			$this->create_lieu($_array_val,$_id);
		}

		if(isset($_array_val['update']) && $_array_val['update'] == 'delete'){
			$this->delete_lieu($_id);
		}

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}

	/**
	* create_lieu creation ou modification d'un lieu
	* @param $_array_val
	* @param $_id
	*/
	function create_lieu($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();
		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."lieux SET lieu_nom=%s, lieu_editeur_ip=%s, lieu_editeur_id=%s WHERE lieu_id=%s",
											func::GetSQLValueString($_array_val['lieu_nom'], "text"),
											func::GetSQLValueString($_SERVER["REMOTE_ADDR"], "text"),
											func::GetSQLValueString($_SESSION['id'], "int"),
											func::GetSQLValueString($_id,"int"));																						
			$update_query	= mysql_query($updateSQL) or die(mysql_error());	

			//suppression des liaisons avec les organismes
			$sql="DELETE FROM sp_rel_lieu_organisme WHERE lieu_id = '".$_id."'";
			mysql_query($sql) or die(mysql_error());

			//création des nouvelles liaisons avec les organismes
			for ($i = 0; $i < count($_array_val['organismes']); $i++) {
				$sqlinsert	= sprintf("INSERT INTO sp_rel_lieu_organisme (lieu_id, organisme_id) VALUES (%s, %s)",
											func::GetSQLValueString($_id, "int"),
											func::GetSQLValueString($_array_val['organismes'][$i], "int"));
				mysql_query($sqlinsert) or die(mysql_error());
			}			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."lieux (lieu_nom, lieu_editeur_ip, lieu_editeur_id) VALUES (%s, %s, %s)",
											func::GetSQLValueString($_array_val['lieu_nom'], "text"),
											func::GetSQLValueString($_SERVER["REMOTE_ADDR"], "text"),
											func::GetSQLValueString($_SESSION['id'], "int"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			$_id = mysql_insert_id();
			for ($i = 0; $i < count($_array_val['organismes']); $i++) {
				$sqlinsert	= sprintf("INSERT INTO sp_rel_lieu_organisme (lieu_id, organisme_id) VALUES (%s, %s)",
											func::GetSQLValueString($_id, "int"),
											func::GetSQLValueString($_array_val['organismes'][$i], "int"));
				mysql_query($sqlinsert) or die(mysql_error());
			}
			return $_id;
		}	
	}

	/**
	* delete_lieu suppression d'un lieu
	* @param $_id
	*/
	function delete_lieu($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$deleteLieuSQL ="DELETE FROM ".TB."lieux WHERE lieu_id = '".$_id."'";
			$delete_lieu_query = mysql_query($deleteLieuSQL) or die(mysql_error());

			$sql="DELETE FROM sp_rel_lieu_organisme WHERE lieu_id = '".$_id."'";
			mysql_query($sql) or die(mysql_error());
		}
	}
}
	
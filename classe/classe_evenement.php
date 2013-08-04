<?php

include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe_fonctions.php');
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
	
	
	function get_default_template($_template=NULL,$_image=NULL){
		// exemple pour le fonctionnement d'un template
		// 
		// 
		$contents = "<!--debut du contenu-->";
		$contents .= '<link rel="template_front/'.$_template.'/style.css">';

		$image = $_image;

		ob_start();

		include(REAL_LOCAL_PATH.'template_front/'.$_template.'/index.php')

		$contents .= ob_get_contents();

		ob_end_clean();

		return $contents;

	}

}
	
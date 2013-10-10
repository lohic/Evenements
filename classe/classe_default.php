<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Default {
	
	var $evenement_db	= NULL;
	var $id				= NULL;
	// sert à mémoriser si l'updater a été appelé
	// la variable est statique, ainsi elle sera valable quel que soit le nombre de fois ou on appelle la classe.
	//  ainsi toutes les fonctions d'insertion, mise à joru suppression serotn appelées au moment de la création de la classe
	static $updated		= false;
	
	/**
	* GESTION DES ORGANISMES
	*
	*
	*/
	function default($_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

		if(self::$updated == false){
			$this->updater();
		}
	}


	function updater(){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets
		//
		// ici on peut aussi normaliser les données à l'aide de :
		func::GetSQLValueString($valeur, 'int');
		func::GetSQLValueString($valeur, 'text');
		// ...etc		
		
		if(isset($_POST['create']) && $_POST['create'] == 'default'){
			create_default($_array_val);
		}


		// ...etc
		// 

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}


	/**
	* create_default creation ou modification d'un élément default
	* @param $_array_val
	* @param $_id (facultatif, si indiqué alors on modifie)
	*/
	function create_default($_array_val=NULL,$_id=NULL){
		if(!empty($_id)){
			// alors on met à jour
		}else{
			// alors on crée
		}

	}

	
	/**
	* create_organisme creation ou modification d'un organisme
	* @param $_array_val
	* @param $_id
	*/
	function create_default($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."organisme_tb SET nom=%s, type=%s, google_analytics_id=%s WHERE id=%s",
													func::GetSQLValueString($_array_val['nom'],					"text"),
													func::GetSQLValueString($_array_val['type'],					"text"),
													func::GetSQLValueString($_array_val['google_analytics_id'],	"text"),
													func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());
			
			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."organisme_tb (nom, type, google_analytics_id) VALUES (%s,%s,%s)",
													func::GetSQLValueString($_array_val['nom'],					"text"),
													func::GetSQLValueString($_array_val['type'],					"text"),
													func::GetSQLValueString($_array_val['google_analytics_id'], 	"text"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			

			return $_id;
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
	
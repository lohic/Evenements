<?php

//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');


class Organisme {
	
	var $evenement_db		= NULL;
	var $id				= NULL;

	// sert à mémoriser si l'updater a été appelé
	// la variable est statique, ainsi elle sera valable quel que soit le nombre de fois ou on appelle la classe.
	//  ainsi toutes les fonctions d'insertion, mise à jour suppression seront appelées au moment de la création de la classe
	static $updated		= false;
	
	/**
	* GESTION DES ORGANISMES
	*
	*
	*/
	function organisme($_id=NULL){
		global $connexion_info;
		$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);
		if(self::$updated == false){
			$this->updater($_array_val,$_id);
		}
	}
	

	function updater($_array_val,$_id){
		// ici on place toutes les fonctions qui servent à mettre à jour ou à créer des objets	
		if(isset($_array_val['update']) && ($_array_val['update'] == 'update' || $_array_val['update'] == 'create')){
			$this->create_organisme($_array_val,$_id);
		}

		if(isset($_array_val['update']) && $_array_val['update'] == 'delete'){
			$this->delete_organisme($_id);
		}

		// on garde en mémoire le fait que la mise à jour a bien eu lieu
		self::$updated = true;
	}

	/**
	* create_organisme creation ou modification d'un organisme
	* @param $_array_val
	* @param $_id
	*/
	function create_organisme($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."organismes SET organisme_nom=%s, organisme_google_analytics_id=%s, organisme_couleur=%s, 
																   organisme_banniere_chemin=%s, organisme_banniere_lien=%s, organisme_logo_chemin=%s,
																   organisme_mentions, organisme_url_front, organisme_image_billet, organisme_url_image WHERE id=%s",
													func::GetSQLValueString($_array_val['organisme_nom'], "text"),
													func::GetSQLValueString($_array_val['google_analytics_id'], "text"),
													func::GetSQLValueString($_array_val['organisme_couleur'], "text"),
													func::GetSQLValueString($_array_val['organisme_banniere_chemin'], "text"),
													func::GetSQLValueString($_array_val['organisme_banniere_lien'], "text"),
													func::GetSQLValueString($_array_val['organisme_logo_chemin'], "text"),
													func::GetSQLValueString($_array_val['organisme_mentions'], "text"),
													func::GetSQLValueString($_array_val['organisme_url_front'], "text"),
													func::GetSQLValueString($_array_val['organisme_image_billet'], "text"),
													func::GetSQLValueString($_array_val['organisme_url_image'], "text"),
													func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());
			
			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."organismes (organisme_nom, organisme_google_analytics_id, organisme_couleur, 
																	 organisme_banniere_chemin, organisme_banniere_lien, organisme_logo_chemin,
																	 organisme_mentions, organisme_url_front, organisme_image_billet, organisme_url_image) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
													func::GetSQLValueString($_array_val['organisme_nom_creation'], "text"),
													func::GetSQLValueString($_array_val['google_analytics_id_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_couleur_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_banniere_chemin_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_banniere_lien_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_logo_chemin_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_mentions_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_url_front_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_image_billet_creation'], "text"),
													func::GetSQLValueString($_array_val['organisme_url_image_creation'], "text"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			

			return $_id;
		}	
	}

	/**
	* delete_organisme suppression d'un organisme
	* @param $_id
	*/
	function delete_organisme($_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$deleteOrganismeSQL ="DELETE FROM ".TB."organismes WHERE organisme_id = '".$_id."'";
			$delete_organisme_query = mysql_query($deleteOrganismeSQL) or die(mysql_error());
		}
	}
	
	/*
	@ creation ou modification d'un groupe utilisateur
	@
	@
	*/
	function create_user_groupe($_array_val,$_id=NULL){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			//MODIFICATION
			$updateSQL 		= sprintf("UPDATE ".TB."user_groupes_tb SET libelle=%s, type=%s, id_organisme=%s WHERE id=%s",
													func::GetSQLValueString($_array_val['nom'],					"text"),
													func::GetSQLValueString($_array_val['type'],					"text"),
													func::GetSQLValueString($_array_val['id_organisme'],	"text"),
													func::GetSQLValueString($_id,"int"));
																										
			$update_query	= mysql_query($updateSQL) or die(mysql_error());
			
			
		}else{
			//CREATION
			$insertSQL 		= sprintf("INSERT INTO ".TB."user_groupes_tb (libelle, type, id_organisme) VALUES (%s,%s,%s)",
													func::GetSQLValueString($_array_val['nom'],					"text"),
													func::GetSQLValueString($_array_val['type'],					"text"),
													func::GetSQLValueString($_array_val['id_organisme'], 	"text"));
			$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			
			$_id = mysql_insert_id();
			

			return $_id;
		}	
	}
	
	/*
	@ RECUPERE LES INFOS DE L'ORGANISME DU FRONT CONSULTE
	@
	@
	*/
	function get_organisme(){
		$this->evenement_db->connect_db();

		$sqlOrganisme = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
		$resOrganisme = mysql_query($sqlOrganisme)or die(mysql_error());
		$rowOrganisme = mysql_fetch_array($resOrganisme);
		return $rowOrganisme;
	}

	/**
	* get_URL_front_from_group Récupère l'URL du front en fonction du groupe auquel appartient l'événement
	* @param $_id => id du groupe
	* @return $rowUrl['organisme_url_front'] => l'URL du front
	*
	*/
	function get_URL_front_from_group($_id){
		$sqlUrl = sprintf("SELECT organisme_url_front FROM ".TB."groupes, ".TB."organismes WHERE groupe_id =%s AND groupe_organisme_id=organisme_id", func::GetSQLValueString($_id, "int"));
		$resUrl = mysql_query($sqlUrl) or die(mysql_error());
		$rowUrl = mysql_fetch_array($resUrl);
		return $rowUrl['organisme_url_front'];
	}

	
	
	/*
	@ RECUPERE LA LISTE DES ORGANISMES
	@
	@
	*/
	function get_organisme_edit_liste(){
		$this->evenement_db->connect_db();

		$sql_organisme		= sprintf('SELECT * FROM '.TB.'organisme_tb');
		$sql_organisme_query = mysql_query($sql_organisme) or die(mysql_error());
		
		$i = 0;

		while ($organisme_item = mysql_fetch_assoc($sql_organisme_query)){
						
			$class				= 'listItemRubrique'.($i+1);
			$id					= $organisme_item['id'];
			$nom				= $organisme_item['nom'];
			$type				= $organisme_item['type'];
			$google_analytics_id= $organisme_item['google_analytics_id'];
			$user_level			= $this->get_admin_level();
	
			global $typeTab;
			
			include('../structure/admin-organisme-list-bloc.php');
			
			$i = ($i+1)%2;
			
		}
	}
	
	function get_organisme_liste(){
		$this->evenement_db->connect_db();

		$sql_organisme		= sprintf('SELECT * FROM '.TB.'organisme_tb');
		$sql_organisme_query = mysql_query($sql_organisme) or die(mysql_error());
	
		
		$liste = array();

		while ($organisme_item = mysql_fetch_assoc($sql_organisme_query)){
						
			$id					= $organisme_item['id'];
			$nom				= $organisme_item['nom'];
	
			$liste[$id] = $nom;
			
		}
		
		return $liste;
	}
	
	
	/*
	@ RECUPERE LA LISTE DES GROUPES D'UTILISATEURS
	@
	@
	*/
	function get_user_groupe_edit_liste(){
		$this->evenement_db->connect_db();

		$sql_user_groupe		= sprintf('SELECT * FROM '.TB.'user_groupes_tb');
		$sql_user_groupe_query = mysql_query($sql_user_groupe) or die(mysql_error());
		
		$i = 0;

		while ($user_groupe_item = mysql_fetch_assoc($sql_user_groupe_query)){
						
			$class				= 'listItemRubrique'.($i+1);
			$id					= $user_groupe_item['id'];
			$nom				= $user_groupe_item['libelle'];
			$type				= $user_groupe_item['type'];
			$id_organisme		= $user_groupe_item['id_organisme'];
			$organismes			= $this->get_organisme_liste();
			$user_level			= $this->get_admin_level();
			
			global $typeTab;
			
			include('../structure/admin-user_groupe-list-bloc.php');
			
			$i = ($i+1)%2;
			
		}
	}
	
	/*
	@ SUPPRIME UN ORGANISME
	@
	@
	*/
	function suppr_organisme($id=NULL){
		if(isset($id)){
			$this->evenement_db->connect_db();

			$supprSQL		= sprintf("DELETE FROM ".TB."organisme_tb WHERE id=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
			
			
			$sql_user_groupe		= sprintf("SELECT * FROM ".TB."user_groupes_tb WHERE id_organisme=%s", func::GetSQLValueString($id,'int'));
			$sql_user_groupe_query = mysql_query($sql_user_groupe) or die(mysql_error());

	
			while ($user_groupe_item = mysql_fetch_assoc($sql_user_groupe_query)){
							
				$id					= $user_groupe_item['id'];
				
				$this->suppr_user_groupe($id);
			}
		}
	}
	
	/*
	@ SUPPRIME UN USER_GROUPE
	@
	@
	*/
	function suppr_user_groupe($id=NULL){
		if(isset($id)){
			$this->evenement_db->connect_db();

			$supprSQL		= sprintf("DELETE FROM ".TB."user_groupes_tb WHERE id=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
			
			$supprSQL		= sprintf("DELETE FROM ".TB."rel_user_groupe_tb WHERE id_groupe=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
			
			$supprSQL		= sprintf("DELETE FROM ".TB."rel_user_groupe_groupe_tb WHERE id_groupe=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
			
			$supprSQL		= sprintf("DELETE FROM ".TB."rel_template_groupe_tb WHERE id_groupe=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
			
			$supprSQL		= sprintf("DELETE FROM ".TB."rel_cat_actu_groupe_tb WHERE id_groupe=%s", func::GetSQLValueString($id,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
		}
	}
	
	
	/*
	@ RECUPERATION DU NIVEAU D'ADMINISTRATION
	@
	@
	*/
	function get_admin_level(){
		$sql_liste_level	= 'SELECT * FROM '.TB.'user_level_tb ORDER BY level';
		$sql_liste_level_query = mysql_query($sql_liste_level) or die(mysql_error());
		
		$retour = array();
		
		while ($level = mysql_fetch_assoc($sql_liste_level_query)){
			$retour[ $level['level']]	= $level['libelle'];
		}
		
		return $retour;
	}

}
	
	

?>
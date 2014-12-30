<?php

include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
//include_once('classe_spuser.php');
include_once(REAL_LOCAL_PATH.'classe/classe_spuser_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');
//include_once(REAL_LOCAL_PATH.'classe/connexion_vars.php');

class Core {
	var $isAdmin		= false;
	var $isSuperAdmin	= false;
	var $evenement_db	= NULL;
	//var $news_db		= NULL;
	//
	var $user;
	var $user_info		= NULL;
	var $groups_id		= NULL;
	
	/* PREPARATION DU CONTENU DE LA PAGE */
	function core(){ 
		ini_set('arg_separator.output', '&amp;');
		session_start();
		date_default_timezone_set('UTC');
		
		global $evenement_cInfo;
		global $news_cInfo;
		global $connexion_info;

		//$this->evenement_db = new connexion($evenement_cInfo['server'],$evenement_cInfo['user'],$evenement_cInfo['password'],$evenement_cInfo['db']);
		$this->evenement_db = new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);
		//$this->news_db		= new connexion($news_cInfo['server'],$news_cInfo['user'],$news_cInfo['password'],$news_cInfo['db']);
		//$this->user 		= new user($this->news_db);
		$this->user 		= new spuser($this->evenement_db);

		$this->user_info	= $this->user->get_user_info();  
	    
	    if(isset($this->user_info->groups)){
			$this->groups_id	= array_keys($this->user_info->groups);
		}
		
		if(isset($_POST['id_actual_group'])){
			$_SESSION['id_actual_group'] = $_POST['id_actual_group'];
		}else if(isset($_SESSION['id_actual_group'])){
			$_SESSION['id_actual_group'] = $_SESSION['id_actual_group'];
		}else{
			$_SESSION['id_actual_group'] = 	$this->groups_id[0];
		}
	
		if($this->user_info){
			$this->isAdmin		= $this->user_info->isAdmin;
			$this->userLevel	= $this->user_info->userLevel;
		}else{
			$this->isAdmin		= false;
			$this->userLevel	= false;
		}
	}
}

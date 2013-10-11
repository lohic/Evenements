<?php


/*
LDAP :
emile.boutmy
boutmy

EMAIL :
login
password
*/

include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');
include_once(REAL_LOCAL_PATH.'vars/constantes_vars.php');
//include_once('../vars/statics_vars.php');

class Spuser {
	var $connexion;

	var $evenement_db	= NULL;
	var $isAdmin;
	//var $isSuperAdmin;
	var $info;

	var $id;
	var $login;
	var $password;
	var $type;
	var $nom;
	var $prenom;
	var $email;
	var $groupe; 
	var $userLevel;
	
	function spuser($connexion){

		$this->isAdmin		= false;
		//$this->isSuperAdmin = false;

		$this->id			= NULL;
		$this->login		= NULL;
		$this->password		= NULL;
		$this->type			= NULL;
		$this->nom			= NULL;
		$this->prenom		= NULL;
		$this->email		= NULL;
		$this->groupe		= NULL; 
		$this->userLevel	= NULL;
		
		$this->LDAP			= NULL;
		
		$this->connexion = $connexion;

		/*
		@ on verifie qu'on ne doit pas se déconnecter 
		*/
		if(isset($_POST['logout']) && $_POST['logout']==true){
			$this->logout();
		}
		
		/*
		@ on verifie qu'un login existe
		@ on recrée un objet user à partir des variables de sessions enregistrées 
		*/
		else if(isset($_SESSION['login'])){

			//echo "SESSION OK ".$_SESSION['login'];
			
			$this->id			= $_SESSION['id'];
			$this->login		= $_SESSION['login'];
			$this->type			= $_SESSION['type'];
			$this->nom			= $_SESSION['nom'];
			$this->prenom		= $_SESSION['prenom'];
			$this->email		= $_SESSION['email'];
			$this->groupe		= $_SESSION['groupe'];
			$this->isSuperAdmin	= $_SESSION['isSuperAdmin']; 
			$this->userLevel	= $_SESSION['userLevel'];
			$this->LDAP			= $_SESSION['LDAP'];
			
			if(!$this->isAuthorised()){
				$this->logout();
			}
			if($this->userLevel<=8){
				$this->isAdmin	= true;
			}else{
				$this->isAdmin	= false;
			}

		}
		/*
		@ on verifie qu'un login et un mot de passe existent
		@ on recrée un objet user à partir des variables de sessions enregistrées 
		*/
		else if(isset($_POST['login']) && isset($_POST['password'])){

			// on verifie de quel type de compte il s'agit
			$ldapConnected = $this->isLDAP($_POST['login'],$_POST['password']);
				
			if(!$ldapConnected){
				//$type = $this->isLDAPorMAIL($_POST['login']); 
				$this->check_login($_POST['login'],$_POST['password']);  
			}

		}
		
		/*
		@ sinon rien on vérifie bien que la variable admin est false
		*/
		else{
			//echo "USER RIEN";
			$this->isAdmin		= false;
			//$this->isSuperAdmin	= false;
			$this->userLevel	= false;
		}
	}
	
	/*
	@ On vérifie avant tout si il s'agit d'un compte LDAP
	@
	@
	*/
	function isLDAP($login=NULL,$password=NULL){
		
		if(IS_LDAP_SERVER){
			$LDAPinfo = $this->connectLDAP($login,$password);
			  
			if(!empty($LDAPinfo->email)){
				
				$this->connexion->connect_db();
			
				$login_query	= sprintf("SELECT * FROM ".TB."users WHERE user_email=%s", func::GetSQLValueString($LDAPinfo->email, "text")); 
			
				$login_info		= mysql_query($login_query) or die(mysql_error());
				$infoUser		= mysql_fetch_assoc($login_info);
				
				if($infoUser['user_account_type'] == 'ldap'){
					   			
					$this->id			= $infoUser['user_id'];
					$this->login		= $LDAPinfo->login;
					$this->type			= $infoUser['user_type'];
					$this->nom			= $LDAPinfo->nom;
					$this->prenom		= $LDAPinfo->prenom;
					$this->email		= $infoUser['user_email'];
					$this->groupe		= $infoUser['user_groupe'];
					$this->userLevel	= $infoUser['user_type'];
					
					$this->LDAP				= true;
					
					//$this->isSuperAdmin	= $infoUser['user_type']=='super_admin'?true:false;
					
					//$this->isAdmin	= true;
					if($this->userLevel<=8){
						$this->isAdmin	= true;
					}else{
						$this->isAdmin	= false;
					}
		
					$_SESSION['id'] 	= $this->id;
					$_SESSION['login']	= $this->login;
					$_SESSION['type']	= $this->type;
					$_SESSION['nom']	= $this->nom;
					$_SESSION['prenom'] = $this->prenom;
					$_SESSION['email']	= $this->email;
					$_SESSION['groupe']	= $this->groupe;
					$_SESSION['userLevel']	= $this->userLevel;
					
					$_SESSION['LDAP']		= $this->LDAP;
					
					//$_SESSION['isSuperAdmin']	= $this->isSuperAdmin;
					
					return true;
				}else{
					$this->isAdmin		= false;
					//$this->isSuperAdmin = false;
					$this->userLevel	= false;
					return false;
				}
			}
		}else{
			return false;	
		}
	}
		
	/*
	@ fonction de connexion au LDAP	
	@
	@
	*/
	function connectLDAP($login=NULL,$password=NULL){
		// Eléments d'authentification LDAP
		
		
		$retour->info	= NULL;
		$retour->login	= NULL;
		$retour->prenom	= NULL;
		$retour->nom	= NULL;
		$retour->email	= NULL;
		$retour->type	= NULL;
		//$retour->spID	= NULL;
		//$retour->annee	= NULL;
	
		if(isset($login) && isset($password) && $login!="" && $password!=""){
			$login = strtolower($login);
			
			$ldaprdn  = 'uid='.$login.',ou=Users,o=sciences-po,c=fr';
			$ldappass = $password;
	
			
			// Connexion au serveur LDAP
			$ldapconn = ldap_connect("ldap.sciences-po.fr") or die("Impossible de se connecter au serveur LDAP.");
			
			if ($ldapconn) {
				// Authentification au serveur LDAP
				$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
		
				// Vérification de l'authentification
				if ($ldapbind) {
					$retour->info = "ok";
			
					//recuperation des informations
					$sr=ldap_search($ldapconn,"ou=Users, o=sciences-po, c=fr", "uid=".$login);
					$info = ldap_get_entries($ldapconn, $sr);
					
					
					for ($i=0; $i<$info["count"]; $i++) 
					{
						if ( isset($info[$i]["cn"][0]) ){				$retour->login	= $info[$i]["cn"][0]; }
						if ( isset($info[$i]["givenname"][0]) ){		$retour->prenom = $info[$i]["givenname"][0]; }
						if ( isset($info[$i]["sn"][0]) ){				$retour->nom	= $info[$i]["sn"][0]; }
						if ( isset($info[$i]["mail"][0]) ){				$retour->email	= $info[$i]["mail"][0]; }
						if ( isset($info[$i]["employeetype"][0]) ){		$retour->type	= $info[$i]["employeetype"][0]; }
	
					}
										
					//$retour->raw = $info;
					
					ldap_close($ldapconn);
				} else {
					$retour->info = "login_error";
				}
			
			}else{
				$retour->info = "no_connexion";
			}
		}else{
			$retour->info = "no_login";
		}
		return $retour;
	}
	
	/*
	@ Connecte un compte email
	@
	@
	*/
	function check_login($login=NULL,$password=NULL){
		$this->connexion->connect_db();
		
		$login__query	= sprintf("SELECT * FROM sp_users WHERE user_login=%s AND user_password=%s",
																GetSQLValueString($login, "text"),
																GetSQLValueString($password, "text")); 
	
		$login_info		= mysql_query($login__query) or die(mysql_error());
		$infoUser		= mysql_fetch_assoc($login_info);
		$loginuser		= mysql_num_rows($login_info);

		//echo "INFO USER  : ".$infoUser['login'];	

		if($loginuser){
			
			//echo "INFO USER 2  : ".$infoUser['login'];

			$this->id			= $infoUser['user_id'];
			$this->login		= $infoUser['user_login'];
			$this->password		= $infoUser['user_password'];
			$this->type			= $infoUser['user_type'];
			$this->nom			= $infoUser['user_nom'];
			$this->prenom		= $infoUser['user_prenom'];
			$this->email		= $infoUser['user_email'];
			$this->groupe		= $infoUser['user_groupe'];
			$this->userLevel	= $infoUser['user_type'];
			
			$this->LDAP				= false;
			
			//$this->isSuperAdmin	= $infoUser['user_type']=='super_admin'?true:false;


			$_SESSION['id'] 	= $this->id;
			$_SESSION['login']	= $this->login;
			$_SESSION['type']	= $this->type;
			$_SESSION['nom']	= $this->nom;
			$_SESSION['prenom'] = $this->prenom;
			$_SESSION['email']	= $this->email;
			$_SESSION['groupe']	= $this->groupe;
			$_SESSION['userLevel']	= $this->userLevel;
			
			$_SESSION['LDAP']		= $this->LDAP;
			
			//$_SESSION['isSuperAdmin']	= $this->isSuperAdmin;

			//echo "SESSION USER 3  : ".$_SESSION['login'];

			if($this->userLevel<=8){
				$this->isAdmin	= true;
			}else{
				$this->isAdmin	= false;
			}
		}else{
			$this->isAdmin	= false;
		}
	}

	/*
	@ LOGOUT DE L'ADMINISTRATION
	@
	@
	*/
	function logout(){
		$this->id			= NULL;
		$this->login		= NULL;
		$this->type			= NULL;
		$this->nom			= NULL;
		$this->prenom		= NULL;
		$this->email		= NULL;
		$this->groupe		= NULL;
		$this->userLevel	= NULL;             
		$this->LDAP			= NULL;
		
		$this->isAdmin	= false;
		//$this->isSuperAdmin	= false;
		
		$_SESSION = array();
		session_unset();
		session_destroy();
	}

	/*
	@ RECUPERATION DES INFORMATIONS D'UN UTILISATEUR
	@
	@
	*/
	function get_user_info(){ 
		if($this->isAdmin){
			$retour = NULL;

			$retour->id				= $this->id;
			$retour->login			= $this->login;
			$retour->password		= $this->password;
			$retour->type			= $this->type;
			$retour->nom			= $this->nom;
			$retour->prenom			= $this->prenom;
			$retour->email			= $this->email;
			$retour->groupe			= $this->groupe; 
			$retour->userLevel		= $this->userLevel;
			$retour->isAdmin		= $this->isAdmin; 
			$retour->groups			= $this->get_groups();
			$retour->LDAP			= $this->LDAP;
		 	//$retour->isSuperAdmin	= $this->isSuperAdmin;
			
			return $retour;
		}else{ 
			return false;
		}
	}
	
   
	
	/*
	@ edition de destinataire
	@
	@
	*/
	function add_groupe_user($_id_user=NULL, $_array_groupe=NULL){
		if(!empty($_id_user) && !empty($_array_groupe)){
			$this->clean_groupe($_id_user);
			
			
			$this->news_db->connect_db();

			foreach($_array_groupe as $_id_groupe){
				$insertSQL 		= sprintf("INSERT INTO sp_rel_user_groupe (user_id,groupe_id) VALUES (%s,%s)",
														GetSQLValueString($_id_user, "int"),
														GetSQLValueString($_id_groupe, "int"));
				$insert_query	= mysql_query($insertSQL) or die(mysql_error());
			}
		}
	}
	
	/*
	@ edition de destinataire
	@
	@
	*/
	function clean_groupe($_id_user=NULL){
		if(!empty($_id_user)){
			$this->news_db->connect_db();

			$supprSQL		= sprintf("DELETE FROM sp_rel_user_groupe WHERE user_id=%s", GetSQLValueString($_id_user,'int'));
			$suppr_query	= mysql_query($supprSQL) or die(mysql_error());
		}
	}
	
	
	/*
	@ TYPE DE VARIABLES MYSQL ATTENDUES
	@
	@
	*/
	function get_post_value($tab){

		$titre_item_attendu = array('password',	'type',	'nom',	'prenom',	'email',	'groupe',	'account_type');
		$type_item_attendu 	= array('text',		'text',	'text',	'text',		'text',		'text',		'text');
		$nbr_colonnes		= count($titre_item_attendu);	

		foreach($tab as $key => $value){
			for($i=0;$i<$nbr_colonnes;$i++){
				if($key==$titre_item_attendu[$i]){
					$tab[$key] = GetSQLValueString($value,$type_item_attendu[$i]);
					break;
				}
			}
		}
		
		return $tab;
	}

	/*
	@ RETOURNE LA LISTE DES GROUPES D'UTILISATEUR
	@
	@
	*/
	function get_groups(){
		$query	= sprintf("SELECT g.groupe_id as id, g.groupe_libelle as libelle, g.groupe_type as type, g.groupe_organisme_id as id_organisme
							FROM sp_groupes g, sp_users u, sp_rel_user_groupe r
							WHERE r.user_id = u.user_id
							AND r.groupe_id = g.groupe_id
							AND u.user_id = %s", GetSQLValueString($this->id, "int")); 
	
		$result	= mysql_query($query) or die(mysql_error());
				
		while ($info = mysql_fetch_assoc($result)){
			$group[$info['id']] = $info['libelle'];
			$groupOrga[] = $info['id_organisme'];
			//if($info['type'] == 'super_admin') $this->isSuperAdmin = true;   
			//if($info['type'] < $this->userLevel) $this->userLevel = $info['type'];
		}
		
		/*$query = 'SELECT organisme_type FROM sp_organismes WHERE organisme_id IN ('.implode(',',$groupOrga).')';
		$result	= mysql_query($query) or die(mysql_error());
		
		while ($info = mysql_fetch_assoc($result)){
			//if($info['organisme_type'] == 'super_admin') $this->isSuperAdmin = true;
			if($info['organisme_type'] < $this->userLevel) $this->userLevel = $info['organisme_type'];
		}*/
		
		return $group;
	}
	
	/*
	@ TESTE SI UTILISATEUR A BIEN LE DROIT D'ETRE SUR LA PLATEFORME 
	@ ET RECUPERE SON NIVEAU D'ADMINISTRATEUR LE CAS ÉCHÉANT
	@
	*/
	function isAuthorised(){
		
		if($this->LDAP){
			$query	= sprintf("SELECT COUNT(user_email) AS nbr
								FROM sp_users
								WHERE user_email= %s", GetSQLValueString($this->email, "text")); 
		}else{
			$query	= sprintf("SELECT COUNT(user_login) AS nbr
								FROM sp_users
								WHERE user_login= %s", GetSQLValueString($this->login, "text")); 	
		}
	
		$result	= mysql_query($query) or die(mysql_error());
		$info	= mysql_fetch_assoc($result);
		
		
		if($info['nbr']>0){		
			return true;
		}else{
			return false;
		}
	}

	/**
	* test_LDAP Teste si les infos de login de soumission sont valides LDAP
	* @param $login => login de l'utilisateur
	* @param $password => mot de passe de l'utilisateur
	* @return JSON => l'objet JSON contiendra les infos de réussite ou d'erreur
	*/
	function test_LDAP($login=NULL,$password=NULL){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		session_start();

		$erreurLDAP = "";
		$champVide = "";
		
		if(isset($login) && isset($password) && $login!="" && $password!=""){
			$infosEtudiant = func::connectLDAP($login,$password);
			
			switch ($infosEtudiant->info){
				case "login error" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
				case "no connexion" : $erreurLDAP="Impossible de se connecter au serveur d'identification pour le moment."; break;
				case "no login" : $erreurLDAP="Les informations fournies ne permettent pas de vous identifier."; break;
				default : $erreurLDAP=""; break;
			}

			if($erreurLDAP==""){
				$_SESSION['nomSP'] = $infosEtudiant->nom;
				$_SESSION['prenomSP'] = $infosEtudiant->prenom;
				$_SESSION['mailSP'] = $infosEtudiant->email;
				$_SESSION['typeSP'] = $infosEtudiant->type;
			}
		}
		if($login=="" || $password==""){
			$champVide = "Tous les champs marqués d'une * doivent être remplis.";
		}

		$retour->titre_bloc 	= "Vous êtes bien inscrit à l'événement";
		$retour->erreurLDAP = $erreurLDAP;
		$retour->champVide = $champVide;

		session_unset();
		return json_encode($retour);
	}
}

?>
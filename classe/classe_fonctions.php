<?php
include_once(REAL_LOCAL_PATH.'inscription/makeTicket.php');
include_once(REAL_LOCAL_PATH.'classe/tcpdf_min/config/lang/eng.php');
include_once(REAL_LOCAL_PATH.'classe/tcpdf_min/tcpdf.php');
include_once(REAL_LOCAL_PATH.'classe/class.phpmailer.php');
include_once(REAL_LOCAL_PATH.'classe/class.smtp.php');
//include_once(REAL_LOCAL_PATH.'classe/classe_billet.php');

class Func {
	/*
	@ GESTION DES FONCTIONS
	@
	@
	*/
	function func(){
		
	}
	
	/*
	@ Fonction de connexion au LDAP
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
						if ( isset($info[$i]["cn"][0]) ){			$retour->login	= $info[$i]["cn"][0]; }
						if ( isset($info[$i]["givenname"][0]) ){	$retour->prenom = $info[$i]["givenname"][0]; }
						if ( isset($info[$i]["sn"][0]) ){			$retour->nom	= $info[$i]["sn"][0]; }
						if ( isset($info[$i]["mail"][0]) ){			$retour->email	= $info[$i]["mail"][0]; }
						if ( isset($info[$i]["employeetype"][0]) ){	$retour->type	= $info[$i]["employeetype"][0]; }
					}
					ldap_close($ldapconn);
				} else {
					$retour->info = "login error";
				}
			
			}else{
				$retour->info = "no connexion";
			}
		}else{
			$retour->info = "no login";
		}
		return $retour;
	}


	/*
	@ CONVERSION DES CHAINES ENVOYEES PAR LES FORMULAIRES
	@
	@
	*/
	static function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = ""){
		if (PHP_VERSION < 6) {
			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		}
		
		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
		switch ($theType) {
			case "text":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;    
			case "long":
			case "int":
				$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
			case "double":
				$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			break;
			case "date":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "defined":
				$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
			case "boolean":
				$theValue = $theValue ? '1' : '0';
			break;
			default :
				$theValue = "NULL";
			break;
		}
		return $theValue;
	}

	/*
	@ Teste si les champs d'un formulaire sont bien remplis, retourne vrai si c'est le cas
	@
	@
	*/
	static function testeChamps($nom, $prenom, $mail){
		$syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#'; 
		if($nom=="" || $prenom=="" || $mail=="" || !preg_match($syntaxe,$mail)){ 
			return false;
		}
		else{ 
			return true;
		}
	}

	/*
	@ testChampsSoumission Teste si les champs obligatoires du formulaire de soumission d'un événement sont bien remplis, retourne vrai si c'est le cas
	@
	@
	*/
	static function testChampsSoumission($titre, $texte, $organisateur, $rubrique, $date, $heure){
		$erreur = "";
		
		if($titre==""){
			$erreur .= "le champ titre de l'événement est obligatoire<br/>";
		}
		if($texte==""){
			$erreur .= "le champ description de l'événement est obligatoire<br/>";
		}
		if($organisateur==""){
			$erreur .= "le champ organisateur de l'événement est obligatoire<br/>";
		}
		if($rubrique=="-1"){
			$erreur .= "Merci de choisir une rubrique pour l'événement<br/>";
		}
		if($date==""){
			$erreur .= "la date de début de l'événement est obligatoire<br/>";
		}
		if($heure==""){
			$erreur .= "l'heure de début de l'événement est obligatoire<br/>";
		}
		return $erreur;
	}

	/*
	@ Retourne les dates et horaires d'un événement en fonction de la langue
	@
	@
	*/
	static function getHorairesEvent($debut, $fin, $lang){
		$jourDebut  =   new DateTime($debut);
		$jourFin  =   new DateTime($fin);

		$testDebut = $jourDebut->format('d');
		$testFin = $jourFin->format('d');

		if($testDebut==$testFin){
			if($jourFin->format('H:i')!="23:59"){
				if($lang=="fr"){
					$horaires = $jourDebut->format('d/m | H')."h".$jourDebut->format('i-').$jourFin->format('H')."h".$jourFin->format('i');
				}
				else{
					$horaires = $jourDebut->format('d/m | H:i-').$jourFin->format('H:i');
				}
			}
			else{
				if($lang=="fr"){
					$horaires = $jourDebut->format('d/m | H')."h".$jourDebut->format('i');
				}
				else{
					$horaires = $jourDebut->format('d/m | H:i');
				}
			}	
		}
		else{
			if($jourFin->format('H:i')!="23:59"){
				if($lang=="fr"){
					$horaires = "du ".$jourDebut->format('d/m | H')."h".$jourDebut->format('i')." au ".$jourFin->format('d/m | H')."h".$jourFin->format('i');
				}
				else{
					$horaires = "from ".$jourDebut->format('d/m | H:i')." to ".$jourFin->format('d/m | H:i');
				}
			}
			else{
				if($lang=="fr"){
					$horaires = "du ".$jourDebut->format('d/m | H')."h".$jourDebut->format('i')." au ".$jourFin->format('d/m');
				}
				else{
					$horaires = "from ".$jourDebut->format('d/m | H:i')." to ".$jourFin->format('d/m');
				}
			}
		}
		return $horaires;
	}

	/*
	@ Retourne l'identifiant unique
	@
	@
	*/
	static function uniqueID($id_event=NULL, $id_user=NULL){
		if(isset($id_event) && isset($id_user)){
		
			$id_diff = abs($id_event-$id_user);
			
			$a			= rand(1,9);	
			$d			= str_split( substr("0000".$id_diff		, -4) );
			$e			= str_split( substr("0000".$id_event	, -4) );
			$u			= str_split( substr("0000".$id_user		, -4) );

			$ordre		= $a%3;
		
			if($ordre == 0){
				$ID = $a . $d[0] . $e[0] . $u[0] . $d[1] . $e[1] . $u[1] . $d[2] . $e[2] . $u[2] . $d[3] . $e[3] . $u[3];
			}else if($ordre == 1){
				$ID = $a . $e[0] . $u[0] . $d[0] . $e[1] . $u[1] . $d[1] . $e[2] . $u[2] . $d[2] . $e[3] . $u[3] . $d[3];
			}else if($ordre == 2){
				$ID = $a . $u[0] . $d[0] . $e[0] . $u[1] . $d[1] . $e[1] . $u[2] . $d[2] . $e[2] . $u[3] . $d[3] . $e[3];
			}

			return $ID;

		}else{

			return NULL;

		}
	}


	/*
	@ Envoie le mail à l'inscrit
	@
	@
	*/
	//static function envoiMail($session, $mailHTML, $billet, $passBook, $mail){
	static function envoiMail($session, $mailHTML, $billet, $mail){

		$mailEnvoi  = new phpmailer();
		$mailEnvoi -> IsMail();
		$mailEnvoi -> Host     = 'localhost';
		$mailEnvoi -> Hostname     = 'Sciences Po';
		$mailEnvoi -> Charset  = 'UTF-8';
		$mailEnvoi -> SMTPAuth = FALSE;
		$mailEnvoi -> From     = 	'no.reply@sciences-po.fr';
		$mailEnvoi -> FromName =	utf8_decode('Sciences Po | événements');
		$mailEnvoi -> AddReplyTo('no.reply@sciences-po.fr');
		$mailEnvoi -> WordWrap = 72;
		$mailEnvoi -> IsHTML( TRUE );
		
		$mailEnvoi -> Subject		= utf8_decode("Inscription à ".$session);

		$mailEnvoi -> Body		.= $mailHTML;
				
		$mailEnvoi -> AddAttachment($billet);
		
		$mailEnvoi -> AddAddress($mail);
		$mailEnvoi->Send();
		unset($mailEnvoi);
	}

	/*
	@ Envoie l'alerte aux admins
	@
	@
	*/
	static function envoiAlerte($session_id){ 
		$sqlSession = sprintf("SELECT session_nom, session_places_internes_totales AS spit, session_places_internes_prises AS spip FROM sp_sessions WHERE session_id =%s", 
									func::GetSQLValueString($session_id, "int"));
		$resSession=mysql_query($sqlSession) or die(mysql_error());
		$rowSession = mysql_fetch_array($resSession);
		
		if(($rowSession['spit']-$rowSession['spip'])==10){
			$sqlIdEvenement = sprintf("SELECT evenement_groupe_id FROM sp_sessions as sps, sp_evenements as spe WHERE spe.evenement_id=sps.evenement_id AND session_id =%s", 
										func::GetSQLValueString($session_id, "int"));
			$resIdEvenement=mysql_query($sqlIdEvenement) or die(mysql_error());
			$rowIdEvenement = mysql_fetch_array($resIdEvenement);
			
			$sqlUser=sprintf("SELECT * FROM sp_users as spu, sp_rel_user_groupe as spr WHERE (user_alerte = '1' AND user_type='1') OR (user_alerte='1' AND groupe_id=%s AND spr.user_id=spu.user_id) GROUP BY spu.user_id",func::GetSQLValueString($rowIdEvenement['evenement_groupe_id'], "int"));
			$resUser=mysql_query($sqlUser) or die(mysql_error());
			while($rowUser = mysql_fetch_array($resUser)){
				$mailAlerte  = new phpmailer();
				$mailAlerte -> IsMail();
				$mailAlerte -> Host     = 'localhost';
				$mailAlerte -> Charset  = 'UTF-8';
				$mailAlerte -> SMTPAuth = FALSE;
				$mailAlerte -> From     = 	'no.reply@sciences-po.fr';
				$mailAlerte -> FromName =	utf8_decode('Sciences Po | événements');
				$mailAlerte -> AddReplyTo( );
				$mailAlerte -> WordWrap = 72;
				$mailAlerte -> IsHTML( TRUE );
				$mailAlerte -> Subject		= utf8_decode("Alerte pour ".$rowSession['session_nom']);

				$mailAlerte -> Body		.= utf8_decode('Il ne reste que 10 places encore disponibles pour '.$rowSession['session_nom']);

				$mailAlerte -> AddAddress($rowUser['user_email']);

				$mailAlerte->Send();
				unset($mailAlerte);
			}
		}	
	}


	/*
	@ Envoie la soumission d'un événement
	@
	@
	*/
	static function envoiImage($image, $evenement, $user, $photo){ 
		$sqlEvenement = sprintf("SELECT * FROM ".TB."evenements WHERE evenement_id=%s", func::GetSQLValueString($evenement, "int"));
		$resEvenement=mysql_query($sqlEvenement) or die(mysql_error());
		$rowEvenement = mysql_fetch_array($resEvenement);
		
		$sqlSoumetteur = sprintf("SELECT * FROM ".TB."users WHERE user_id =%s", func::GetSQLValueString($user, "int"));
		$resSoumetteur=mysql_query($sqlSoumetteur) or die(mysql_error());
		$rowSoumetteur = mysql_fetch_array($resSoumetteur);
	    


		$sqlUser=sprintf("SELECT * FROM ".TB."users as spu, ".TB."rel_user_groupe as spr WHERE (user_alerte = '1' AND user_type='1') OR (user_alerte='1' AND groupe_id=%s AND spr.user_id=spu.user_id) GROUP BY spu.user_id",func::GetSQLValueString($rowEvenement['evenement_groupe_id'], "int"));
		$resUser=mysql_query($sqlUser) or die(mysql_error());
		
		$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($evenement, "int"));
		$ressessions = mysql_query($sqlsessions) or die(mysql_error()); 
		
		$finEvenement=0;
		while($rowsession = mysql_fetch_array($ressessions)){
			if($rowsession['session_fin']>$finEvenement){
				$finEvenement = $rowsession['session_fin'];
			}
		}

		$jourDebut = date("d", $rowEvenement['evenement_date']);
		$jourFin = date("d", $finEvenement);
		$horaires = func::getHorairesAlerte($jourDebut, $jourFin, $rowEvenement['evenement_date'], $finEvenement);
		
		
		while($rowUser = mysql_fetch_array($resUser)){
			$mailAlerte  = new phpmailer();
			$mailAlerte -> IsMail();
			$mailAlerte -> Host     = 'localhost';
			$mailAlerte -> Charset  = 'UTF-8';
			$mailAlerte -> SMTPAuth = FALSE;
			$mailAlerte -> From     = 	'no.reply@sciences-po.fr';
			$mailAlerte -> FromName =	utf8_decode('Sciences Po - Evénements');
			$mailAlerte -> AddReplyTo( );
			$mailAlerte -> WordWrap = 72;
			$mailAlerte -> IsHTML( TRUE );
			$mailAlerte -> Subject		= utf8_decode("Nouvel événement proposé : ".$rowEvenement['evenement_titre']." par : ".$rowSoumetteur['user_nom']." ".$rowSoumetteur['user_prenom']);
	        
			$mailAlerte -> Body		.= utf8_decode($rowEvenement['evenement_titre'].", ".$horaires."<br/>");
	        
			$mailAlerte -> Body		.= utf8_decode("Proposé par ".$rowSoumetteur['user_nom']." ".$rowSoumetteur['user_prenom']." (<a href=\"mailto:".$rowSoumetteur['user_email']."\">".$rowSoumetteur['user_email']."</a>)<br/>");

			$mailAlerte -> Body		.= utf8_decode("<a href=\"".CHEMIN_BACK."edit_evenement_unique.php?id=".$rowEvenement['evenement_id']."&menu_actif=evenements\">".$rowEvenement['evenement_titre']."</a><br/>");

	        if($image!=""){
		    	$mailAlerte -> Body		.= utf8_decode("Ci-joint l'image pour l'événement:<br/>"); 
				$mailAlerte -> Body		.= utf8_decode("<img src=\"".CHEMIN_IMAGES."evenement_".$evenement."/".$photo."\" alt=\"".$rowEvenement['evenement_titre']."\"/>");    
				$mailAlerte -> AddAttachment($image);
			}

			$mailAlerte -> AddAddress($rowUser['user_email']);

			$mailAlerte->Send();
			unset($mailAlerte);
		}
	}

	/*
	@ Horaires pour l'envoi de la soumission
	@
	@
	*/
	static function getHorairesAlerte($jourDebut, $jourFin, $debut, $fin){
		if($jourDebut==$jourFin){
			if(date("H:i", $fin)!="23:59"){
				$horaires = date("d/m/Y", $debut)." : ".date("H\hi", $debut)." > ".date("H\hi", $fin);
			}
			else{
				$horaires = date("d/m/Y", $debut)." à ".date("H\hi", $debut);
			}	
		}
		else{
			if(date("H:i", $fin)!="23:59"){
				$horaires = "du ".date("d/m/Y", $debut)." à ".date("H\hi", $debut)." au ".date("d/m/Y", $fin)." à ".date("H\hi", $fin);
			}
			else{
				$horaires = "du ".date("d/m/Y", $debut)." à ".date("H\hi", $debut)." au ".date("d/m/Y", $fin);
			}
		}
		return $horaires;
	}

	/*
	@ extension d'un fichier
	@
	@
	*/
	static function getExtension($fileName){
		
		// get the extension
		$ext = substr(strrchr($fileName, '.'), 1);
		
		// to lower case
		$ext = "." . strtolower($ext);
		
		return $ext;
	}

	/*
	@ contrôle la validité de l'extension
	@
	@
	*/
	static function isExtAuthorized($ext){
		$format_autorise = array( ".jpg", ".jpeg", ".png", ".gif");
		if(in_array($ext, $format_autorise)){
			return true;
		}else{
			return false;
		}
	}

	/*
	@ crée une miniature pour une image
	@
	@
	*/
	static function make_miniature($img, $max_width=400, $max_height=400, $repertoire_destination='./', $prefixe='mini-', $supprimer_original=false){
		/*Fonction qui créer la mimiature d'une image.
		Retourne true si la miniature a bien été créée, un message d'erreur sinon

		Liste des paramètres :
		- $img : Chemin relatif du répertoire dans lequel se trouve l'image originale
		- $max_width : Largeur maximale pour la miniature
		- $max_height : Hauteur maximale pour la miniature
		- $repertoire_destination : Répertoire dans lequel doit être sauvegardée la miniature
		- $prefixe : Préfixe donné à la miniature (Ex : "ma-photo.jpg" devient "mini-ma-photo.jpg"
		- $supprimer_original : Si est égal à true on supprime l'image originale

		/*Initialisations*/
		$reussi = false;
		$message = '';

		/*on ouvre le fichier*/
		$file = fopen($img,'r');
		
		
		
		if ($file !== false){ //Le fichier existe
			/*On recupere le nom de l'image*/
			$nom = $prefixe.basename($img);
			
			/*getimagesize() renvoie FALSE si le fichier n'est pas une image*/
			if (false !== list($largeur_orig,$hauteur_orig,$extension) = getimagesize($img)){
				/*On récupère l'extension de l'image*/
				$extension_img = substr(strchr($nom,'.'),1);
				/*On vérifie que le fichier soit bien au format jpg, gif ou png*/
				if(ereg('(jpeg|jpg|gif|png)$',$extension_img)){
					
					switch ($extension_img){
						case "gif": // GIF
						$type_img = imagecreatefromgif($img); break;
						case "jpg": //JPEG
						$type_img = imagecreatefromjpeg($img); break;
						case "jpeg": //JPEG
						$type_img = imagecreatefromjpeg($img); break;
						case "png": // PNG
						$type_img = imagecreatefrompng($img); break;
					}
					
			
					/*On verifie la taille*/
					if(($largeur_orig > $max_width) || ($hauteur_orig > $max_height)){
						// si l'image est trop large ou trop haute
						if ($largeur_orig > $hauteur_orig){
							// image plus large que haute
							$hauteur = round(($hauteur_orig * $max_width) / $largeur_orig);
							$largeur = $max_width;
						}
						else{
							// image plus haute que large
							$hauteur = $max_height;
							$largeur = round(($largeur_orig * $max_height) / $hauteur_orig);
						}
					}
					else{
						$largeur = $largeur_orig;
						$hauteur = $hauteur_orig;
					}

					/*On créer la miniature*/
					$src = imagecreatetruecolor($largeur,$hauteur);
					imagealphablending($src, false);
			
					imagecopyresampled($src,$type_img,0,0,0,0,$largeur,$hauteur,$largeur_orig,$hauteur_orig);
					
					imagesavealpha($src, true);
					
					/*On sauvegarde la miniature*/
					
					
					switch ($extension_img){
						case "gif": // GIF
						imagegif($src, $repertoire_destination.$nom); break;
						case "jpg": //JPEG
						imagejpeg($src, $repertoire_destination.$nom); break;
						case "jpeg": //JPEG
						imagejpeg($src, $repertoire_destination.$nom); break;
						case "png": // PNG
						imagepng($src, $repertoire_destination.$nom); break;
					}
					
					/*On libere la memoire utilisée par cette image.*/
					imagedestroy($src);
					
					/*On supprime éventuellement l'image originale*/
					if ($supprimer_original === true){
						unlink($img);
					}
					
					$reussi=true;
				}
				else{
					$message = 'Erreur : L\'extension de l\'image est '.$extension_img.' ! Elle devrait être JPG, JPEG, GIF ou PNG';
				}
			}
			else{
				$message = 'Erreur : Le fichier n\'est pas une image !';
			}
		}
		else{
			$message = 'Erreur : Le fichier n\'existe pas !';
		}

		if ($reussi){
			return true;
		}
		else{
			return $message;
		}
	}

	/*
	@ Détecte l'url dans une chaine de caractères et la retourne
	@
	@
	*/
	static function detectURL($t) {
		// link URLs
		$t = str_replace("\\r","\r",$t);
		$t = str_replace("\\n","\n<BR>",$t);
		$t = str_replace("\\n\\r","\n\r",$t);
		
		$trans = array("http://" => "");
		$t = strtr($t, $trans);

		$in=array(
		'`((?:https?|ftp)://\S+[[:alnum:]]/?)`si',
		'`((?<!//)(www\.\S+[[:alnum:]]/?))`si'
		);
		$out=array(
		'<a href="$1" target="_blank">$1</a> ',
		'<a href="http://$1" target="_blank">$1</a>'
		);
		$t = preg_replace($in,$out,$t); 

		// link mailtos
		$t = preg_replace( "/(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)".
			"([[:alnum:]-]))/i", "<a href=\"mailto:\\1\">\\1</a>", $t);

		return trim($t);
	}
	
	
	/*
	@ CONVERSION DES URLs EN LIENS CLICABLES
	@
	@
	*/
	static function formatage($texte){
		
		if (ereg("[\"|'][[:alpha:]]+://",$texte) == false){
			$texte = ereg_replace('([[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/])', '<a target=\"_new\" href="\\1">\\1</a>', $texte); 
			$texte = ereg_replace("(^| |\n)(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a target=\"_new\" href=\"http://\\2\">\\2</a>", $texte);
		} 
		
		return $texte;
	}
	
	
	
	/*
	@ CREER UN ELEMENT DE FORMULAIRE COMBOBOX A PARTIR D'UN TABLEAU
	@
	@
	*/
	static function createSelect($array, $name='', $id = NULL, $additionnal=NULL, $isnull=true){
		
		if(isset($additionnal)){$add = $additionnal; }else{ $add = '';};
	
		$selectItem = "<select name=\"$name\" id=\"$name\" $add>\n";
	
		if($isnull){ $selectItem .= "\t<option value=\"\" >Aucun</option>\n"; }
	
		foreach($array as $key => $value){
			$sep = explode('_',$key);
			
			if($sep[0]=='separateur'){
				$selectItem .= "\t<optgroup label=\"$value\"></optgroup>\n";
			}else{
				if($id && $id.'' == $key.''){ $sel = "selected=\"selected\""; }else {$sel="";}
				$selectItem .= "\t<option value=\"$key\" $sel>$value</option>\n";
			}
		}
		
		$selectItem .= "</select>";
		
		return $selectItem;
	}
	
	/*
	@ FONCTION POUR CREER UN COMBOBOX A PARTIR D'UN DOSSIER
	@
	@
	*/
	static function createFolderSelect($_folder=NULL,$_name=NULL,$_id=NULL,$_selectValue=NULL){
		if(isset($_folder)){
			
			if ($handle = opendir($_folder)) {
				
				$selectItem = "<select name=\"$_name\" id=\"$_id\">\n";	
				
				/* Ceci est la façon correcte de traverser un dossier. */
				while (false !== ($file = readdir($handle))) {
					
					if (substr($file,0,1)!='.'){					
							if($_selectValue && $_selectValue.'' == $file.''){ $sel = "selected=\"selected\""; }else {$sel="";}
							$selectItem .= "\t<option value=\"$file\" $sel>$file</option>\n";
					}
				}
			
				closedir($handle);		
	
				$selectItem .= "</select>";
				
				return $selectItem;
			}
		}
	}
	
	/*
	@ CREER UN GROUPE DE CASE A COCHER
	@
	@
	*/
	static function createCheckBox($array, $name='', $id=NULL){
	
		$selectItem = '';
	
		foreach($array as $key => $value){
			// $array->select	|	$array->value	|	$array->label	| $array->classe
			$classe = isset($value->classe)?' class="'.$value->classe.'" ':'';
			$checked= !empty($value->select)?'checked="checked"':'';
			$selectItem .= '<span><input type="checkbox" name="'.$name.'" value="'.$value->value.'" id="'.$id.'-'.$key.'" '.$classe.' '.$checked.'/><label for="'.$id.'-'.$key.'">'.$value->label.'</label></span>'."\n";
		}
	
		return $selectItem ;
	}
	
	/*
	@ CREER UN ELEMENT DE FORMULAIRE SELECT
	@ avec id différent de l'attribut name
	@
	*/
	static function createCombobox($array, $name='', $id = NULL, $selectValue=NULL, $additionnal=NULL, $isnull=true){
		
		if(isset($additionnal)){$add = $additionnal; }else{ $add = '';}
	
		$selectItem = "<select name=\"$name\" id=\"$id\" $add>\n";
	
		if($isnull){ $selectItem .= "\t<option value=\"\" >Aucun</option>\n"; }
	
		foreach($array as $key => $value){
			$sep = explode('_',$key);
			
			if($sep[0]=='separateur'){
				$selectItem .= "\t<optgroup label=\"$value\"></optgroup>\n";
			}else{
				if($selectValue && $selectValue.'' == $key.''){ $sel = "selected=\"selected\""; }else {$sel="";}
				$selectItem .= "\t<option value=\"$key\" $sel>$value</option>\n";
			}
		}
		
		$selectItem .= "</select>";
		
		return $selectItem;
	}

	
	
	
	// TRANSFORMER UNE CHAINE EN IDENTIFIANT : PAS D'ACCENTS, PAS D'ESPACES
	static function makeIdentifier($valeur){
		$valeur = strtr($valeur,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ','AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$valeur = preg_replace('/([^.a-z0-9]+)/i', '_', $valeur);
		
		return $valeur;
	}

	/*
	@ NETTOYAGE D'UNE CHAINE DE CARACTERES
	@
	@
	*/
	static function clean($valeur){
		return strtolower(	utf8_encode(strtr(utf8_decode($valeur),
							utf8_decode("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'" ), 
							utf8_decode("aaaaaceeeeiiiinooooouuuuyyaaaaaceeeeiiiinooooouuuuy " ))));
	
	}
	
	
	/*
	@ UPLOADER UN FICHIER
	@
	@
	*/
	static function upload($file, $repository){
		
		
		$name = $file["name"];
	
		$name = strtr($name,'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
							'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
		$name = preg_replace('/([^.a-z0-9]+)/i', '_', $name);
		$pos = strrpos($name, '.');
		$extension = substr($name, $pos, strlen($name) );
		$nom = substr($name, 0, $pos);
		$cpt=0;
		while(file_exists($repository.$name)){
			$cpt++;
			$name = $nom.'('.$cpt.')'.$extension;
		}
		
		copy($file['tmp_name'], $repository.$name);
		return $name;
	}
	
	
	/*
	@ SUPPRIMER UN FICHIER
	@
	@
	*/
	static function delete($name, $repository){
		unlink($repository.$name);
	}
	
	
	/*
	@ SUPPRIMER UN DOSSIER
	@ et son contenus de différents fichiers
	@
	*/
	static function delete_dir($chemin) {
		// vérifie si le nom du repertoire contient "/" à la fin
		// place le pointeur en fin d'url
		if ($chemin[strlen($chemin)-1] != '/'){
			// rajoute '/'
			$chemin .= '/';
		}
	
		if (is_dir($chemin)) {
			 $sq = opendir($chemin); // lecture
			 while ($f = readdir($sq)) {
				if ($f != '.' && $f != '..'){
					$fichier = $chemin.$f; // chemin fichier
					if (is_dir($fichier)){
						sup_repertoire($fichier);
					// rapel la fonction de manière récursive
					}else{
						// sup le fichier
						unlink($fichier);
					}
				}
			}
			closedir($d);
			rmdir($chemin); // sup le répertoire
		}else {
			unlink($chemin);  // sup le fichier
		}
	}
	

	
	/*
	@ FONCTION POUR BIEN FERMER TOUS LES TAGS D'UNE CHAINE HTML
	@
	@
	*/
	static function close_dangling_tags($html){
	  #put all opened tags into an array
	  preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU",$html,$result);
	  $openedtags=$result[1];
	
	  #put all closed tags into an array
	  preg_match_all("#</([a-z]+)>#iU",$html,$result);
	  $closedtags=$result[1];
	  $len_opened = count($openedtags);
	  # all tags are closed
	  if(count($closedtags) == $len_opened){
		return $html;
	  }
	
	  $openedtags = array_reverse($openedtags);
	  # close tags
	  for($i=0;$i < $len_opened;$i++) {
		if (!in_array($openedtags[$i],$closedtags)){
		  $html .= '</'.$openedtags[$i].'>';
		} else {
		  unset($closedtags[array_search($openedtags[$i],$closedtags)]);
		}
	  }
	  return $html;
	}
	
	/*
	@ CONVERTI LES CHAINES D'UN TABLEAU EN BAS DE CASSE
	@
	@
	*/
	static function arraytolower(array $array, $round = 0){ 
	  return unserialize(strtolower(serialize($array))); 
	}
	
	/*
	@ GENERE UN TIMESTAMP UNIX DEPUIS UNE DATE yyyy-mm-dd hh:mm:ss
	@
	@
	*/
	static function makeTime($date=NULL){
		
		if(!empty($date)){
			$a = date_parse($date);
			$timestamp = mktime($a['hour'], $a['minute'], $a['second'], $a['month'], $a['month'], $a['year']);
			
			return $timestamp;
		}
		
	}
	
	static function time2sec($duree=NULL){
		if(!empty($duree)){
			
			$d = explode (':', $duree);
			
			return $d[0]*3600+$d[1]*60+$d[2];
			
		}
	}
	
	
	
	//// virer les sauts de ligne
	// GILDAS 19/07/2012
	static function nonl($str){
		$str = str_replace("\n", "", $str);
		$str = str_replace("\r\n", "", $str);
		$str = str_replace("\r", "", $str);
		return $str;
	}
	
	//// <br /> -> newline
	// GILDAS 19/07/2012
	static function br2nl($str){
		$str = preg_replace("#\<br\s*\/?\>#isU", '
	', $str);
		return $str;
	}
	
	static function encodeAccentHTML($data = NULL){
		if(isset($data)){
			$trans = array(	'À'=>'&Agrave;',
							'Á'=>'&Aacute;',
							'Â'=>'&Acirc;',
							'Ã'=>'&Atilde;',
							'Ä'=>'&Auml;',
							'Å'=>'&Aring;',
							'Ç'=>'&Ccedil;',
							'È'=>'&Egrave;',
							'É'=>'&Eacute;',
							'Ê'=>'&Ecirc;',
							'Ë'=>'&Euml;',
							'Ì'=>'&Igrave;',
							'Í'=>'&Iacute;',
							'Î'=>'&Icirc;',
							'Ï'=>'&Iuml;',
							'Ò'=>'&Ograve;',
							'Ó'=>'&Oacute;',
							'Ô'=>'&Ocirc;',
							'Õ'=>'&Otilde;',
							'Ö'=>'&Ouml;',
							'Ù'=>'&Ugrave;',
							'Ú'=>'&Uacute;',
							'Û'=>'&Ucirc;',
							'Ü'=>'&Uuml;',
							'Ý'=>'&Yacute;',
							'Ÿ'=>'&Yuml;',
							'à'=>'&agrave;',
							'á'=>'&aacute;',
							'â'=>'&acirc;',
							'ã'=>'&atilde;',
							'ä'=>'&auml;',
							'å'=>'&aring;',
							'ç'=>'&ccedil;',
							'è'=>'&egrave;',
							'é'=>'&eacute;',
							'ê'=>'&ecirc;',
							'ë'=>'&euml;',
							'ì'=>'&igrave;',
							'í'=>'&iacute;',
							'î'=>'&icirc;',
							'ï'=>'&iuml;',
							'ð'=>'&eth;',
							'ò'=>'&ograve;',
							'ó'=>'&oacute;',
							'ô'=>'&ocirc;',
							'õ'=>'&otilde;',
							'ö'=>'&ouml;',
							'ù'=>'&ugrave;',
							'ú'=>'&uacute;',
							'û'=>'&ucirc;',
							'ü'=>'&uuml;',
							'ý'=>'&yacute;',
							'ÿ'=>'&yuml;');
									
			return strtr($data,$trans);
		}else{
			return false;	
		}
	}

	//============================================================+
	// File name   : test_tcpdf-1.php
	//
	// Description : Generation du billet sciences po en pdf
	//
	// Author: Loic Horellou
	//
	// (c) Copyright:
	//               Loic Horellou
	//               www.syclo.fr
	//               Fabien Raymondaud
	//               www.fabien-raymondaud.net
	//============================================================+

	/**
	 * Creates an example PDF TEST document using TCPDF
	 * @package com.tecnick.tcpdf
	 * @link http://tcpdf.org
	 * @license http://www.gnu.org/copyleft/lesser.html LGPL
	 */

	static function createBillet($uniqueID,$titre,$date,$heure,$nom,$prenom,$statut,$organisateur,$adresse,$salle,$isHeadphones=false,$mentions){
			
		// ON CREE UN DOCUMENT AU FORMAT 18x11 cm
		$pdf = new TCPDF('H', 'mm', 'A4', true, 'UTF-8', false);
		//$pdf = new TCPDF('L', 'mm', array(180.000,110.000), true, 'UTF-8', false);
		
		// DECLARATION DES INFORMATIONS
		$pdf->SetCreator('Sciences Po - Sÿclo');
		$pdf->SetAuthor('Sciences Po - Sÿclo');
		$pdf->SetTitle('Billet - '.$titre);
		$pdf->SetSubject('Billet - '.$titre);
		$pdf->SetKeywords('Sciences Po, '.$titre.', billet, Sÿclo,');
		

		// REGLAGE DE LA PAGE
		$pdf->SetMargins(0,0,0);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setCellPaddings(0,0,0,0);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('helvetica', '', 10);
		$pdf->AddPage();

		
		// ON FORMATTE L'ID POUR AVOIR UN ESPACE APRES LE PREMIER CHIFFRE PUIS UN ESPACE TOUS LES 4
		$uniqueIDstr = strrev(implode(' ',str_split(strrev($uniqueID), 4)));
		
		$decalX = 15;
		$decalY = 20;
		
		// ON RECUPERE LE FICHIER ILLUSTRATOR QUI VA SERVIR DE FOND
		$pdf->ImageEps($file='../../inscription/billet.ai', $x=$decalX, $y=$decalY, $w=180, $h=110, $link='', $useBoundingBox=false, $align='', $palign='', $border=0, $fitonpage=false);
		
		
		//************** GAUCHE **************

		//TITRE
		$pdf->SetFont('helvetica', '', 14);
		$pdf->SetY(25+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(170, 5,uc_strtoupper_fr($titre), 0, 'L', 1, 0, '', '', true);

		
		//DATE
		$pdf->SetFont('helvetica', 'I', 10);
		$pdf->SetY(38+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(170, 5,dateFormat($date), 0, 'L', 1, 0, '', '', true);

		//NOM
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(45+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'Nom :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->SetY(45+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,uc_strtoupper_fr($nom), 0, 'L', 1, 0, '', '', true);

		//PRENOM
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(49+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'Prénom :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->SetY(49+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,$prenom, 0, 'L', 1, 0, '', '', true);

		//N°
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(53+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'N° :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->SetY(53+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,$uniqueIDstr, 0, 'L', 1, 0, '', '', true);

		//STATUT
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(57+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'Statut :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->SetY(57+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,$statut, 0, 'L', 1, 0, '', '', true);

		//ORGANISATEUR
		$pdf->SetFont('helvetica', '', 8);
		$pdf->SetY(65+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'Organisateur :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetY(65+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,$organisateur, 0, 'L', 1, 0, '', '', true);

		//ADRESSE
		$pdf->SetY(69+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		$pdf->MultiCell(20, 5,'Adresse :', 0, 'L', 1, 0, '', '', true);

		$pdf->SetY(69+$decalY);
		$pdf->setCellMargins(25+$decalX,0,0,0);
		$pdf->MultiCell(60, 5,$adresse, 0, 'L', 1, 0, '', '', true);	


		//************** DROITE **************

		//ECOUTEURS
		if($isHeadphones){
			$pdf->SetFont('helvetica', 'BI', 10);
			$pdf->SetY(10+$decalY);
			$pdf->setCellMargins(100+$decalX,0,0,0);
			$pdf->MultiCell(90, 5,uc_strtoupper_fr('{ Écouteurs réservés }'), 0, 'C', 1, 0, '', '', true);
		}
		

		//SALLE
		$pdf->SetFont('helvetica', 'B', 18);
		$pdf->SetY(45+$decalY);
		$pdf->setCellMargins(90+$decalX,0,0,0);
		$pdf->MultiCell(90, 5,uc_strtoupper_fr($salle), 0, 'C', 1, 0, '', '', true);

		// ON CREE LE CODE BARRE AU FORMAT 128B
		$pdf->setCellMargins(0,0,0,0);
		$pdf->SetY(62+$decalY);
		$pdf->SetFont('helvetica', '', 10);
		$style = array(
			'hpadding'		=> 'auto',
			'vpadding'		=> 'auto',
			'text'			=> true,
			'label'			=> $uniqueIDstr,
			'fontsize'		=> 8,
			'stretchtext'	=> 4
		);
		$pdf->write1DBarcode($uniqueID, 'C128B', $x=100+$decalX, $y=62+$decalY, $w=70, $h=20, $xres=0.4, $style, 'N');

		
		//INFORMATIONS COMPLEMENTAIRES
		$pdf->SetFont('helvetica', '', 6);
		$pdf->SetY(88+$decalY);
		$pdf->setCellMargins(5+$decalX,0,0,0);
		if($mentions!=""){
			$texte=utf8_encode(strip_tags(html_entity_decode($mentions)));
		}
		else{
			$texte = "INFORMATIONS COMPLÉMENTAIRES :\nLe billet sera contrôlé à l'entrée de l'événement. Nous vous conseillons vivement d'arriver au plus tard 15 minutes avant le début de l'événement. - Le jour de l'événement, Sciences Po décline toute responsabilité en cas de perte ou de vol du ticket - Pour vérifier la bonne qualité du billet, assurez-vous que les informations du billet, ainsi que le code barres sont bien lisibles. - Le billet est personnel et incessible. Lors des contrôles, vous devrez obligatoirement être munis d'une pièce d'identité, en cours de validité et avec photo : carte d'identité, carte d'étudiant, passeport, permis de conduire ou carte de séjour. - Le billet est uniquement valable pour l'événement, à la date et aux conditions y figurant. Dans les autres cas, ce titre sera considéré comme non valable. - Sciences Po décline toute responsabilité : pour les anomalies pouvant survenir en cours de réservation ou de traitement du billet. ";
		}

		$pdf->MultiCell(170, 5,$texte, 0, 'L', 1, 0, '', '', true);
		
		
		// ON PROTEGE LE FICHIER POUR EVITER LES MODIFICATIONS
		$permissions = array('modify', 'annot-forms', 'fill-forms', 'extract', 'assemble');
		$pdf->SetProtection($permissions);

		// ON EXPORTE LE FICHIER
		mkdir("../inscription/export/".date("M_Y"));
		
		$pdf->Output('../inscription/export/'.date("M_Y").'/billet_'.$uniqueID.'.pdf', 'F');
	}

	/*
	@ Construit le codebar du billet
	@
	@
	*/
	static function buildBarCode( $name, $code, $nameEvent, $dateEvent, $horaireEvent, $adress1Event, $adress2Event, $adress3Event, $adress4Event, $organisationEvent, $typePublic, $noEtudiant, $inscriptionVision ) {
		$objCode = new makeTicket() ;
		$objCode->setText('');
		$objCode->setType('C39');
		$objCode->hideCodeType();
		$objCode->setColors('#131313');
		$objCode -> setCode($code);
		$objCode -> setInfos($name, $code, $nameEvent, $dateEvent, $horaireEvent, $adress1Event, $adress2Event, $adress3Event, $adress4Event, $organisationEvent, $typePublic, $noEtudiant, $inscriptionVision );
		$objCode -> writeBarcodeFile('../inscription/img/code_EAN'.$code.'.png');
		return 'code_EAN'.$code.'.png';
	}


	/*
	@ Transforme caractères spéciaux en majuscule
	@
	@
	*/
	static function uc_strtoupper_fr($chaine){
		$chaine = strtoupper($chaine);
		$chaine = strtr($chaine, “äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø”,”ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ”);
		return $chaine;
	}

	/*
	@ Formate la date
	@
	@
	*/
	static function dateFormat($date){
		date_default_timezone_set("Europe/Paris");
		setlocale(LC_TIME, 'fr_FR');
		return utf8_encode(strftime('%d %B %Y',strtotime($date)));
	}

	/*
	@ Génère le code extene pour une session
	@
	@
	*/
	static function genereCode(){
		$code = '';
		for($i=0;$i<6;$i++){
			switch(rand(1,3)){
				case 1: $code.=chr(rand(48,57));  break; //0-9
				case 2: $code.=chr(rand(65,90));  break; //A-Z
				case 3: $code.=chr(rand(97,122)); break; //a-z
			}
		}
		return $code;
	} 

}

?>
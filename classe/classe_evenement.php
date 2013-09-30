<?php

include_once('../vars/config.php');
include_once('classe_connexion.php');
include_once('classe_fonctions.php');
include_once('classe_session.php');
//include_once('fonctions.php');
//include_once('connexion_vars.php');
include_once('class.phpmailer.php');
include_once('class.smtp.php');

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

	/**
	* get_events_organism récupération des événements d'un organisme à venir pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_events_organism($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauDesEvents=array();

			$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."sessions AS sps, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND spe.evenement_rubrique=spr.rubrique_id AND spg.groupe_organisme_id=%s  AND session_fin_datetime >=NOW() AND spg.groupe_id=spr.rubrique_groupe_id AND sps.evenement_id=spe.evenement_id GROUP BY spe.evenement_id", 
							func::GetSQLValueString($_id, "int"));
			$res = mysql_query($sql)or die(mysql_error());
			
			while($row = mysql_fetch_array($res)){
				$tableauDesEvents[] = $row['evenement_id']; 
			}
			return $tableauDesEvents;
		}
	}

	/**
	* get_events_partages récupération des événements partagés à venir pour le front office
	* @param $_id => id de l'organisme
	*/
	function get_events_partages($_id=1){
		$this->evenement_db->connect_db();

		if(isset($_id)){
			$tableauDesEvents=array();
		    $aujourdhui = date("Y-m-d").' 00:00:00';
			
			$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rel_evenement_rubrique as spre, ".TB."groupes as spg, ".TB."sessions AS sps WHERE spe.evenement_statut=3 AND evenement_datetime >=%s  AND spre.evenement_id=spe.evenement_id AND spg.groupe_id=spre.groupe_id AND spg.groupe_organisme_id=%s AND sps.evenement_id=spe.evenement_id GROUP BY spe.evenement_id", 
									func::GetSQLValueString($aujourdhui, "text"),
									func::GetSQLValueString($_id, "int"));

			$res = mysql_query($sql)or die(mysql_error());
		  
		  	while($row = mysql_fetch_array($res)){ 
				$tableauDesEvents[] = $row['evenement_id']; 
			}
			return $tableauDesEvents;
		}
	}
	
	/**
	* get_title affiche le titre d'un événement sur le front office en fonction de la langue
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function get_title($row, $lang){
		if($lang=="fr"){
			$titre = $row['evenement_titre'];
		}
		else{
			$titre = $row['evenement_titre_en'];
		}

		return $titre;
	}

	/**
	* get_organisateur affiche l'organisateur d'un événement sur le front office en fonction de la langue
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function get_organisateur($row, $lang){
		if($lang=="fr"){
			$organisateur = $row['evenement_organisateur'];
		}
		else{
			$organisateur = $row['evenement_organisateur_en'];
		}

		return $organisateur;
	}

	/**
	* affiche_resume affiche le résumé d'un événement sur le front office en fonction de la langue et retourne le résumé facebook
	* @param $row => tableau contenant les infos de l'événement
	* @param $lang => langue du front office
	*/
	function affiche_resume($row, $lang){
		if($lang=="fr"){
			$resumeGeneral = explode(" ",strip_tags($row['evenement_texte']));
			$resume = explode(" ",strip_tags($row['evenement_texte'],'<br>'));
		}
		else{
			$resumeGeneral = explode(" ",strip_tags($row['evenement_texte_en']));
			$resume = explode(" ",strip_tags($row['evenement_texte_en'],'<br>'));
		}

		$resumeFacebook = "";
		$borne = 15;
		$bornefacebook = 15;
		if (count($resume)<15){
			$borne = count($resume);
		}
		
		if (count($resumeGeneral)<15){
			$bornefacebook = count($resumeGeneral);
		}

		if($row['evenement_image']==""){
			$borne=60; 
			if (count($resume)<60){
				$borne = count($resume);
			}
		}
		
		if($row['evenement_image']==""){
			$bornefacebook=60; 
			if (count($resumeGeneral)<60){
				$bornefacebook = count($resumeGeneral);
			}
		}

		for($i = 0 ; $i < $borne ; $i++){
			if($i != ($borne-1)){
				echo $resume[$i]." ";
			}
			else{
				echo $resume[$i]."... &nbsp;";
			}
		}

		for($i = 0 ; $i < $bornefacebook ; $i++){
			if($i != ($bornefacebook-1)){
				str_replace('"','', $resumeGeneral[$i]);
				$resumeFacebook .= $resumeGeneral[$i]." ";
			}
			else{
				str_replace('"','', $resumeGeneral[$i]);
				$resumeFacebook .= $resumeGeneral[$i]."... &nbsp;";
			}
		}

		return $resumeFacebook;
	}

	/**
	* get_fin_event récupère la date de fin d'événement au format datetime
	* @param $_id => id de l'événement
	*/
	function get_fin_event($_id=1){
		$this->evenement_db->connect_db();
		$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($_id, "int"));
		$ressessions = mysql_query($sqlsessions) or die(mysql_error());
		$finEvenement="1980-01-01 00:00:00";
		while($row = mysql_fetch_array($ressessions)){
			if($row['session_fin_datetime']>$finEvenement){
				$finEvenement = $row['session_fin_datetime'];
			}
		}
		return $finEvenement;
	}

	/**
	* get_events_months récupération des différents mois des événements à venir du front office 
	* @param $evenements => tableau contenant l'ensemble des id des événements publiés à venir
	*/
	function get_events_months($evenements){
		$this->evenement_db->connect_db();
		$objDateTime = date("Y-m-d H:i:s", time());
		$tableauTest = array();
		$tableauMois = array();
		$indice = 0;
		foreach($evenements as $evenement){
			$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($evenement, "int"));
			$ressessions = mysql_query($sqlsessions) or die(mysql_error());
			$finEvenement="1980-01-01 00:00:00";
			while($rowsession = mysql_fetch_array($ressessions)){
				if($rowsession['session_fin_datetime']>$finEvenement){
					$finEvenement = $rowsession['session_fin_datetime'];
				}
			}
			if($finEvenement>$objDateTime){
				$sql = sprintf("SELECT EXTRACT(YEAR_MONTH FROM evenement_datetime) as leTest, EXTRACT(MONTH FROM evenement_datetime) as leMois, EXTRACT(YEAR FROM evenement_datetime) as lAnnee FROM ".TB."evenements WHERE evenement_id=%s", 
							func::GetSQLValueString($evenement, "int"));
				$res = mysql_query($sql)or die(mysql_error());
				$row = mysql_fetch_array($res);

				if(!in_array($row['leTest'], $tableauTest)){
					$tableauMois[$indice]['mois'] = $row['leMois'];
					$tableauMois[$indice]['annee'] = $row['lAnnee'];
					$tableauMois[$indice]['unique'] = $row['leTest'];
					$tableauTest[]=$row['leTest'];
					$indice++;
				}
			}
		}
		return $tableauMois;
	}

	/**
	* get_event_unique_month récupére le mois unique (année+mois) d'un événement
	* @param $_id => id de l'événement
	*/
	function get_event_unique_month($_id){
		$this->evenement_db->connect_db();
		$sql = sprintf("SELECT EXTRACT(YEAR_MONTH FROM evenement_datetime) as moisUnique FROM ".TB."evenements WHERE evenement_id=%s", 
							func::GetSQLValueString($_id, "int"));
		$res = mysql_query($sql)or die(mysql_error());
		$row = mysql_fetch_array($res);

		return $row['moisUnique'];
	}


	/**
	* get_event_infos récupére les infos pour le détail d'un événement
	* @param $_id => id de l'événement
	* @param $lang => langue du front office
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos($_id, $lang){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$sql = sprintf("SELECT * FROM ".TB."evenements as spe, ".TB."rubriques as spr WHERE spe.evenement_rubrique=spr.rubrique_id AND evenement_id=%s", 
							func::GetSQLValueString($_id, "int"));

		$res = mysql_query($sql)or die(mysql_error());
		$row = mysql_fetch_array($res);

		$finEvenement = $this->get_fin_event($row['evenement_id']);
		$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);
		
		$sqlsession1 = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id =%s LIMIT 1", func::GetSQLValueString($row['evenement_id'], "int"));
		$ressession1 = mysql_query($sqlsession1) or die(mysql_error());
		$rowsession1 = mysql_fetch_array($ressession1);

		if($rowsession1['session_langue']!="" && $rowsession1['session_langue']!="33"){ 
			foreach($langues_evenement as $cle => $valeur){
				if($rowsession1['session_langue']==$valeur){
					$langue=$cle;
				}
			}
		}
		else{
			$langue = "Français";
		}

		if($rowsession1['session_lieu']!=-1){
			$sqllieu = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id=%s", func::GetSQLValueString($rowsession1['session_lieu'], "int"));
			$reslieu = mysql_query($sqllieu) or die(mysql_error());
			$rowlieu = mysql_fetch_array($reslieu);
			$lieu = utf8_encode($rowlieu['lieu_nom']);
		}
		else{
			$lieu = $rowsession1['session_adresse1'];
		}

		if($rowsession1['session_code_batiment']!=-1){
			$sqlcode = sprintf("SELECT * FROM ".TB."codes_batiments WHERE code_batiment_id=%s", func::GetSQLValueString($rowsession1['session_code_batiment'], "int"));
			$rescode = mysql_query($sqlcode) or die(mysql_error());
			$rowcode = mysql_fetch_array($rescode);
			$batiment = utf8_encode($rowcode['code_batiment_nom']);
		}
		else{
			$batiment = $rowsession1['session_adresse2'];
		}

		$image = CHEMIN_IMAGES."evenement_".$row['evenement_id']."/grande-".$row['evenement_image']."?cache=".time();

		$inscription = func::detectURL($rowsession1['session_complement_type_inscription']);

		if($lang=="en"){
			$resume = explode(" ",strip_tags($row['evenement_texte_en'])); 
			$complet = "FULL";
        	$sinscrire = "SIGN UP";
		}
		else{
			$resume = explode(" ",strip_tags($row['evenement_texte'])); 
			$complet = "COMPLET";
        	$sinscrire = "S'INSCRIRE";
		}
		$resumeFacebook = "";
		for($i = 0 ; $i < 15 ; $i++){
			if($i != 14){
				$resumeFacebook .= $resume[$i]." ";
			}
			else{
				$resumeFacebook .= $resume[$i]."... &nbsp;";
			}
		}

		$sinscrireTexte = "";
		$session = new session();

		$rowSession = $session->get_session($row['evenement_id']);
        $sinscrireTexte = $session->affiche_statut_inscription($rowSession, $row, $sinscrire, $complet, $row['rubrique_couleur']); 

		if($lang=="en"){
			$retour->titre 	= $row['evenement_titre_en'];
			$retour->rubrique 	= $row['rubrique_titre_en'];
			$retour->organisateur 	= $row['evenement_organisateur_en'];
			$retour->coorganisateur 	= $row['evenement_coorganisateur_en'];
			$retour->lien 	= $rowsession1['session_lien_en'];
			$retour->texte_lien 	= $rowsession1['session_texte_lien_en'];
			$retour->texte 	= $row['evenement_texte_en'];
			$retour->facebook = "http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=".CHEMIN_FRONT_OFFICE."index.php?id=".$row['evenement_id']."&amp;picture=".ABSOLU_IMAGES."evenement_".$row['evenement_id']."/mini-".$row['evenement_image']."&amp;name=".$row['evenement_titre_en']."&amp;caption=".$horaires."&amp;description=".$resumeFacebook."&amp;message=Sciences Po | événements&amp;redirect_uri=".CHEMIN_FRONT_OFFICE; 
		}
		else{
			$retour->titre 	= $row['evenement_titre'];
			$retour->rubrique 	= $row['rubrique_titre'];
			$retour->organisateur 	= $row['evenement_organisateur'];
			$retour->coorganisateur 	= $row['evenement_coorganisateur'];
			$retour->lien 	= $rowsession1['session_lien'];
			$retour->texte_lien 	= $rowsession1['session_texte_lien'];
			$retour->texte 	= $row['evenement_texte'];
			$retour->facebook = "http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=".CHEMIN_FRONT_OFFICE."index.php?id=".$row['evenement_id']."&amp;picture=".ABSOLU_IMAGES."evenement_".$row['evenement_id']."/mini-".$row['evenement_image']."&amp;name=".$row['evenement_titre']."&amp;caption=".$horaires."&amp;description=".$resumeFacebook."&amp;message=Sciences Po | événements&amp;redirect_uri=".CHEMIN_FRONT_OFFICE; 
		}

		$retour->evenement_id 	= $row['evenement_id'];
		$retour->date 	= $horaires;
		$retour->rubrique_id 	= $row['rubrique_id'];
		$retour->couleur 	= $row['rubrique_couleur'];
		$retour->langue 	= $langue;
		$retour->lieu 	= $lieu;
		$retour->batiment 	= $batiment;
		$retour->inscription = $inscription;
		$retour->image 	= $image;
		$retour->texte_image 	= $row['evenement_texte_image'];
		$retour->twitter = "http://twitter.com/home?status=Je participe à cet événement Sciences Po :  ".CHEMIN_FRONT_OFFICE."index.php?id=".$row['evenement_id'];
		$retour->ical = "makeIcal.php?id=".$row['evenement_id'];
		$retour->sinscrire = $sinscrireTexte;
		return json_encode($retour);
	}

	/**
	* get_event_infos_inscription récupére les infos pour l'inscription à un événement simple
	* @param $_id => id de l'événement
	* @param $lang => langue du front office
	* @param $code => code externe passé par l'url
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos_inscription($_id, $lang, $code){

		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$sql = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($_id, "int"));
		$res = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);

		$finEvenement = $this->get_fin_event($row['evenement_id']);
		$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);
		
		if($row['session_lieu']!=-1){
			$sqllieu = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id=%s", func::GetSQLValueString($row['session_lieu'], "int"));
			$reslieu = mysql_query($sqllieu) or die(mysql_error());
			$rowlieu = mysql_fetch_array($reslieu);
			$lieu = utf8_encode($rowlieu['lieu_nom']);
		}
		else{
			$lieu = $row['session_adresse1'];
		}

		$alerteInterne = "";
		$alerteExterne = "";

		$casque = "";

		$interneOuvert = "";
		$interneComplet = "";
		$externeOuvert = "";
		$externeComplet = "";

		$toutComplet = "";
		$toutClos = "";

		$totalInterne = $row['session_places_internes_totales']+$row['session_places_internes_totales_visio'];
		$totalInternePrises = $row['session_places_internes_prises']+$row['session_places_internes_prises_visio'];
		$differenceInterneTotale = $totalInterne - $totalInternePrises;
		$differenceInterneAmphi = $row['session_places_internes_totales'] - $row['session_places_internes_prises'];
		$differenceInterneVisio = $row['session_places_internes_totales_visio'] - $row['session_places_internes_prises_visio'];

		$totalExterne = $row['session_places_externes_totales']+$row['session_places_externes_totales_visio'];
		$totalExternePrises = $row['session_places_externes_prises']+$row['session_places_externes_prises_visio'];
		$differenceExterneTotale = $totalExterne - $totalExternePrises;
		$differenceExterneAmphi = $row['session_places_externes_totales'] - $row['session_places_externes_prises'];
		$differenceExterneVisio = $row['session_places_externes_totales_visio'] - $row['session_places_externes_prises_visio'];

		if($row['session_statut_inscription']==1){
			if($differenceInterneAmphi==0){
				$alerteInterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
				nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			}
			if($differenceExterneAmphi==0){
				$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
				nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			}
		}
		else{
			$alerteInterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
			nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
			nous vous proposons de vous inscrire à la retransmission en direct.</p>";
		}
		
		//Cas où il reste des places internes
		if(($row['session_places_internes_totales']!=0 || $row['session_places_internes_totales_visio']!=0) && $differenceInterneTotale!=0){
			//Si les inscriptions sont encore ouvertes
			if((($differenceInterneAmphi!=0 && $row['session_statut_inscription']==1) || ($differenceInterneAmphi==0 && $row['session_statut_visio']==1) || ($row['session_statut_visio']==1 && $differenceInterneVisio!=0))){
				$interneOuvert = true;
				if($row['session_traduction']==1){
					$casque=true;
				}
			}
		}

		// cas où il y avait possibilité de s'inscrire en interne mais où toutes les places internes sont prises
		if(($row['session_places_internes_totales']!=0 || $row['session_places_internes_totales_visio']!=0) && $differenceInterneTotale==0){
			$interneComplet = true;
		}


		//Cas où il reste des places externes
		if(($row['session_places_externes_totales']!=0 || $row['session_places_externes_totales_visio']!=0) && $differenceExterneTotale!=0){
			//Si les inscriptions sont encore ouvertes et si on est bien en inscription externe (avec le code)
			if((($differenceExterneAmphi!=0 && $row['session_statut_inscription']==1) || ($differenceExterneAmphi==0 && $row['session_statut_visio']==1) || ($row['session_statut_visio']==1 && $differenceExterneVisio!=0)) && ($code!=NULL)){
				$externeOuvert = true;
				if($row['session_traduction']==1){
					$casque=true;
				}
			}
		}

		// cas où il y avait possibilité de s'inscrire en externe mais où toutes les places externes sont prises
		if(($row['session_places_externes_totales']!=0 || $row['session_places_externes_totales_visio']!=0) && $differenceExterneTotale==0 && $code!=NULL){
			$externeComplet = true;
		}

		if($differenceExterneTotale==0 && $differenceInterneTotale==0){
			$toutComplet = '<div class="plus_de_place"><p class="bit_small">Désolé Il n\'y a plus de places pour cet événement.</p></div>';
		}

		if($closInterne && $closExterne && !$interneOuvert && !$externeOuvert){
			$toutClos = '<div class="plus_de_place"><p class="bit_small">Désolé les inscriptions pour cet événement sont actuellement closes.</p></div>';
		}

		if($lang=="en"){
			$retour->titre 	= $row['evenement_titre_en'];
		}
		else{
			$retour->titre 	= $row['evenement_titre'];
		}

		$retour->session_id 	= $row['session_id'];
		$retour->evenement_id 	= $row['evenement_id'];
		$retour->date 	= $horaires;
		$retour->lieu 	= $lieu;
		$retour->casque  = $casque;
		$retour->interneOuvert = $interneOuvert;
		$retour->interneComplet = $interneComplet;
		$retour->externeOuvert  = $externeOuvert;
		$retour->externeComplet = $externeComplet;

		$retour->toutClos  = $toutClos;
		$retour->toutComplet = $toutComplet;

		$retour->alerteInterne = $alerteInterne;
		$retour->alerteExterne  = $alerteExterne;

		$retour->codeExterne  = $code;

		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";
		return json_encode($retour);
	}

	/**
	* get_event_infos_inscription_externe récupére les infos pour l'inscription externe à un événement simple via la page d'inscription externe
	* @param $_id => id de la session
	* @param $id_event => id de l'événement
	* @param $code => code externe passé par l'url
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos_inscription_externe($_id, $id_event, $code){

		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$sql = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($id_event, "int"));
		$res = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);

		$codeErreur = "";

		if($row['session_code_externe']==$code){
			$finEvenement = $this->get_fin_event($row['evenement_id']);
			$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,'fr');
			
			if($row['session_lieu']!=-1){
				$sqllieu = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id=%s", func::GetSQLValueString($row['session_lieu'], "int"));
				$reslieu = mysql_query($sqllieu) or die(mysql_error());
				$rowlieu = mysql_fetch_array($reslieu);
				$lieu = utf8_encode($rowlieu['lieu_nom']);
			}
			else{
				$lieu = $row['session_adresse1'];
			}

			$alerteExterne = "";

			$casque = "";

			$externeOuvert = "";
			$externeComplet = "";

			$totalExterne = $row['session_places_externes_totales']+$row['session_places_externes_totales_visio'];
			$totalExternePrises = $row['session_places_externes_prises']+$row['session_places_externes_prises_visio'];
			$differenceExterneTotale = $totalExterne - $totalExternePrises;
			$differenceExterneAmphi = $row['session_places_externes_totales'] - $row['session_places_externes_prises'];
			$differenceExterneVisio = $row['session_places_externes_totales_visio'] - $row['session_places_externes_prises_visio'];

			if($row['session_statut_inscription']==1){
				if($differenceExterneAmphi==0){
					$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
					nous vous proposons de vous inscrire à la retransmission en direct.</p>";
				}
			}
			else{
				$alerteExterne = "<p class=\"alerte_statut\">Le nombre de places disponibles pour cet événement étant atteint,
				nous vous proposons de vous inscrire à la retransmission en direct.</p>";
			}
			

			//Cas où il reste des places externes
			if(($row['session_places_externes_totales']!=0 || $row['session_places_externes_totales_visio']!=0) && $differenceExterneTotale!=0){
				//Si les inscriptions sont encore ouvertes et si on est bien en inscription externe (avec le code)
				if((($differenceExterneAmphi!=0 && $row['session_statut_inscription']==1) || ($differenceExterneAmphi==0 && $row['session_statut_visio']==1) || ($row['session_statut_visio']==1 && $differenceExterneVisio!=0)) && ($code!=NULL)){
					$externeOuvert = true;
					if($row['session_traduction']==1){
						$casque=true;
					}
				}
			}

			// cas où il y avait possibilité de s'inscrire en externe mais où toutes les places externes sont prises
			if(($row['session_places_externes_totales']!=0 || $row['session_places_externes_totales_visio']!=0) && $differenceExterneTotale==0 && $code!=NULL){
				$externeComplet = true;
			}
		}
		else{
			$codeErreur = '<div class="plus_de_place"><p class="bit_small">Désolé Il n\'est pas possible de vous inscrire à cet événement.</p></div>';
		}
		

		$retour->titre 	= $row['evenement_titre'];
		$retour->session_id 	= $row['session_id'];
		$retour->evenement_id 	= $row['evenement_id'];
		$retour->date 	= $horaires;
		$retour->lieu 	= $lieu;
		$retour->casque  = $casque;
		$retour->externeOuvert  = $externeOuvert;
		$retour->externeComplet = $externeComplet;

		$retour->alerteExterne  = $alerteExterne;
		$retour->codeErreur  = $codeErreur;

		$retour->codeExterne  = $code;

		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";
		return json_encode($retour);
	}

	/**
	* get_event_infos_inscription_multiple récupére les infos pour l'inscription à un événement multiple
	* @param $_id => id de l'événement
	* @param $lang => langue du front office
	* @param $code => code externe passé par l'url
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos_inscription_multiple($_id, $lang, $code){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$session = new session();

		$horairesSessions = array();
		$lesSessions = array();

		$sql = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s LIMIT 1", 
							func::GetSQLValueString($_id, "int"));
		$res = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);


		$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($_id, "int"));
		$resSessions = mysql_query($sqlSessions) or die(mysql_error());
		$indice = 0;

		$interneOuvert = "";
		$externeOuvert = "";

		$toutComplet = "";


		while($rowSession = mysql_fetch_array($resSessions)){ 
			$sqllieux = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", 
								func::GetSQLValueString($rowSession['session_lieu'], "int"));
			$reslieux = mysql_query($sqllieux) or die(mysql_error());
			$rowlieu = mysql_fetch_array($reslieux);

			$lesSessions[$indice]['identifiant'] = $rowSession['session_id'];
			$lesSessions[$indice]['nom'] = $rowSession['session_nom'];
			$lesSessions[$indice]['pascomplete'] = "";
			$lesSessions[$indice]['pascompleteexterne'] = "";
			if($rowSession['session_traduction']==1){
				$lesSessions[$indice]['casque'] = true;
			}
			else{
				$lesSessions[$indice]['casque'] = "";
			}

			$lesSessions[$indice]['horaire'] = $session->get_horaires_session($rowSession['session_debut_datetime'], $rowSession['session_fin_datetime']);
			$lesSessions[$indice]['lieu'] = utf8_encode($rowlieu['lieu_nom']);

			$totalInterne = $rowSession['session_places_internes_totales']+$rowSession['session_places_internes_totales_visio'];
			$totalInternePrises = $rowSession['session_places_internes_prises']+$rowSession['session_places_internes_prises_visio'];
			$differenceInterneTotale = $totalInterne - $totalInternePrises;
			$differenceInterneAmphi = $rowSession['session_places_internes_totales'] - $rowSession['session_places_internes_prises'];
			$differenceInterneVisio = $rowSession['session_places_internes_totales_visio'] - $rowSession['session_places_internes_prises_visio'];

			if($rowSession['session_statut_inscription']==1 && $differenceInterneAmphi!=0){
				$lesSessions[$indice]['pascomplete'] = true;
				$lesSessions[$indice]['placement'] = ""; 
				$interneOuvert = true;
			}
			else{
				if($rowSession['session_statut_visio']==1 && $differenceInterneVisio!=0){
					$lesSessions[$indice]['pascomplete'] = true;
					$lesSessions[$indice]['placement'] = true;
					$interneOuvert = true; 
				}
			}


			$totalExterne = $rowSession['session_places_externes_totales']+$rowSession['session_places_externes_totales_visio'];
			$totalExternePrises = $rowSession['session_places_externes_prises']+$rowSession['session_places_externes_prises_visio'];
			$differenceExterneTotale = $totalExterne - $totalExternePrises;
			$differenceExterneAmphi = $rowSession['session_places_externes_totales'] - $rowSession['session_places_externes_prises'];
			$differenceExterneVisio = $rowSession['session_places_externes_totales_visio'] - $rowSession['session_places_externes_prises_visio'];

			if($rowSession['session_statut_inscription']==1 && $differenceExterneAmphi!=0){
				$lesSessions[$indice]['pascompleteexterne'] = true;
				$lesSessions[$indice]['placementexterne'] = ""; 
				$externeOuvert = true;
			}
			else{
				if($rowSession['session_statut_visio']==1 && $differenceExterneVisio!=0){
					$lesSessions[$indice]['pascompleteexterne'] = true;
					$lesSessions[$indice]['placementexterne'] = true; 
					$externeOuvert = true;
				}
			}			
			$indice++;
		}

		if($interneOuvert=="" && $externeOuvert==""){
			$toutComplet = '<div class="plus_de_place"><p class="bit_small">Désolé Il n\'y a plus de places pour cet événement.</p></div>';
		}

		$finEvenement = $this->get_fin_event($_id);
		$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);

		if($lang=="en"){
			$retour->titre 	= $row['evenement_titre_en'];
		}
		else{
			$retour->titre 	= $row['evenement_titre'];
		}

		$retour->evenement_id 	= $_id;
		$retour->date 	= $horaires;
		$retour->sessions 	= $lesSessions;
		$retour->codeExterne  = $code;
		$retour->interneOuvert  = $interneOuvert;
		$retour->externeOuvert  = $externeOuvert;
		$retour->toutComplet  = $toutComplet;
		$retour->externeComplet = '<div class="plus_de_place"><p class="bit_small">Désolé Il n\'y a plus de places pour cet événement.</p></div>';
		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";
		return json_encode($retour);
	}


	/**
	* get_event_infos_inscription_multiple_externe récupére les infos pour l'inscription externe à un événement multiple via la page d'inscription externe
	* @param $_id => id de l'événement
	* @param $code => code externe passé par l'url
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function get_event_infos_inscription_multiple_externe($_id, $code){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$session = new session();

		$horairesSessions = array();
		$lesSessions = array();

		$sql = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s LIMIT 1", 
							func::GetSQLValueString($_id, "int"));
		$res = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($res);


		$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($_id, "int"));
		$resSessions = mysql_query($sqlSessions) or die(mysql_error());
		$indice = 0;

		$externeOuvert = "";

		$codeErreur = "";

		if($row['session_code_externe']==$code){
			while($rowSession = mysql_fetch_array($resSessions)){ 
				$sqllieux = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", 
									func::GetSQLValueString($rowSession['session_lieu'], "int"));
				$reslieux = mysql_query($sqllieux) or die(mysql_error());
				$rowlieu = mysql_fetch_array($reslieux);

				$lesSessions[$indice]['identifiant'] = $rowSession['session_id'];
				$lesSessions[$indice]['nom'] = $rowSession['session_nom'];
				$lesSessions[$indice]['pascomplete'] = "";
				if($rowSession['session_traduction']==1){
					$lesSessions[$indice]['casque'] = true;
				}
				else{
					$lesSessions[$indice]['casque'] = "";
				}

				$lesSessions[$indice]['horaire'] = $session->get_horaires_session($rowSession['session_debut_datetime'], $rowSession['session_fin_datetime']);
				$lesSessions[$indice]['lieu'] = utf8_encode($rowlieu['lieu_nom']);

				$totalExterne = $rowSession['session_places_externes_totales']+$rowSession['session_places_externes_totales_visio'];
				$totalExternePrises = $rowSession['session_places_externes_prises']+$rowSession['session_places_externes_prises_visio'];
				$differenceExterneTotale = $totalExterne - $totalExternePrises;
				$differenceExterneAmphi = $rowSession['session_places_externes_totales'] - $rowSession['session_places_externes_prises'];
				$differenceExterneVisio = $rowSession['session_places_externes_totales_visio'] - $rowSession['session_places_externes_prises_visio'];

				if($rowSession['session_statut_inscription']==1 && $differenceExterneAmphi!=0){
					$lesSessions[$indice]['pascomplete'] = true;
					$lesSessions[$indice]['placement'] = ""; 
					$externeOuvert = true;
				}
				else{
					if($rowSession['session_statut_visio']==1 && $differenceExterneVisio!=0){
						$lesSessions[$indice]['pascomplete'] = true;
						$lesSessions[$indice]['placement'] = true; 
						$externeOuvert = true;
					}
				}			
				$indice++;
			}
		}
		else{
			$codeErreur = '<div class="plus_de_place"><p class="bit_small">Désolé Il n\'est pas possible de vous inscrire à cet événement.</p></div>';
		}

		$finEvenement = $this->get_fin_event($_id);
		$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);

		$retour->titre 	= $row['evenement_titre'];
		$retour->evenement_id 	= $_id;
		$retour->date 	= $horaires;
		$retour->sessions 	= $lesSessions;
		$retour->codeExterne  = $code;
		$retour->externeOuvert  = $externeOuvert;
		$retour->codeErreur  = $codeErreur;
		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";
		return json_encode($retour);
	}

	/**
	* make_inscription_multiple réalise une inscription interne à un événement avec une session unique
	* @param $sessions => sessions cochées dans le formulaire
	* @param $casques => casques cochés dans le formulaire
	* @param $_id => id l'événement multisessions
	* @param $login => login LDAP passé par le formulaire
	* @param $password => mot de passe LDAP passé par le formulaire
	* @param $titre => titre de l'événement passé par le formulaire
	* @param $date => date de l'événement passé par le formulaire
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function make_inscription_multiple($sessions, $casques, $_id, $login, $password, $titre, $date){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$session = new session();

		session_start();

		$erreurLDAP = "";

		$champVide = "";
		$erreurChamps = "";

		$lesSessions = array();

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

		$sqlSession = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s LIMIT 1", func::GetSQLValueString($_id, "int"));
		$resSession = mysql_query($sqlSession) or die(mysql_error());
		$rowSession = mysql_fetch_array($resSession);

		if(isset($_SESSION['nomSP'])){
			if(isset($sessions)){
				$indice=0;
				$inscritPartout=true;
				$tousDerniereMinute=true;
				foreach($sessions as $uneSession){
					$lesSessions[$indice]['inscriptionOK']="";
					$lesSessions[$indice]['dejaInscrit']="";
					$lesSessions[$indice]['completeDerniereMinute']="";
					$lesSessions[$indice]['numero']="";
					$lesSessions[$indice]['type_inscription']="";
					$lesSessions[$indice]['endroit']="";
					$lesSessions[$indice]['horaire']="";
					$lesSessions[$indice]['nom']="";

					$testVisio = false;
					$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND sps.session_id =%s", func::GetSQLValueString($uneSession, "int"));	
					$resSessions = mysql_query($sqlSessions) or die(mysql_error());
					$rowsession = mysql_fetch_array($resSessions);

					$lesSessions[$indice]['session_nom'] = $rowsession['session_nom'];
					$lesSessions[$indice]['horaire'] = $session->get_horaires_session($rowsession['session_debut_datetime'], $rowsession['session_fin_datetime']);

					if($rowsession['session_statut_inscription']==1){
						$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
						if($estComplete){
							$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_internes_totales_visio", "session_places_internes_prises_visio");
							if(!$estComplete){
								$affichageRecap = "Retransmission";
								$type_inscription = "visio interne";
								$testVisio = true;
							}
						}
						else{
							$affichageRecap = "Amphithéâtre";
							$type_inscription = "amphi interne";
						}
					}
					else{
						$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_internes_totales_visio", "session_places_internes_prises_visio");
						$affichageRecap = "Retransmission";
						$type_inscription = "visio interne";
					}
					$lesSessions[$indice]['type_inscription']=$affichageRecap;


					if($estComplete){
						$lesSessions[$indice]['completeDerniereMinute'] = "La dernière place pour la conférence ".$rowsession['session_nom']." vient malheureusement d'être réservée.";
					}
					else{
						$tousDerniereMinute = "";
						$testDejaInscrit = $session->deja_inscrit($_SESSION['mailSP'],$rowsession['session_id']);
						if(!$testDejaInscrit){
							$inscritPartout="";
							$dateInscription = time();
							$dateTime = date('Y-m-d H:i:s');
							
							$avecCasque = 0;
							foreach($casques as $casque){
								if($casque==$rowsession['session_id']){
									$avecCasque=1;
								}
							}

							$sqlinsert ="INSERT INTO ".TB."inscrits VALUES ('', '".$rowsession['evenement_id']."', '".$rowsession['session_id']."', '".addslashes($_SESSION['nomSP'])."', '".addslashes($_SESSION['prenomSP'])."', '".addslashes($_SESSION['mailSP'])."', 'Sciences Po', '".$_SESSION['typeSP']."', '".$type_inscription."','".$avecCasque."','','','".$dateInscription."','".$dateTime."','')"; 
							$resinsert = mysql_query($sqlinsert) or die(mysql_error());
							$lastIdInsert = mysql_insert_id(); 

							if($rowsession['session_statut_inscription']==1 && !$testVisio){				
								$session->incremente_nb_inscrits($rowsession['session_id'], "session_places_internes_prises");
								
								if($rowsession['session_lieu']!=-1){							
									$sqlLieu =sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", func::GetSQLValueString($rowsession['session_lieu'], "int"));
									$resLieu = mysql_query($sqlLieu) or die(mysql_error());
									$rowLieu = mysql_fetch_array($resLieu);
									$endroit = $rowLieu['lieu_nom'];
								}
								else{
									$endroit = $rowsession['session_adresse1'];
								}
								$endroitMessage = 0;
							}
							else{
								$session->incremente_nb_inscrits($rowsession['session_id'], "session_places_internes_prises_visio");
								$endroit = "Retransmission";
								$endroitMessage = 1;
							}

							$lesSessions[$indice]['endroit']=$endroit;

							$sqlcountinscrit = sprintf("SELECT COUNT(*) AS nb FROM ".TB."inscrits WHERE inscrit_session_id=%s", func::GetSQLValueString($rowsession['session_id'], "int"));
							$sqlcountinscrits = mysql_query($sqlcountinscrit) or die(mysql_error());
							$rescountinscrits = mysql_fetch_array($sqlcountinscrits);
							
							$uniqueId = func::uniqueID($rowsession['session_id'], $rescountinscrits['nb']);
				            $lesSessions[$indice]['numero']=$uniqueId;

							$sqlupdate = sprintf("UPDATE ".TB."inscrits SET inscrit_unique_id=%s WHERE inscrit_id =%s", 
														func::GetSQLValueString($uniqueId, "text"),
														func::GetSQLValueString($lastIdInsert, "int"));

							mysql_query($sqlupdate) or die(mysql_error());
							$dateBillet=date("Y-m-d", $rowsession['session_debut']);
							$dateMail=date("d/m/Y", $rowsession['session_debut']);
							$heureDebut = date("H:i", $rowsession['session_debut']);
							$heureFin = date("H:i", $rowsession['session_fin']);
							
							if($heureFin=="23:59"){
								$heureBillet="à ".$heureDebut;
							}
							else{
								$heureBillet="de ".$heureDebut." à ".$heureFin;
							}
							
							if($avecCasque==1){
								$leCasque = true;
							}
							else{
								$leCasque = false;
							}
							
							$sqlBan = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
							$resBan = mysql_query($sqlBan)or die(mysql_error());
							$rowBan = mysql_fetch_array($resBan);
							
							if($rowBan['organisme_mentions']==""){
								$mentions = "";
							}
							else{
								$mentions = $rowBan['organisme_mentions'];
							}
							
							//func::createBillet($uniqueId, $rowsession['session_nom'], $dateBillet, $heureBillet, $_SESSION['nomSP'], $_SESSION['prenomSP'], 'interne', $rowsession['evenement_organisateur'], $rowsession['session_adresse2'], utf8_encode($endroit), $leCasque, $mentions);
							
							$cheminBillet = "../inscription/export/".date("M_Y")."/billet_".$uniqueId.".pdf";
							$session_nom = $rowsession['session_nom'];
						
							func::envoiMail($_SESSION['nomSP'], $_SESSION['prenomSP'], $_SESSION['mailSP'], $session_nom, $dateMail, $cheminBillet, $endroitMessage);
							
							func::envoiAlerte($rowsession['session_id']);

							if($rowsession['session_statut_inscription']==1 && !$testVisio){
								$estCompleteBis = $session->test_session_complete($rowsession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
								if($estCompleteBis){
									$estCompleteBis = $session->test_session_complete($rowsession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
									if($estCompleteBis){
										$totale=true;
										$session->bascule_inscription($rowsession['session_id'], $totale);
									}
									else{
										$totale=false;
										$session->bascule_inscription($rowsession['session_id'], $totale);
									}
								}
							}
							$lesSessions[$indice]['inscriptionOK']=true;
						}
						else{
							$lesSessions[$indice]['dejaInscrit']="Vous êtes déjà inscrit pour cette session.";
						}
					}
					$indice++;
				}
			}
			else{
				$erreurChamps="Il faut choisir au moins une session.";
			}
		}



		//On récupère la totalité des sessions à afficher en cas d'erreur du formulaire
		$toutesLesSessions = array();
		$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($_id, "int"));
		$resSessions = mysql_query($sqlSessions) or die(mysql_error());
		$indice = 0;
		while($rowSession = mysql_fetch_array($resSessions)){ 
			$sqllieux = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", 
								func::GetSQLValueString($rowSession['session_lieu'], "int"));
			$reslieux = mysql_query($sqllieux) or die(mysql_error());
			$rowlieu = mysql_fetch_array($reslieux);

			$totalInterne = $rowSession['session_places_internes_totales']+$rowSession['session_places_internes_totales_visio'];
			$totalInternePrises = $rowSession['session_places_internes_prises']+$rowSession['session_places_internes_prises_visio'];
			$differenceInterneTotale = $totalInterne - $totalInternePrises;
			$differenceInterneAmphi = $rowSession['session_places_internes_totales'] - $rowSession['session_places_internes_prises'];
			$differenceInterneVisio = $rowSession['session_places_internes_totales_visio'] - $rowSession['session_places_internes_prises_visio'];

			$toutesLesSessions[$indice]['identifiant'] = $rowSession['session_id'];
			$toutesLesSessions[$indice]['nom'] = $rowSession['session_nom'];

			$toutesLesSessions[$indice]['pascomplete'] = "";
			if($rowSession['session_traduction']==1){
				$toutesLesSessions[$indice]['casque'] = true;
			}
			else{
				$toutesLesSessions[$indice]['casque'] = "";
			}

			$toutesLesSessions[$indice]['horaire'] = $session->get_horaires_session($rowSession['session_debut_datetime'], $rowSession['session_fin_datetime']);
			$toutesLesSessions[$indice]['lieu'] = utf8_encode($rowlieu['lieu_nom']);

			if($rowSession['session_statut_inscription']==1 && $differenceInterneAmphi!=0){
				$toutesLesSessions[$indice]['pascomplete'] = true;
				$toutesLesSessions[$indice]['placement'] = ""; 
			}
			else{
				if($rowSession['session_statut_visio']==1 && $differenceInterneVisio!=0){
					$toutesLesSessions[$indice]['pascomplete'] = true;
					$toutesLesSessions[$indice]['placement'] = true; 
				}
			}
			$indice++;
		}

		$retour->titre_bloc 	= "Vous êtes bien inscrit à l'événement.";
		$retour->evenement_id 	= $_id;
		$retour->titre 	= $titre;
		$retour->date 	= $date;
		$retour->infos_inscription = "Vos informations d'inscription sont les suivantes :";
		$retour->nom  = $_SESSION['nomSP'];
		$retour->prenom  = $_SESSION['prenomSP'];
		$retour->sessions 	= $lesSessions;
		$retour->toutesLesSessions 	= $toutesLesSessions;
		$retour->important = "<strong>IMPORTANT :</strong> ".count($lesSessions)." mail(s) contenant vos billets au format .pdf vont vous être envoyés à l'adresse ".$_SESSION['mailSP'].". <strong>Veuillez imprimer le billet et vous présenter à l'accueil à l'adresse spécifiée.</strong>";
		$retour->erreurLDAP = $erreurLDAP;
		$retour->erreurChamps = $erreurChamps;
		$retour->champVide = $champVide;
		$retour->inscritPartout = $inscritPartout;
		$retour->tousDerniereMinute = $tousDerniereMinute;
		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";

		session_unset();
		return json_encode($retour);
	}


	/**
	* make_inscription_externe_multiple réalise une inscription interne à un événement avec une session unique
	* @param $sessions => sessions cochées dans le formulaire
	* @param $casques => casques cochés dans le formulaire
	* @param $_id => id l'événement multisessions
	* @param $nom => nom de l'inscrit
	* @param $prenom => prénom de l'inscrit
	* @param $mail => mail de l'inscrit
	* @param $entreprise => entreprise de l'inscrit
	* @param $fonction => fonction de l'inscrit
	* @param $titre => titre de l'événement passé par le formulaire
	* @param $date => date de l'événement passé par le formulaire
	* @return JSON => l'objet JSON contiendra les infos de l'événement
	*/
	function make_inscription_externe_multiple($sessions, $casques, $_id, $nom, $prenom, $mail, $entreprise, $fonction, $titre, $date){
		$this->evenement_db->connect_db();
		$retour = new stdClass();

		$session = new session();
		session_start();

		$erreurChamps = "";

		$lesSessions = array();

		$sqlSession = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s LIMIT 1", func::GetSQLValueString($_id, "int"));
		$resSession = mysql_query($sqlSession) or die(mysql_error());
		$rowSession = mysql_fetch_array($resSession);

		if(isset($nom)){
			if(isset($sessions)){
				$indice=0;
				$erreurChampsTest = func::testeChamps($nom, $prenom, $mail);
				$inscritPartout = true;
				$tousDerniereMinute = true;
				if($erreurChampsTest){
					foreach($sessions as $uneSession){
						$lesSessions[$indice]['inscriptionOK']="";
						$lesSessions[$indice]['dejaInscrit']="";
						$lesSessions[$indice]['completeDerniereMinute']="";
						$lesSessions[$indice]['numero']="";
						$lesSessions[$indice]['type_inscription']="";
						$lesSessions[$indice]['endroit']="";
						$lesSessions[$indice]['horaire']="";
						$lesSessions[$indice]['nom']="";

						$testVisio = false;

						$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND sps.session_id =%s", func::GetSQLValueString($uneSession, "int"));	
						$resSessions = mysql_query($sqlSessions) or die(mysql_error());
						$rowsession = mysql_fetch_array($resSessions);

						$lesSessions[$indice]['session_nom'] = $rowsession['session_nom'];
						$lesSessions[$indice]['horaire'] = $session->get_horaires_session($rowsession['session_debut_datetime'], $rowsession['session_fin_datetime']);

						if($rowsession['session_statut_inscription']==1){
							$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
							if($estComplete){
								$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_externes_totales_visio", "session_places_externes_prises_visio");
								if(!$estComplete){
									$affichageRecap = "Retransmission";
									$type_inscription = "visio externe";
									$testVisio = true;
								}
							}
							else{
								$affichageRecap = "Amphithéâtre";
								$type_inscription = "amphi externe";
							}
						}
						else{
							$estComplete = $session->test_session_complete($rowsession['session_id'], "session_places_externes_totales_visio", "session_places_externes_prises_visio");
							$affichageRecap = "Retransmission";
							$type_inscription = "visio externe";
						}
						$lesSessions[$indice]['type_inscription']=$affichageRecap;


						if($estComplete){
							$lesSessions[$indice]['completeDerniereMinute'] = "La dernière place pour la conférence ".$rowsession['session_nom']." vient malheureusement d'être réservée.";
						}
						else{
							$tousDerniereMinute = "";
							$testDejaInscrit = $session->deja_inscrit($mail,$rowsession['session_id']);
							if(!$testDejaInscrit){
								$inscritPartout = "";
								$dateInscription = time();
								$dateTime = date('Y-m-d H:i:s');
								
								$avecCasque = 0;
								foreach($casques as $casque){
									if($casque==$rowsession['session_id']){
										$avecCasque=1;
									}
								}

								$sqlinsert ="INSERT INTO ".TB."inscrits VALUES ('', '".$rowsession['evenement_id']."', '".$rowsession['session_id']."', '".addslashes($nom)."', '".addslashes($prenom)."', '".addslashes($mail)."', '".addslashes($entreprise)."', '".addslashes($fonction)."', '".$type_inscription."','".$avecCasque."','','','".$dateInscription."','".$dateTime."','')"; 
								$resinsert = mysql_query($sqlinsert) or die(mysql_error());
								$lastIdInsert = mysql_insert_id(); 

								if($rowsession['session_statut_inscription']==1 && !$testVisio){				
									$session->incremente_nb_inscrits($rowsession['session_id'], "session_places_externes_prises");
									
									if($rowsession['session_lieu']!=-1){							
										$sqlLieu =sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", func::GetSQLValueString($rowsession['session_lieu'], "int"));
										$resLieu = mysql_query($sqlLieu) or die(mysql_error());
										$rowLieu = mysql_fetch_array($resLieu);
										$endroit = $rowLieu['lieu_nom'];
									}
									else{
										$endroit = $rowsession['session_adresse1'];
									}
									$endroitMessage = 0;
								}
								else{
									$session->incremente_nb_inscrits($rowsession['session_id'], "session_places_externes_prises_visio");
									$endroit = "Retransmission";
									$endroitMessage = 1;
								}

								$lesSessions[$indice]['endroit']=$endroit;

								$sqlcountinscrit = sprintf("SELECT COUNT(*) AS nb FROM ".TB."inscrits WHERE inscrit_session_id=%s", func::GetSQLValueString($rowsession['session_id'], "int"));
								$sqlcountinscrits = mysql_query($sqlcountinscrit) or die(mysql_error());
								$rescountinscrits = mysql_fetch_array($sqlcountinscrits);
								
								$uniqueId = func::uniqueID($rowsession['session_id'], $rescountinscrits['nb']);
					            $lesSessions[$indice]['numero']=$uniqueId;

								$sqlupdate = sprintf("UPDATE ".TB."inscrits SET inscrit_unique_id=%s WHERE inscrit_id =%s", 
															func::GetSQLValueString($uniqueId, "text"),
															func::GetSQLValueString($lastIdInsert, "int"));

								mysql_query($sqlupdate) or die(mysql_error());
								$dateBillet=date("Y-m-d", $rowsession['session_debut']);
								$dateMail=date("d/m/Y", $rowsession['session_debut']);
								$heureDebut = date("H:i", $rowsession['session_debut']);
								$heureFin = date("H:i", $rowsession['session_fin']);
								
								if($heureFin=="23:59"){
									$heureBillet="à ".$heureDebut;
								}
								else{
									$heureBillet="de ".$heureDebut." à ".$heureFin;
								}
								
								if($avecCasque==1){
									$leCasque = true;
								}
								else{
									$leCasque = false;
								}
								
								$sqlBan = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
								$resBan = mysql_query($sqlBan)or die(mysql_error());
								$rowBan = mysql_fetch_array($resBan);
								
								if($rowBan['organisme_mentions']==""){
									$mentions = "";
								}
								else{
									$mentions = $rowBan['organisme_mentions'];
								}
								
								//func::createBillet($uniqueId, $rowsession['session_nom'], $dateBillet, $heureBillet, $nom, $prenom, 'externe', $rowsession['evenement_organisateur'], $rowsession['session_adresse2'], utf8_encode($endroit), $leCasque, $mentions);
								
								$cheminBillet = "../inscription/export/".date("M_Y")."/billet_".$uniqueId.".pdf";
								$session_nom = $rowsession['session_nom'];
							
								func::envoiMail($nom, $prenom, $mail, $session_nom, $dateMail, $cheminBillet, $endroitMessage);
								
								func::envoiAlerte($rowsession['session_id']);

								if($rowsession['session_statut_inscription']==1 && !$testVisio){
									$estCompleteBis = $session->test_session_complete($rowsession['session_id'], "session_places_internes_totales", "session_places_internes_prises");
									if($estCompleteBis){
										$estCompleteBis = $session->test_session_complete($rowsession['session_id'], "session_places_externes_totales", "session_places_externes_prises");
										if($estCompleteBis){
											$totale=true;
											$session->bascule_inscription($rowsession['session_id'], $totale);
										}
										else{
											$totale=false;
											$session->bascule_inscription($rowsession['session_id'], $totale);
										}
									}
								}
								$lesSessions[$indice]['inscriptionOK']=true;
							}
							else{
								$lesSessions[$indice]['dejaInscrit']="Vous êtes déjà inscrit pour cette session.";
							}
						}
						$indice++;
					}
				}
				else{
					$erreurChamps = "Les informations que vous avez fournies ne permettent pas de vous identifier.";
				}
			}
			else{
				$erreurChamps = "Il faut choisir au moins une session.";
			}
		}

		//On récupère la totalité des sessions à afficher en cas d'erreur du formulaire
		$toutesLesSessions = array();
		$sqlSessions = sprintf("SELECT * FROM ".TB."sessions AS sps, ".TB."evenements AS spe WHERE sps.evenement_id=spe.evenement_id AND spe.evenement_id =%s", 
							func::GetSQLValueString($_id, "int"));
		$resSessions = mysql_query($sqlSessions) or die(mysql_error());
		$indice = 0;
		while($rowSession = mysql_fetch_array($resSessions)){ 
			$sqllieux = sprintf("SELECT * FROM ".TB."lieux WHERE lieu_id =%s", 
								func::GetSQLValueString($rowSession['session_lieu'], "int"));
			$reslieux = mysql_query($sqllieux) or die(mysql_error());
			$rowlieu = mysql_fetch_array($reslieux);

			$totalExterne = $rowSession['session_places_externes_totales']+$rowSession['session_places_externes_totales_visio'];
			$totalExternePrises = $rowSession['session_places_externes_prises']+$rowSession['session_places_externes_prises_visio'];
			$differenceExterneTotale = $totalExterne - $totalExternePrises;
			$differenceExterneAmphi = $rowSession['session_places_externes_totales'] - $rowSession['session_places_externes_prises'];
			$differenceExterneVisio = $rowSession['session_places_externes_totales_visio'] - $rowSession['session_places_externes_prises_visio'];

			$toutesLesSessions[$indice]['identifiant'] = $rowSession['session_id'];
			$toutesLesSessions[$indice]['nom'] = $rowSession['session_nom'];

			$toutesLesSessions[$indice]['pascomplete'] = "";
			if($rowSession['session_traduction']==1){
				$toutesLesSessions[$indice]['casque'] = true;
			}
			else{
				$toutesLesSessions[$indice]['casque'] = "";
			}

			$toutesLesSessions[$indice]['horaire'] = $session->get_horaires_session($rowSession['session_debut_datetime'], $rowSession['session_fin_datetime']);
			$toutesLesSessions[$indice]['lieu'] = utf8_encode($rowlieu['lieu_nom']);

			if($rowSession['session_statut_inscription']==1 && $differenceExterneAmphi!=0){
				$toutesLesSessions[$indice]['pascomplete'] = true;
				$toutesLesSessions[$indice]['placement'] = ""; 
			}
			else{
				if($rowSession['session_statut_visio']==1 && $differenceexterneVisio!=0){
					$toutesLesSessions[$indice]['pascomplete'] = true;
					$toutesLesSessions[$indice]['placement'] = true; 
				}
			}
			$indice++;
		}

		$retour->titre_bloc 	= "Vous êtes bien inscrit à l'événement.";
		$retour->evenement_id 	= $_id;
		$retour->titre 	= $titre;
		$retour->date 	= $date;
		$retour->infos_inscription = "Vos informations d'inscription sont les suivantes :";
		$retour->nom  = $nom;
		$retour->prenom  = $prenom;
		$retour->sessions 	= $lesSessions;
		$retour->toutesLesSessions 	= $toutesLesSessions;
		$retour->important = "<strong>IMPORTANT :</strong> ".count($lesSessions)." mail(s) contenant vos billets au format .pdf vont vous être envoyés à l'adresse ".$mail.". <strong>Veuillez imprimer le billet et vous présenter à l'accueil à l'adresse spécifiée.</strong>";
		$retour->erreurChamps = $erreurChamps;
		$retour->inscritPartout = $inscritPartout;
		$retour->tousDerniereMinute = $tousDerniereMinute;
		$retour->mention = "Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour l'exercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris";

		session_unset();
		return json_encode($retour);
	}

	
	function get_default_template($_template=NULL,$_image=NULL){
		// exemple pour le fonctionnement d'un template
		// 
		// 
		$contents = "<!--debut du contenu-->";
		$contents .= '<link rel="template_front/'.$_template.'/style.css">';

		$image = $_image;

		ob_start();

		include(REAL_LOCAL_PATH.'template_front/'.$_template.'/index.php');

		$contents .= ob_get_contents();

		ob_end_clean();

		return $contents;

	}

}
	
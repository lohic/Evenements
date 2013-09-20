<?php

include_once('../vars/config.php');
include_once('classe_connexion.php');
include_once('classe_fonctions.php');
include_once('classe_session.php');
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
		while($rowsession = mysql_fetch_array($ressessions)){
			if($rowsession['session_fin_datetime']>$finEvenement){
				$finEvenement = $rowsession['session_fin_datetime'];
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
	
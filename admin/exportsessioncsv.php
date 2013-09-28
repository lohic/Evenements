<?php

// connection to data base
include('connect.php');

// functions library
include('functions.php');

if( isset($_GET['session_id']) ){

	$session_id = $_GET['session_id'];
	$typeExport = $_GET['export'];
	if ( $typeExport == 2 ) {
		
		$filename = 'export_'.$_GET['session_id'].".xml";
	
		$sqlSession = "SELECT * FROM sp_sessions WHERE session_id = ".$session_id;
		$resultSession = mysql_query($sqlSession);
		$rowSession = mysql_fetch_array($resultSession);
		
		$nbAmphi = $rowSession['session_places_internes_totales']+$rowSession['session_places_externes_totales'];
		$nbVisio = $rowSession['session_places_internes_totales_visio']+$rowSession['session_places_externes_totales_visio'];
		$nbInscritsAmphi = $rowSession['session_places_internes_prises']+$rowSession['session_places_externes_prises'];
		$nbInscritsVisio = $rowSession['session_places_internes_prises_visio']+$rowSession['session_places_externes_prises_visio'];
		
		$nbPresentsAmphi=0;
		$nbPresentsVisio=0;
		
		$sqlcount = mysql_query("SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$session_id." AND est_venu=1");	
		while($rowPresent = mysql_fetch_array($sqlcount)){
			if($rowPresent['inscrit_type_inscription']=="amphi interne" || $rowPresent['inscrit_type_inscription']=="amphi externe"){
				$nbPresentsAmphi++;
			}
			else{
				$nbPresentsVisio++;
			}
		}
		
		$resteAmphi = $nbAmphi-$nbPresentsAmphi;
		$resteVisio = $nbVisio-$nbPresentsVisio;
		
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<conference id="'. $session_id .'">'."\n";
		$xml .= '<informations>'."\n";
		$xml .= '<titre>'. $rowSession['session_nom'] .'</titre>'."\n";
		$xml .= '<salles>'."\n";
		$xml .= '<salle id="1" places="'.$nbAmphi.'" inscrits="'.$nbInscritsAmphi.'" noninscrits="0" presents="'.$nbPresentsAmphi.'" reste="'.$resteAmphi.'">Amphithéâtre</salle>'."\n";
		$xml .= '<salle id="2" places="'.$nbVisio.'" inscrits="'.$nbInscritsVisio.'" noninscrits="0" presents="'.$nbPresentsVisio.'" reste="'.$resteVisio.'">Visioconférence</salle>'."\n";
		$xml .= '</salles>'."\n";
		$xml .= '</informations>'."\n";
		$xml .= '<liste_inscrits>'."\n";
		
		$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$session_id;
		$result = mysql_query($sql_query);
		
		while ($row = mysql_fetch_array($result)) {
			
			if($row['inscrit_type_inscription']=="amphi interne" || $row['inscrit_type_inscription']=="amphi externe"){
				$salle=1;
			}
			else{
				$salle=2;
			}
			
			if($row['inscrit_type_inscription']=="amphi interne" || $row['inscrit_type_inscription']=="visio interne"){
				$statut="interne";
			}
			else{
				$statut="externe";
			}
			
			$date = date("Y-m-d",$row['inscrit_date']);
			
			$xml .= '<inscrit id="'. $row['inscrit_id'] .'" date_inscrit="'.$date.'" code="'. $row['inscrit_unique_id'] .'" registered="0" salle="'. $salle .'" statut="'. $statut .'" mail="'.$row['inscrit_mail'].'" entreprise="'.$row['inscrit_entreprise'].'" fonction="'.$row['inscrit_fonction'].'">'."\n";
			$xml .= '<nom>'. $row['inscrit_nom'] .'</nom>'."\n";
			$xml .= '<prenom>'. $row['inscrit_prenom'] .'</prenom>'."\n";
			$xml .= '</inscrit>'."\n";
		} 
		$xml .= '</liste_inscrits>'."\n";
		$xml .= '</conference>'."\n";
		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: text/xml");
		header("Content-Disposition: attachment; filename=$filename");
		echo $xml;
		exit;
	} 
	else {
		if ( $typeExport == 1 ) {
			$filename = 'export_'.$_GET['session_id'].".csv";
			$csv_terminated = "\n";
			$csv_separator = ";";
			$csv_enclosed = '';
			$csv_escaped = "\\";
			$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$session_id;
			$result = mysql_query($sql_query);

			$schema_insert = '';
			$schema_insert .= $csv_enclosed . "Nom : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Prénom : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Mail : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Entreprise : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Fonction : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Type d'inscription : " . $csv_enclosed;
			$schema_insert .= $csv_separator;
			$schema_insert .= $csv_enclosed . "Salle : " . $csv_enclosed;
			$schema_insert .= $csv_separator;

 
			$out = trim(substr($schema_insert, 0, -1));
			$out .= $csv_terminated;
 
			// Format the data
			while ($row = mysql_fetch_array($result)) {
				$type_salle=explode(" ", $row['inscrit_type_inscription']);
				
				$schema_insert = '';		
				$schema_insert .= $csv_enclosed . $row['inscrit_nom'] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $row['inscrit_prenom'] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $row['inscrit_mail'] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $row['inscrit_entreprise'] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $row['inscrit_fonction'] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $type_salle[1] . $csv_enclosed . $csv_separator;
				$schema_insert .= $csv_enclosed . $type_salle[0] . $csv_enclosed . $csv_separator;
				
				/*
		
				$tabInscription = split(',', $row['listTablesRondesID'] );
				$insertNameTab = "";
				for ( $x=0; $x<count($tabInscription); $x++ ) {
					if ( $tabInscription[$x] <> '' ) {
						$sql_query = "select * from tablesRondes WHERE id = ".$tabInscription[$x];
						$resultTab = mysql_query($sql_query);
						$rowTab = mysql_fetch_array($resultTab);
						$insertNameTab.= $rowTab['name']." (".$rowTab['hourStart']."-".$rowTab['hourEnd'].")"." | ";
					}
				}
				$insertNameTab = trim(substr($insertNameTab, 0, -3));
				$schema_insert .= $csv_enclosed . $insertNameTab . $csv_enclosed . $csv_separator;*/
				$schema_insert .= $csv_separator;
 
				$out .= $schema_insert;
				$out .= $csv_terminated;
			} // end while
 
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Length: " . strlen($out));
			// Output to browser with appropriate mime type, you choose ;)
			header("Content-type: text/x-csv");
			//header("Content-type: text/csv");
			//header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=$filename");
			echo $out;
			exit;
		} else {
		    	$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
			$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
			$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>CMS | inscriptions</title>
						<link href="css/layout.css" rel="stylesheet" type="text/css" />
						<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="ckeditor.js"></script>
					<script src="sample.js" type="text/javascript"></script>
					<link href="css/sample.css" rel="stylesheet" type="text/css"/>
					</head>';
		
			$sql_query = "SELECT * FROM sp_sessions WHERE session_id = ".$session_id;
			$result = mysql_query($sql_query);
			$row = mysql_fetch_array($result);

			echo '<body>';
			echo '<div class="entete" style="width:95%; margin:auto; background:#ffffff; height:100px;">';
			
			
			//total
			$nombreTotal = $row['session_places_internes_totales'] + $row['session_places_externes_totales'] + $row['session_places_internes_totales_visio'] + $row['session_places_externes_totales_visio'];
			$nombreTotalInscrits = $row['session_places_internes_prises'] + $row['session_places_externes_prises'] + $row['session_places_internes_prises_visio'] + $row['session_places_externes_prises_visio'];
			echo '<p style="float:none;">Nombre total de personnes inscrites : '.$nombreTotalInscrits." sur ".$nombreTotal." </p>";
			
			//total en salle de conf
			$nombreAmphi = $row['session_places_internes_totales'] + $row['session_places_externes_totales'];
			if($nombreAmphi>0){
				$nombreAmphiInscrits = $row['session_places_internes_prises'] + $row['session_places_externes_prises'];
				echo '<p style="float:none;">Nombre de places en salle de conférence: '.$nombreAmphiInscrits.' sur '.$nombreAmphi."</p>";
			}
			
			//total en retransmission
			$nombreRetransmission = $row['session_places_internes_totales_visio'] + $row['session_places_externes_totales_visio'];
			if($nombreRetransmission>0){
				$nombreRetransmissionInscrits = $row['session_places_internes_prises_visio'] + $row['session_places_externes_prises_visio'];
				echo '<p style="float:none;">Nombre de places en retransmission: '.$nombreRetransmissionInscrits.' sur '.$nombreRetransmission."</p>";
			}
			
			
			if ( $row['session_places_internes_totales'] > 0 ) {	
				echo '<p>Nombre de places Internes: '.$row['session_places_internes_prises'].' sur '.$row['session_places_internes_totales']."</p><br/>";
			}
			if ( $row['session_places_externes_totales'] > 0 ) {
				echo '<p>Nombre de places Externes: '.$row['session_places_externes_prises'].' sur '.$row['session_places_externes_totales']."</p><br/>";
			}		
			if ( $row['session_statut_visio'] == 1 && ($row['session_places_internes_totales_visio']>0 || $row['session_places_externes_totales_visio']>0 ) ) {
				if ( $row['session_places_internes_totales_visio'] > 0 ) {
					echo '<p>Nombre de places Interne en retransmission : '.$row['session_places_internes_prises_visio'].' sur '.$row['session_places_internes_totales_visio']."</p><br/>";
				}
				if ( $row['session_places_externes_totales_visio'] > 0 ) {
					echo '<p>Nombre de places Externe en retransmission : '.$row['session_places_externes_prises_visio'].' sur '.$row['session_places_externes_totales_visio']."</p><br/>";
				}
			}
			
			$type_salle=explode(" ", $row['inscrit_type_inscription']);
			
			echo '<p><strong>inscriptions en cours : '.$row['session_nom'].'</strong></p></div>';
					
			echo'		<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr><td bgcolor="#FFFFFF"><div class="content">';
			echo '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td>Nom :</td>
					<td>Prénom :</td>
					<td>Mail :</td>
					<td>Entreprise :</td>
					<td>Fonction :</td>
					<td>Type d\'inscription :</td>
					<td>Salle :</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			';
		
			// Format the data
			$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$session_id;
			$result = mysql_query($sql_query);

			while ($row = mysql_fetch_array($result)) {
				if( $color == "#F0F0F0") {
					$color = "#FFFFFF";
				} else {
					$color = "#F0F0F0";				
				}
				$schema_insert = '<tr bgcolor="'.$color.'">';		
				$schema_insert .= "<td valign=top>" . $row['inscrit_nom'] . "</td>";
				$schema_insert .= "<td valign=top>" . $row['inscrit_prenom'] . "</td>";
				$schema_insert .= "<td valign=top>" . $row['inscrit_mail'] . "</td>";
				$schema_insert .= "<td valign=top>" . $row['inscrit_entreprise'] . "</td>";
				$schema_insert .= "<td valign=top>" . $row['inscrit_fonction'] . "</td>";
				$schema_insert .= "<td valign=top>" . $type_salle[1] . "</td>";
				$schema_insert .= "<td valign=top>" . $type_salle[0] . "</td>";
			
				/*$tabInscription = split(',', $row['listTablesRondesID'] );
				$insertNameTab = "";
				for ( $x=0; $x<count($tabInscription); $x++ ) {
					if ( $tabInscription[$x] <> '' ) {
						$sql_query = "select * from tablesRondes WHERE id = ".$tabInscription[$x];
						$resultTab = mysql_query($sql_query);
						$rowTab = mysql_fetch_array($resultTab);
						$insertNameTab.= $rowTab['name']." (".$rowTab['hourStart']."-".$rowTab['hourEnd'].")"."<br>";
					}
				}
				$schema_insert .= "<td valign=top>" . $insertNameTab . "</td>";

				$insertNameTab = trim(substr($insertNameTab, 0, -3));*/
				echo $schema_insert;
			} // end while
		}
	}
}
?>

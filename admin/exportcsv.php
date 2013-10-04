<?php
include_once('../vars/config.php');
// connection to data base
include('connect.php');

// functions library
include('functions.php');
session_start();
if(isset($_POST['venu'])){
	$tableauVenus = "";
	$nbr = count($_POST['venu']);
	
	for($i=0;$i<$nbr;$i++){
		if($i<($nbr-1)){
			$tableauVenus.=$_POST['venu'][$i].",";
		}
		else{
			$tableauVenus.=$_POST['venu'][$i];	
		}
	}
	
	$sql="UPDATE sp_inscrits SET inscrit_est_venu=1 WHERE inscrit_id IN (".$tableauVenus.") AND inscrit_session_id=".$_GET['session_id'];
	mysql_query($sql) or die(mysql_error());
	
	$sql="UPDATE sp_inscrits SET inscrit_est_venu=0 WHERE inscrit_id NOT IN (".$tableauVenus.") AND inscrit_session_id=".$_GET['session_id'];
	mysql_query($sql) or die(mysql_error());
}
else{

}

if(isset($_POST['supprimer'])){
	$tableauSupprimer = "";
	$nbr = count($_POST['supprimer']);
	for($i=0;$i<$nbr;$i++){
		if($i<($nbr-1)){
			$tableauSupprimer.=$_POST['supprimer'][$i].",";
		}
		else{
			$tableauSupprimer.=$_POST['supprimer'][$i];	
		}
	}
	
	foreach($_POST['supprimer'] as $inscrit){
		$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_id = ".$inscrit;
		$result = mysql_query($sql_query);
		$row = mysql_fetch_array($result);
		
		if($row['inscrit_type_inscription'] == "amphi interne"){
			$sql="UPDATE sp_sessions SET session_places_internes_prises=session_places_internes_prises-1 WHERE session_id=".$_GET['session_id'];
			mysql_query($sql) or die(mysql_error());
		}
		
		if($row['inscrit_type_inscription'] == "amphi externe"){
			$sql="UPDATE sp_sessions SET session_places_externes_prises=session_places_externes_prises-1 WHERE session_id=".$_GET['session_id'];
			mysql_query($sql) or die(mysql_error());
		}
		
		if($row['inscrit_type_inscription'] == "visio interne"){
			$sql="UPDATE sp_sessions SET session_places_internes_prises_visio=session_places_internes_prises_visio-1 WHERE session_id=".$_GET['session_id'];
			mysql_query($sql) or die(mysql_error());
		}
		
		if($row['inscrit_type_inscription'] == "visio externe"){
			$sql="UPDATE sp_sessions SET session_places_externes_prises_visio=session_places_externes_prises_visio-1 WHERE session_id=".$_GET['session_id'];
			mysql_query($sql) or die(mysql_error());
		}
		
		
	}
	
	$sql="DELETE FROM sp_inscrits WHERE inscrit_id IN (".$tableauSupprimer.") AND inscrit_session_id=".$_GET['session_id'];
	mysql_query($sql) or die(mysql_error());
}



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
		
		$sqlcount = mysql_query("SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$_GET['conf']." AND est_venu=1");	
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
			
			$xml .= '<inscrit id="'. $row['inscrit_id'] .'" date_inscrit="'.$date.'" code="'. $row['inscrit_unique_id'] .'" registered="0" salle="'. $salle .'" statut="'. $statut .'" mail="'.$row['inscrit_mail'].'" entreprise="'.$row['inscrit_entreprise'].'" fonction="'.$row['inscrit_fonction'].'" horaire="'.$row['inscrit_date_scan'].'">'."\n";
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
			?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>Sciences Po | Événements : administration</title>
					<link href="css/layout.css" rel="stylesheet" type="text/css" />
					<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
					<script type="text/javascript" src="ckeditor.js"></script>
					<script src="sample.js" type="text/javascript"></script>
				</head>
			<?php	
				$sql_query = "SELECT * FROM sp_sessions WHERE session_id = ".$session_id;
				$result = mysql_query($sql_query);
				$row = mysql_fetch_array($result);
			?>
			<body>
				<div id="page_export">
					    <div id="header_export">
							<a href="list.php"><img src="img/logo_full.png" alt="Sciences-Po"/></a>
							<h1><a href="list.php">/ événements</a></h1>
							<a href="index.php?error=1" class="deconnecter">se déconnecter</a>
						</div>
				    <div id="menu_export">
						<?php include("menu.php"); ?>
				    </div>
				    <div id="content_export">
						<div class="entete">
							<?php  
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
								echo '<p style="float:none;">Nombre de places Internes: '.$row['session_places_internes_prises'].' sur '.$row['session_places_internes_totales']."</p>";
							}
							if ( $row['session_places_externes_totales'] > 0 ) {
								echo '<p style="float:none;">Nombre de places Externes: '.$row['session_places_externes_prises'].' sur '.$row['session_places_externes_totales']."</p>";
							}		
							
							if ( $row['session_places_internes_totales_visio'] > 0 ) {
								echo '<p style="float:none;">Nombre de places Interne en retransmission : '.$row['session_places_internes_prises_visio'].' sur '.$row['session_places_internes_totales_visio']."</p>";
							}
							if ( $row['session_places_externes_totales_visio'] > 0 ) {
								echo '<p style="float:none;">Nombre de places Externe en retransmission : '.$row['session_places_externes_prises_visio'].' sur '.$row['session_places_externes_totales_visio']."</p>";
							}
						    
							
						    $sqlcount = mysql_query("SELECT COUNT(*) AS nb FROM sp_inscrits WHERE inscrit_session_id='".$session_id."' AND inscrit_casque=1");
							$rescount = mysql_fetch_array($sqlcount);
			
			                echo '<p style="float:none;">Nombre de casques à prévoir : '.$rescount['nb'].'</p>';
			
							echo '<p style="float:none;"><strong>inscriptions en cours : '.$row['session_nom'].'</strong></p>';
							?>
						</div>
					
						<div class="tableau">
							<form name="liste" action="#" method="post">
								<input type="submit" value="Valider"/>
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<th>Date d'inscription</th>
										<th>Nom</th>
										<th>Prénom</th>
										<th>Mail</th>
										<th>Entreprise</th>
										<th>Fonction</th>
										<th>Type d'inscription</th>
										<th>Salle</th>
										<th>Est venu</th>
										<th>Supprimer</th>
									</tr>
								
								<?php		
								// Format the data
								$sql_query = "SELECT * FROM sp_inscrits WHERE inscrit_session_id = ".$session_id;
								$result = mysql_query($sql_query);
							
								while ($row = mysql_fetch_array($result)) {
									if( $color == "#F0F0F0") {
										$color = "#FFFFFF";
									} else {
										$color = "#F0F0F0";				
									}
									$type_salle=explode(" ", $row['inscrit_type_inscription']);
								
								?>
									<tr bgcolor="<?php echo $color;?>">	
										<td><?php echo date("d/m/Y H:i:s",$row['inscrit_date']);?></td>
										<td><?php echo $row['inscrit_nom'];?></td>
										<td><?php echo $row['inscrit_prenom'];?></td>
										<td><?php echo $row['inscrit_mail'];?></td>
										<td><?php echo $row['inscrit_entreprise'];?></td>
										<td><?php echo $row['inscrit_fonction'];?></td>
										<td><?php echo $type_salle[1];?></td>
										<td><?php echo $type_salle[0];?></td>
										<td align="center"><input type="checkbox" name="venu[]" value="<?php echo $row['inscrit_id'];?>" <?php if($row['inscrit_est_venu']==1){echo "checked=\"checked\"";}?>/></td>
										<td align="center"><input type="checkbox" name="supprimer[]" value="<?php echo $row['inscrit_id'];?>"/></td>
									</tr>

								<?php
								} // end while
							?>
								</table>
							</form>
						</div>
					</div>
				</div>
			</body>	
			</html>
		<?php
		}
	}
}
?>

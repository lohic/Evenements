<?php
include_once('../vars/config.php');
// connection to data base
include('connect.php');

$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$xml .= '<liste_evenements>'."\n";

$sql_query = "SELECT * FROM sp_evenements";
$result = mysql_query($sql_query);

while ($row = mysql_fetch_array($result)){
    
	$sqlRubrique = "SELECT * FROM sp_rubriques WHERE rubrique_id=".$row['evenement_rubrique'];
	$resRubrique = mysql_query($sqlRubrique)or die(mysql_error());
	$rowRubrique= mysql_fetch_array($resRubrique);
	
	$sqlsessions ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
	$ressessions = mysql_query($sqlsessions) or die(mysql_error());
	$finEvenement=0;
	while($rowsession = mysql_fetch_array($ressessions)){
		if($rowsession['session_fin']>$finEvenement){
			$finEvenement = $rowsession['session_fin'];
		}
	}

	$xml .= '<evenement id="'. $row['evenement_id'] .'">'."\n";
	$xml .= '<informations>'."\n";
	
	$xml .= '<titre>'. $row['evenement_titre'] .'</titre>'."\n";
	$xml .= '<categorie>'. $rowRubrique['rubrique_titre'] .'</categorie>'."\n";
	$xml .= '<date_debut>'.date("d/m/Y H:i:s", $row['evenement_date']).'</date_debut>'."\n";
	$xml .= '<date_fin>'.date("d/m/Y H:i:s", $finEvenement).'</date_fin>'."\n";
	
	$sqlcountsessions = mysql_query("SELECT COUNT(*) AS nb FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'");
	$rescountsessions = mysql_fetch_array($sqlcountsessions);
	
	if($rescountsessions['nb']>1){
		$sqlsessionsBis ="SELECT * FROM sp_sessions WHERE evenement_id='".$row['evenement_id']."'";
		$ressessionsBis = mysql_query($sqlsessionsBis) or die(mysql_error());
		$i=1;
		while($rowsessionBis = mysql_fetch_array($ressessionsBis)){
			$xml .= '<session_'.$i.'>'."\n";
            
			$xml .= '<titre_session>'. $rowsessionBis['session_nom'] .'</titre_session>'."\n";
            $xml .= '<date_debut_session>'.date("d/m/Y H:i:s", $rowsessionBis['session_debut']).'</date_debut_session>'."\n";
            $xml .= '<date_fin_session>'.date("d/m/Y H:i:s", $rowsessionBis['session_fin']).'</date_fin_session>'."\n";

			$xml .= '</session_'.$i.'>'."\n";
			$i++;
		}
			
			
	}
	
	$xml .= '</informations>'."\n";
	$xml .= '</evenement>'."\n";
 
}
$xml .= '</liste_evenements>'."\n";

echo $xml;	
?>
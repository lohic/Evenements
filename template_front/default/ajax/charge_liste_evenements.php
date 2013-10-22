<?php
header('Content-type: text/html; charset=UTF-8');

include_once('vars/config.php');
include_once(REAL_LOCAL_PATH.'vars/statics_vars.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_session.php');
include_once(REAL_LOCAL_PATH.'classe/classe_rubrique.php');
include_once(REAL_LOCAL_PATH.'classe/classe_organisme.php');
include_once(REAL_LOCAL_PATH.'classe/classe_keyword.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

$organisme = new organisme();
$event = new evenement();
$rubrique = new rubrique();
$session = new session();
$keyword = new keyword();

$recherche = strtr($_GET['recherche'],'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%');
	
$tableauDesEvents=array(0);
$tableauAlternatif=array();
$aujourdhui = mktime(0,0,0,date("n"),date("d"),date("Y"));

$evenements_organisme=array();
$evenements_organisme = $event->get_events_organism();
$evenements_partages = $event->get_events_partages();
if(count($evenements_partages)>0){
    $evenements_organisme = array_merge($evenements_organisme, $evenements_partages);
}

if($_GET['langue']=="en"){
    $lang="en";
    $complet = "FULL";
    $sinscrire = "SIGN UP";
    $aucun = "No event to come.";
}
else{
    $lang="fr";
    $complet = "COMPLET";
    $sinscrire = "S'INSCRIRE";
    $aucun = "Il n'y a aucun événement à venir.";
}

?>
<div id="liste_evenements" class="masonry">
<?php
if(count($evenements_organisme)>0){
	if($_GET['langue']=="en"){
		$sql = "SELECT * FROM ".TB."evenements, ".TB."rubriques WHERE evenement_rubrique=rubrique_id AND evenement_id IN (".implode(',',$evenements_organisme).") AND (evenement_texte_en LIKE '%".$recherche."%' OR evenement_titre_en LIKE '%".$recherche."%') ORDER BY evenement_datetime";
	}
	else{
		$sql = "SELECT * FROM ".TB."evenements, ".TB."rubriques WHERE evenement_rubrique=rubrique_id AND evenement_id IN (".implode(',',$evenements_organisme).") AND (evenement_texte LIKE '%".$recherche."%' OR evenement_titre LIKE '%".$recherche."%') ORDER BY evenement_datetime";
	}

	$res = mysql_query($sql)or die(mysql_error());
	$resBis = mysql_query($sql)or die(mysql_error());
	if(mysql_fetch_array($res)==false){
		$sql = "SELECT * FROM ".TB."evenements, ".TB."rubriques WHERE evenement_rubrique=rubrique_id AND evenement_id IN (".implode(',',$evenements_organisme).") ORDER BY evenement_datetime";
		$resBis = mysql_query($sql)or die(mysql_error());
?>
		<div id="pasderesultat"><p>Il n'y a aucun résultat pour cette recherche.</p></div>
<?php

	}
	$multiplicateur = 1;
	while($row = mysql_fetch_array($resBis)){
		include(REAL_LOCAL_PATH.'integration/event.php');
	}
}
else{
?>

	<div id="pasderesultat"><p><?php echo $aucun;?></p></div>
<?php
}
?>
</div>
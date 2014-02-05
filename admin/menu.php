<?php
include_once('../vars/config.php');
//include_once('../vars/constantes_vars.php');
//include_once('../vars/statics_vars.php');

include_once(REAL_LOCAL_PATH.'classe/classe_core_event.php');
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

//$core = new core(); 

$idGroups= array();
foreach($core->user_info->groups as $cle => $valeur) 
{
	$idGroups[]=$cle;
}
$idGroups = implode(',',$idGroups);     

$sqlcountsoumission = mysql_query("SELECT COUNT(*) AS nb FROM sp_evenements WHERE evenement_statut=4 AND evenement_groupe_id IN ($idGroups)");	
$rescountsoumission = mysql_fetch_array($sqlcountsoumission);
?>
<?php 
if($core->isAdmin){ 
?>
	<ul>
		<li class="menu_principal" id="principal_1">
			<a href="list.php?menu_actif=evenements" class="" id="evenements">événements</a>
			<ul id="secondaire_1" style="display:none;">
				<li><a href="new_evenement.php?menu_actif=evenements" class="" id="nouvelevenement">Nouvel événement</a></li>
			</ul>
		</li>
		 
		<?php 
			if($core->userLevel<=3){
		?>
				<li class="menu_principal" id="principal_2"><a href="evenements_partages.php?menu_actif=evenementspartage" class="" id="evenementspartage">Evéne. partagés</a></li>
	    		<li class="menu_principal" id="principal_3"><a href="soumissions.php?menu_actif=soumissions" class="" id="soumissions">Soumissions : <?php echo $rescountsoumission['nb'];?></a></li>  
		<?php
			}
		?>
	
	   	<li class="menu_principal" id="principal_4"><a href="rubriques.php?menu_actif=rubriques" class="" id="rubriques">Paramètres</a></li>
		
		<?php  
			if($core->userLevel<=1){
		?>
		<li class="menu_principal" id="principal_5">
			<a href="logins.php?menu_actif=logins" class="" id="logins">Comptes</a>
				<ul id="secondaire_5" style="display:none;">
					<li><a href="groupes.php?menu_actif=logins" class="" id="groupes">Groupes</a></li>
					<li><a href="organismes.php?menu_actif=logins" class="" id="organismes">Organismes</a></li>
				</ul>  
		</li>
		<?php
			}
		?>
		
		<?php 
			if($core->userLevel<=3){
		?>
	    		<li class="menu_principal" id="principal_6"><a href="export_events.php?menu_actif=export" class="" id="export">Export</a></li>  
		<?php
			}
		?> 
	</ul> 
<?php
}
?>

<script type="text/javascript">
	$(window).load(function(){
		$("li.menu_principal").mouseover(function(e){
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "secondaire_"+tableau_id[1];
			//document.getElementById(identifiant).style.display="block";
			$('#'+identifiant).show();
	   	});
			
		$("li.menu_principal").mouseout(function(e){
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "secondaire_"+tableau_id[1];
			//document.getElementById(identifiant).style.display="none";
			$('#'+identifiant).hide();
	   	});
	});
</script>

<?php
// connection to data base
//include('../../../../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_session.php');
include_once(REAL_LOCAL_PATH.'classe/classe_rubrique.php');
include_once(REAL_LOCAL_PATH.'classe/classe_organisme.php');
include_once(REAL_LOCAL_PATH.'classe/classe_keyword.php');

$organisme = new organisme();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Sciences Po | événements</title>

<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="rss_events.php" />
<link href="css/facebook.css" rel="stylesheet" type="text/css" media="screen" />

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/masonry.js"></script>
<script type="text/javascript" src="js/jquery.maximage.js"></script>
<script type="text/javascript" src="js/jquery.adress.js"></script>
<meta name="description" content="Le guide des événements de Sciences Po répertorie mois après mois l’éventail des 
manifestations organisées à Sciences Po." />

<script type="text/javascript" src="js/scripts.js"></script>

<!--[if lte IE 7]>
	<link rel="stylesheet" type="text/css" href="css/ie7.css" />
<![endif]-->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21940997-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>
	
<div class="wrap" id="wrap">
	<?php
		$sqlOrganisme = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
		$resOrganisme = mysql_query($sqlOrganisme)or die(mysql_error());
		$rowOrganisme = mysql_fetch_array($resOrganisme);
	?>

	<div class="banniere_events">
		 <a href="<?php echo CHEMIN_FRONT_OFFICE; ?>" target="_blank">
		 	<img src="<?php echo CHEMIN_UPLOAD_GENERAL;?>banniere_facebook/<?php echo $rowOrganisme['organisme_id'];?>/<?php echo $rowOrganisme['organisme_banniere_facebook_chemin'];?>" alt="sciencesPo" width="500"/>
		 </a>
	</div>
	<?php  
		$debutmois = mktime(0,0,0,date("n"),1,date("Y"));

		$sql = sprintf("SELECT * FROM ".TB."evenements AS spe, ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spe.evenement_statut=3 AND spe.evenement_rubrique=spr.rubrique_id AND evenement_facebook=1 AND spg.groupe_organisme_id=%s  AND evenement_date >=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY spe.evenement_date", 
								func::GetSQLValueString($rowOrganisme['organisme_id'], "int"),
								func::GetSQLValueString($debutmois, "text"));
		$res = mysql_query($sql)or die(mysql_error());
		$compteur=0;
		$ouvert=false;
		$ferme=false;
		
		while($row = mysql_fetch_array($res)){ 
			$sqlsessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($row['evenement_id'], "int"));
			$ressessions = mysql_query($sqlsessions) or die(mysql_error());
			$finEvenement=0;
			while($rowsession = mysql_fetch_array($ressessions)){
				if($rowsession['session_fin']>$finEvenement){
					$finEvenement = $rowsession['session_fin'];
				}
			}
			$sqlSession = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($row['evenement_id'], "int"));
			$resSession = mysql_query($sqlSession) or die(mysql_error());
			$rowSession = mysql_fetch_array($resSession);
			
			if($finEvenement>time()){
				$moisDebut = date("F", $row['evenement_date']);
				if($compteur<=3){
		?>			
					<div class="box col1 rubrique_<?php echo $row['rubrique_id']; ?> <?php echo $moisDebut ?> <?php if($compteur===0 || $compteur===2){echo "first_child";}?>" id="box_<?php echo $row['evenement_id'];?>">
					<?php
						if($row['evenement_image']!=""){
							$lacouleur = explode("#",$row['rubrique_couleur']);

					?>
							<a href="<?php echo CHEMIN_FRONT_OFFICE; ?>?lang=fr&amp;id=<?php echo $row['evenement_id']; ?>" class="lien_event" id="image_lien_<?php echo $row['evenement_id']; ?>" style="float:left;" target="_blank">
							<img src="<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id']; ?>/moyen-<?php echo $row['evenement_image']; ?>?cache=<?php echo time(); ?>" alt="<?php echo $row['evenement_texte_image']; ?>" width="240" height="136"/>
							</a>
							<img src="<?php echo CHEMIN_TRIANGLES; ?>triangle_<?php echo $lacouleur[1]; ?>.png" alt="triangle" class="triangle"/>

					<?php
						}
					?>
			          	<p style="background-color:<?php echo $row['rubrique_couleur']; ?>" class="titre">
							<a href="<?php echo CHEMIN_FRONT_OFFICE; ?>?lang=fr&amp;id=<?php echo $row['evenement_id']; ?>" class="lien_event" id="titre_lien_<?php echo $row['evenement_id']; ?>" target="_blank"><?php echo $row['evenement_titre']; ?></a>
						</p>

					<?php

						$jourDebut = date("d", $row['evenement_date']);
						$jourFin = date("d", $finEvenement);
						if($jourDebut==$jourFin){
							if(date("H:i", $finEvenement)!="23:59"){
								$horaires = date("d/m", $row['evenement_date'])." | ".date("H", $row['evenement_date'])."h".date("i", $row['evenement_date'])."-".date("H", $finEvenement)."h".date("i", $finEvenement);	
							}
							else{
								$horaires = date("d/m", $row['evenement_date'])." | ".date("H", $row['evenement_date'])."h".date("i", $row['evenement_date']);
							}	
						}
						else{
							if(date("H:i", $finEvenement)!="23:59"){
								$horaires = "du ".date("d/m", $row['evenement_date'])." | ".date("H", $row['evenement_date'])."h".date("i", $row['evenement_date'])." au ".date("d/m", $finEvenement)." | ".date("H", $finEvenement)."h".date("i", $finEvenement);
							}
							else{
								$horaires = "du ".date("d/m", $row['evenement_date'])." | ".date("H", $row['evenement_date'])."h".date("i", $row['evenement_date'])." au ".date("d/m", $finEvenement);
							}
						}

					?>
						<p class="date"><?php echo $horaires; ?></p>
						<p>
						<?php 
							$resumeGeneral = strip_tags($row['evenement_texte']);
							$resumeGeneral = explode(" ",$resumeGeneral);
							$resume = strip_tags($row['evenement_texte'],'<br>'); 
							$resume = explode(" ",$resume);

							$resumeFacebook = "";

							$borne = 15;

							if($row['evenement_image']==""){
								$borne=45;
							}

							for($i = 0 ; $i < $borne ; $i++){
								if($i != ($borne-1)){
									echo $resume[$i]." ";
									str_replace('"','', $resumeGeneral[$i]);
									$resumeFacebook .= $resumeGeneral[$i]." ";
								}
								else{
									echo $resume[$i]."... &nbsp;";
									str_replace('"','', $resumeGeneral[$i]);
									$resumeFacebook .= $resumeGeneral[$i]."... &nbsp;";
								}
							}


						?>

						<a href="<?php echo CHEMIN_FRONT_OFFICE; ?>?lang=fr&amp;id=<?php echo $row['evenement_id']; ?>" class="suite" style="background-color:<?php echo $row['rubrique_couleur']; ?>" id="lien_suite_<?php echo $row['evenement_id']; ?>" target="_blank"><img src="<?php echo CHEMIN_ICONES; ?>lien_suite.png" alt=""/></a>

						</p>

						<?php
						if($row['evenement_organisateur']!=""){
						?>
							<p class="organisateur">
								<img src="<?php echo CHEMIN_ICONES; ?>buddy_icn.png" alt="organisateur" style="background-color:<?php echo $row['rubrique_couleur']; ?>"/><?php echo $row['evenement_organisateur']; ?>
							</p>
						<?php
						}
						?>

						<p>
						<?php
							if($rowSession['session_type_inscription']==2 && ($rowSession['session_statut_inscription']==1||$rowSession['session_statut_visio']==1)){
								$sqlcountsession = sprintf("SELECT COUNT(*) AS nb FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($row['evenement_id'], "int"));
								$sqlcountsessions = mysql_query($sqlcountsession) or die(mysql_error());
								$rescountsessions = mysql_fetch_array($sqlcountsessions);
								if($rescountsessions['nb']==1){
									$totalPlaces = $rowSession['session_places_internes_totales'] + $rowSession['session_places_internes_totales_visio'];
									$totalPrises = $rowSession['session_places_internes_prises'] + $rowSession['session_places_internes_prises_visio'];
									if($totalPlaces > $totalPrises){
						?>
										<a href="<?php echo CHEMIN_INSCRIPTION; ?>inscription.php?id=<?php echo $rowSession['session_id']; ?>" style="background-color:<?php echo $row['rubrique_couleur']; ?>" class="sinscrire" target="_blank">S'INSCRIRE</a>
						<?php
									}
									else{
										if($totalPlaces!=0){
						?>				
											<span class="complet">COMPLET</span>
						<?php
										}
									}
								}
								else{
									$sqlSessions = sprintf("SELECT * FROM ".TB."sessions WHERE evenement_id=%s", func::GetSQLValueString($row['evenement_id'], "int"));
									$resSessions = mysql_query($sqlSessions) or die(mysql_error());
									$complet = true;
									$totalPlacesGeneral = 0;
									while($rowSessions = mysql_fetch_array($resSessions)){
										$totalPlaces = $rowSessions['session_places_internes_totales'] + $rowSessions['session_places_internes_totales_visio'];
										$totalPrises = $rowSessions['session_places_internes_prises'] + $rowSessions['session_places_internes_prises_visio'];
										$totalPlacesGeneral = $totalPlacesGeneral + $totalPlaces;
										if($totalPlaces > $totalPrises){
											$complet=false;
										}
									}
									if($complet==false){
						?>
										<a href="<?php echo CHEMIN_INSCRIPTION; ?>inscription_multiple.php?id=<?php echo $row['evenement_id'];?>" style="background-color:<?php echo $row['rubrique_couleur']; ?>" class="sinscrire" target="_blank">S'INSCRIRE</a>
						<?php				
									}
									else{
										if($totalPlacesGeneral!=0){
						?>
											<span class="complet">COMPLET</span>
						<?php		
										}
									}
						?>

						<?php
								}
							}
						?>

							<a href="http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=<?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;picture=<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id']; ?>/mini-<?php echo $row['evenement_image'];?>&amp;name=<?php echo str_replace('"','', $row['evenement_titre']); ?>&amp;caption=<?php echo $horaires; ?>&amp;description=<?php echo $resumeFacebook; ?>&amp;message=Sciences Po | événements&amp;redirect_uri=<?php echo CHEMIN_FRONT_OFFICE; ?>" class="reseaux" style="background-color:<?php echo $row['rubrique_couleur']; ?>"  target="_blank"><img src="<?php echo CHEMIN_ICONES; ?>facebook_icn.png" alt="facebook"/></a>

							<a href="http://twitter.com/home?status=Je participe à cet événement Sciences Po :  <?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" class="reseaux" style="background-color:<?php echo $row['rubrique_couleur']; ?>"><img src="<?php echo CHEMIN_ICONES; ?>twitter_icn.png" alt="twitter"/></a>

							<a href="makeIcal.php?id=<?php echo $row['evenement_id']; ?>" target="_blank" class="reseaux" style="background-color:<?php echo $row['rubrique_couleur']; ?>"><img src="<?php echo CHEMIN_ICONES; ?>calendar_icn.png" alt="ical"/></a>

						</p>

			        </div>
		<?php	  
				}
				else{
					if($compteur<=11){
						if(!$ouvert){ 
							$ouvert=true;
		?>                  
		                	<div id="autres_liens"> 
								<h5>Et aussi...</h5>
								<ul class="autres_events">
		<?php					
						}
		?>
						<li style="color:<?php echo $row['rubrique_couleur']; ?>"><a href="<?php echo CHEMIN_FRONT_OFFICE; ?>?lang=fr&amp;id=<?php echo $row['evenement_id']; ?>" class="lien_event" id="titre_lien_<?php echo $row['evenement_id']; ?>" target="_blank"><?php echo date("d/m",$row['evenement_date'])." | ".$row['evenement_titre']; ?></a></li>
		<?php				
						if($compteur==11){  
							$ferme=true;
		?>                  
								</ul>
		     				</div> 
		<?php					
						}
					}
				}
				$compteur++;
			}
		}
		if(!$ferme && $ouvert){
	?>
			</ul>
		</div>
	<?php
		}
	?> 
	<div class="banniere_events">
		<p>Retrouver tous les événements sur le site <a href="http://www.sciencespo.fr/evenements" title="Sciences Po Evénements"  target="_blank">www.sciencespo.fr/evenements</a></p>
		<a href="<?php echo CHEMIN_FRONT_OFFICE; ?>" target="_blank">
			<!--<img src="images/banniere_events.png" alt="sciencesPo" width="500"/>-->
			<img src="<?php echo CHEMIN_UPLOAD_GENERAL;?>footer_facebook/<?php echo $rowOrganisme['organisme_id'];?>/<?php echo $rowOrganisme['organisme_footer_facebook_chemin'];?>" alt="sciencesPo" width="500"/>
		</a>
	</div>   
</div> <!-- .wrap --> 

<div id="fb-root"></div>
 <script src="//connect.facebook.net/en_US/all.js"></script>
 <script>
   FB.init({
     appId  : '197353513668674',
     status : true, // check login status
     cookie : true, // enable cookies to allow the server to access the session
     xfbml  : true, // parse XFBML
     channelUrl  : 'http://www.sciencespo.fr/evenements/facebook.php' // Custom channel URL
   });

FB.Canvas.setSize({width:680});

window.fbAsyncInit = function() {
  FB.Canvas.setAutoGrow();
}

 </script>
</body>
</html> 

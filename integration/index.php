<?php
	include_once('../classe/classe_evenement.php');
	include_once('../classe/classe_rubrique.php');
	include_once('../classe/classe_organisme.php');

	$organisme = new organisme();
	$event = new evenement();
	$rubrique = new rubrique();

	if($_GET['lang']=="en"){
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

<html>
	<head>
		<title>Science po événement</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
	    <link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.1.4" media="screen" />

		<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
		<script type="text/javascript" src="js/jquery.isotope.min.js"></script>
		<script type="text/javascript" src="js/ICanHaz.min.js"></script>
		<script type="text/javascript" src="js/jquery.resizend.js"></script>
		<script type="text/javascript" src="js/jquery.jpanelmenu.min.js"></script>
		<script type="text/javascript" src="js/jRespond.min.js"></script>
		<script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>
	    <script type="text/javascript" src="source/jquery.fancybox.js?v=2.1.4"></script>

	    <script type="text/javascript" src="js/general.js"></script>
	</head>
	<body>
		<?php
			$rowOrganisme = $organisme->get_organisme();
			$rubriques_organisme = $rubrique->get_rubriques_organism($rowOrganisme['organisme_id']);
			$rubriques_partages = $rubrique->get_rubriques_partages($rowOrganisme['organisme_id']);
			$rubriques_organisme = array_merge($rubriques_organisme, $rubriques_partages);

			$evenements_organisme=array();
			$evenements_organisme = $event->get_events_organism();
			$evenements_partages = $event->get_events_partages();
			$evenements_organisme = array_merge($evenements_organisme, $evenements_partages);

			$nomMoisAnglais = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
			$nomMoisFrancais = array(1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre');
					
			$tableauMois = array();
			$tableauMois = $event->get_events_months($evenements_organisme);
		?>
		<section id="menu_smartphone" class="grand-hidden">
			<input type="text" name="mot_recherche" id="mot_recherche" value="Rechercher"/>
			<div id="options_smart" class="little_bigger">
			<?php
				if(count($rubriques_organisme)>0){
					$sql = "SELECT * FROM ".TB."rubriques WHERE rubrique_id IN (".implode(',',$rubriques_organisme).") ORDER BY rubrique_titre";
					$res = mysql_query($sql)or die(mysql_error());
				}
				else{
					$res=-1;
				}
				if($res!=-1){
			?>
					<div id="filtre_categorie_smart" class="mb1 option-set" data-group="rubrique">
						<span>Catégories</span>
						<div class="conteneur_filtre">
						<input type="checkbox" value=""        id="rubrique_toutes" class="all checkbox_smart" checked />
						<label for="rubrique_toutes" class="all"><span class="" style="background:#fff;"></span>toutes</label>
						<?php		
								while($row = mysql_fetch_array($res)){
						?>
									<input type="checkbox" value=".rubrique_<?php echo $row['rubrique_id'];?>" id="rubrique_<?php echo $row['rubrique_id'];?>" class="checkbox_smart" />
									<label for="rubrique_<?php echo $row['rubrique_id'];?>"><span class="vide" style="background:<?php echo $row['rubrique_couleur'];?>; border-color:<?php echo $row['rubrique_couleur'];?>;"></span><?php echo utf8_encode($row['rubrique_titre']);?></label>
						<?php
								}
						?>
						</div>			
					</div>
			<?php
				}
			?>
				<!--<div id="filtre_mot_smart" class="mb1 option-set" data-group="mots">
					<span>Mots-clés</span>
					<input type="checkbox" value=""        id="mots-tous" class="all" checked /><label for "mots-tous">tous</label>
				</div>-->
			<?php
				if(count($tableauMois)>0){
			?>
					<div id="filtre_date_smart" class="option-set" data-group="dates">
						<span>Dates</span>
						<div class="conteneur_filtre">
						<input type="checkbox" value=""        id="dates_toutes" class="all checkbox_smart" checked />
						<label for="dates_toutes" class="all"><span class="" style="background:#fff;"></span>toutes</label>
						
			<?php
						foreach($tableauMois as $mois){
							if($lang=="fr"){
			?>
								<input type="checkbox" value=".mois_<?php echo $mois['unique'];?>" id="mois_<?php echo $mois['unique'];?>" class="checkbox_smart"/>
								<label for="mois_<?php echo $mois['unique'];?>"><span class="vide" style="background:#fff;"></span><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></label>
			<?php
							}
							else{
			?>
								<input type="checkbox" value=".mois_<?php echo $mois['unique'];?>" id="mois_<?php echo $mois['unique'];?>" class="checkbox_smart"/>
								<label for="mois_<?php echo $mois['unique'];?>"><span class="vide" style="background:#fff;"></span><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></label>
			<?php
							}
						}
			?>		
						</div>
					</div>
			<?php		
				}
			?>
				</div>

				<button id="validation_smart" class="very_small">Filtrer</button>
				<a href="soumettre.php" class="soumettre"><span class="icone"></span><span class="texte_icone">Proposer un événement</span></a>
		</section>
		<section id="contenu_principal">
			<header>
				
				<div>
					<section id="pre_header">
						<a href="#" id="lien_menu_smartphone" class="grand-hidden"></a>
						<div class="small-hidden">
							<a href="soumettre.php" class="soumettre"><span class="icone"></span><span class="texte_icone">Proposer un événement</span></a>
						</div>
						<div>
						<?php
							if($lang=="fr"){
						?>
								<span class="francais"></span>
								<a href="index.php?lang=en" title="anglais" class="anglais"><span class="anglais"></span></a>
						<?php
							}
							else{
						?>
								<a href="index.php?lang=fr" title="français" class="francais"><span class="francais"></span></a>
								<span class="anglais"></span>
						<?php
							}
						?>
							<input type="text" name="mot_recherche" id="mot_recherche" value="Rechercher" class="small-hidden"/>
							<input type="submit" value="OK" class="valider_recherche small-hidden"/>
							<a href="rss_events.php?lang=fr" class="rss"></a>
						</div>
					</section>
					<section id="header">
						<h1><span class="invisible">Science po événements</span><a href="index.php"><img src="img/logo_spo.png" alt="sciences Po événements"/></a></h1>
					<?php
						if(count($rubriques_organisme)>0){
							$sql = "SELECT * FROM ".TB."rubriques WHERE rubrique_id IN (".implode(',',$rubriques_organisme).") ORDER BY rubrique_titre";
							$res = mysql_query($sql)or die(mysql_error());
						}
						else{
							$res=-1;
						}
						if($res!=-1){
					?>
							<div id="filtre_categorie" class="small-hidden">
								<ul>
									<li class="titre_filtre bit_small">
										<span>Catégories</span>
										<ul id="filtering-nav-categorie" class="filtre_isotope option-set" data-filter-group="rubrique">
									<?php		
											while($row = mysql_fetch_array($res)){
									?>
												<li class="" id="entre_cate_<?php echo $row['rubrique_id'];?>">
													<a class="carre" href="#" style="background:<?php echo $row['rubrique_couleur']; ?>;" data-filter-value=".rubrique_<?php echo $row['rubrique_id'];?>"></a>
													<a class="" href="#" data-filter-value=".rubrique_<?php echo $row['rubrique_id'];?>"><?php echo utf8_encode($row['rubrique_titre']);?></a>
												</li>
									<?php
											}
									?>
										</ul>
									</li>
								</ul>					
							</div>
					<?php
						}
					?>

					<?php
						if(count($tableauMois)>0){
					?>
							<div id="filtre_date" class="small-hidden">
								<ul>
									<li class="titre_filtre bit_small">
										<span>Dates</span>
										<ul id="filtering-nav-date" class="filtre_isotope option-set" data-filter-group="month">
					<?php
											foreach($tableauMois as $mois){
												if($lang=="fr"){
					?>
													<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="" href="#" data-filter-value=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
					<?php
												}
												else{
					?>
													<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="" href="#" data-filter-value=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
					<?php
												}
											}
					?>
										</ul>
									</li>
								</ul>	
							</div>
					<?php		
						}
					?>
						
						<div id="filtre_mot" class="small-hidden filtre_isotope">
							<ul>
								<li class="titre_filtre bit_small">
									<span>Mots-clés</span>
									<ul id="filtering-nav-mot">
										<li class="" id="entree_mot_2"><a class="mot_2" href="#mot_2" id="entree_2">Sciences Po Paris</a></li>
										<li class="" id="entree_mot_5"><a class="mot_5" href="#mot_5" id="entree_5">Campus du Havre</a></li>
										<li class="" id="entree_mot_3"><a class="mot_3" href="#mot_3" id="entree_3">Autre mot-clé</a></li>
									</ul>
								</li>
							</ul>	
						</div>
					</section>
				</div>
			</header>
			<div id="image" class="maxDivImg conteneur_banniere">
					<img src="http://www.sciencespo.fr/evenements/admin/upload/banniere/banniere.jpg?cache=1375289621" alt="bannière" class="banniere" width="1280" height="128"/>
			</div>
			<div id="liste_evenements" class="masonry">
				<!-- attention data-sort doit être un multiple de 10-->
				<?php
					if(count($evenements_organisme)>0){
						$sql = "SELECT * FROM ".TB."evenements AS spe, ".TB."rubriques AS spr WHERE spe.evenement_rubrique=spr.rubrique_id AND evenement_id IN (".implode(',',$evenements_organisme).") ORDER BY spe.evenement_datetime";
						$res = mysql_query($sql)or die(mysql_error());
					}
					else{
						$res=-1;
					}
					if($res!=-1){
						$multiplicateur = 1;
						while($row = mysql_fetch_array($res)){
							if (in_array($row['evenement_id'], $evenements_partages)) {
	                            $rowRubrique = $rubrique->get_rubrique_event_partage($row['evenement_id']);
	                   			$rubrique_id = $rowRubrique['rubrique_id'];
								$rubrique_couleur = $rowRubrique['rubrique_couleur'];
								$lacouleur = explode("#",$rowRubrique['rubrique_couleur']);
							}
							else{
								$rubrique_id = $row['rubrique_id'];
								$rubrique_couleur = $row['rubrique_couleur'];
								$lacouleur = explode("#",$row['rubrique_couleur']);
							}
							$multiple = 10*$multiplicateur;
							$multiplicateur++;

							$moisId = $event->get_event_unique_month($row['evenement_id']);
							$finEvenement = $event->get_fin_event($row['evenement_id']);
						
							$horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);
				?>
							<div class="event rubrique_<?php echo $rubrique_id;?> mois_<?php echo $moisId;?>" data-sort="<?php echo $multiple;?>">
								<?php
									if($row['evenement_image']!=""){
								?>
										<a href="/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" rel="address:/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" class="lien_event" id="image_lien_<?php echo $row['evenement_id'];?>" style="float:left;">
											<img src="<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id'];?>/moyen-<?php echo $row['evenement_image'];?>?cache=<?php echo time(); ?>" alt="<?php echo $row['evenement_texte_image'];?>" width="320" height="180"/>
										</a>
										<img src="<?php echo CHEMIN_TRIANGLES; ?>triangle_<?php echo $lacouleur[1]; ?>.png" alt="triangle" class="triangle"/>
								<?php
									}
								?>
								<h1 style="background-color:<?php echo $rubrique_couleur;?>" class="titre">
									<a href="/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" rel="address:/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" class="lien_event" id="titre_lien_<?php echo $row['evenement_id'];?>"><?php echo $event->get_title($row, $lang);?></a>
								</h1>								

								<p class="date h5-like"><?php echo $horaires;?></p>
								<p>
									<?php $resumeFacebook = $event->affiche_resume($row, $lang);?>
									<a href="/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" rel="address:/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" class="suite" style="background-color:<?php echo $rubrique_couleur; ?>" id="lien_suite_<?php echo $row['evenement_id'];?>">
										<img src="http://www.sciencespo.fr/evenements/images/lien_suite.png" alt=""/>
									</a>
								</p>
								
								<?php
									$organisateur = $event->get_organisateur($row, $lang);
									if($organisateur!=""){
								?>
										<p class="organisateur bit_small">
											<span style="background-color:<?php echo $rubrique_couleur; ?>"></span><?php echo $organisateur;?>									
										</p>
								<?php
									}
								?>

								<div class="reseaux">			
									<a href="http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=<?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;picture=<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id']; ?>/mini-<?php echo $row['evenement_image'];?>&amp;name=<?php echo $row['evenement_titre_en']; ?>&amp;caption=<?php echo $horaires; ?>&amp;description=<?php echo $resumeFacebook; ?>&amp;message=Sciences Po | events&amp;redirect_uri=<?php echo CHEMIN_FRONT_OFFICE; ?>" class="reseaux facebook" style="background-color:<?php echo $rubrique_couleur; ?>"  target="_blank">
									</a>

									<a href="http://twitter.com/home?status=Je participe à cet événement Sciences Po :  <?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;lang=en" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" class="reseaux twitter" style="background-color:<?php echo $rubrique_couleur; ?>">
									</a>

									<a href="makeIcal.php?id=<?php echo $row['evenement_id']; ?>" target="_blank" class="reseaux ical" style="background-color:<?php echo $rubrique_couleur; ?>">
									</a>
								</div>

								<a href="#" class="sinscrire bit_big" target="_blank"><span style="background-color:<?php echo $rubrique_couleur; ?>"></span><?php echo $sinscrire;?></a>
							</div>
				<?php
						}
					}
					else{
				?>
						<div id="pasderesultat"><p><?php echo $aucun;?></p></div>
				<?php
					}
				?>
			</div>


			<script id="event_info" type="text/html">
		        <article class="event resume">
		        	<div class="resumeContent">
		        		<div class="fermer" style="background-color:#fab80e">
		        			<a href="#" id="close"></a>
		        		</div>
		        		<div class="row">
		        			<div class="conteneur_detail">
			        			<div class="col visuel">
			        				<img src="img/visuel.png" id="vignette" />
			        				<h1 class="bit_big"><span class="bit_small">Art et culture</span> Germany before the elections</h1>
			        			</div>
			        			
			        			<div class="col informations">
			        				<h2 class="biggest">05/09 | 16h30-19h00</h2>
			        				<p class="langue"><span style="background-color:#fab80e"></span>Anglais</p>
				        			<p class="lieu"><span style="background-color:#fab80e"></span>Lieu</p>
				        			<p class="inscription"><span style="background-color:#fab80e"></span>Inscription obligatoire</p>
				        			<p class="organisateur"><span style="background-color:#fab80e"></span>Bibliothèque de Sciences Po</p>
				        			<p class="infos"><span style="background-color:#fab80e"></span>Infos</p>
			        			</div>
				        	</div>	

			        		<div class="col contenu">
			        			<div class="texte bit_big">
			        				<p>Guest speakers :</p>
			        				<p>Chair : <strong>Catherine Perron</strong>, CERI-Sciences Po</p>
									<p>- Ulrike Guérot, Representative for Germany and Senior Policy Fellow, European Council on Foreign Relations, Berlin German-European policy after the elections - business as usual?</p>
									<p>- Discussant : Christian Lequesne, Director, CERI-Sciences P</p>
									<p>- Prof. Klaus Dörre, Institute of Sociology, Friedrich Schiller University of Jena</p>
									<p>Activating Labour Market Reforms In Germany : <em>How Do They Impact Labour Market Orientations of Unemployed and Precarious Workers?</em></p>
									<p>- Discussant : to be determined</p>
									<p>- Severin Fischer, Fellow,  Stiftung für Wissenschaft und Politik (SWP), Berlin The Energiewende in the electoral campaign</p>
									<p>Discussant : Rachel Guyet, Project Manager, CERI-Sciences Po</p>
									<p>Activating Labour Market Reforms In Germany : <em>How Do They Impact Labour Market Orientations of Unemployed and Precarious Workers?</em></p>
									<p>- Discussant : to be determined</p>
									<p>- Severin Fischer, Fellow,  Stiftung für Wissenschaft und Politik (SWP), Berlin The Energiewende in the electoral campaign
									Discussant : Rachel Guyet, Project Manager, CERI-Sciences Po</p>
									<p>Discussant : Rachel Guyet, Project Manager, CERI-Sciences Po</p>
									<p>Guest speakers :</p>
			        				<p>Chair : Catherine Perron, CERI-Sciences Po</p>
									<p>- Ulrike Guérot, Representative for Germany and Senior Policy Fellow, European Council on Foreign Relations, Berlin German-European policy after the elections - business as usual?</p>
									<p>- Discussant : Christian Lequesne, Director, CERI-Sciences P</p>
									<p>- Prof. Klaus Dörre, Institute of Sociology, Friedrich Schiller University of Jena</p>
								</div>
		        			</div>

		        		</div>
		        		<div class="reset"></div>
		        		<div class="meta">
		        			<div>
				        		<a href="#" class="reseaux facebook" style="background-color:#fab80e"  target="_blank">
								</a>

								<a href="#" class="reseaux twitter" style="background-color:#fab80e">
								</a>

								<a href="#" target="_blank" class="reseaux ical" style="background-color:#fab80e">
								</a>
								<a href="#" class="sinscrire bit_big" id="inscription_submit"><span style="background-color:#fab80e"></span>SINSCRIRE</a>
							</div>
				        </div>
			        	<div class="reset"></div>
			        	<div class="bottom"  style="background-color:#fab80e"></div>
		        	</div>
		        </article>
		    </script>

		    <script id="inscription_form" type="text/html">
		    	<div id="cartouche">
		    		<h2 class="little_bigger">Inscription</h2>
		    		<div class="confirmation">
		    			<h3 class="little_bigger">Vous êtes bien inscrit à lévénement</h3>
		    			<h4 class="bit_big">Intégration économique et conflit de souveraineté : peut-on circonscrire le politique ?</h4>
		    			<p class="date bit_big">Samedi 06/07</p>
		    			<p class="horaire bit_big">de 18:00 à 19:30</p>
		    			<p class="lieu bit_big">Amphithéâtre</p>
		    		</div>

		    		<div class="informations_inscription">
		    			<p>Vos informations inscription sont les suivantes :</p>
		    			<p class="nom biggest">SOPHIE <span>sophie</span></p>
						<p class="lieu biggest">AMPHITHÉÂTRE</p>
						<p class="numero biggest">7101101201129</p>
		    		</div>

		    		<div class="important bit_small">
		    			<p><strong>IMPORTANT :</strong> Un mail contenant un billet au format .pdf vous a été envoyé à ladresse sophblum@free.fr. <strong>Veuillez imprimer le billet et vous présenter à laccueil à ladresse spécifiée.</strong></p>
		    		</div>

		    		<div class="mentions small">
		    			<p>Mention CNIL : Les informations qui vous concernent sont destinées exclusivement à Sciences Po. Vous disposez dun droit daccès, de modification, de rectification et de suppression des données qui vous concernent (art. 34 de la loi « Informatique et Libertés »). Pour lexercer, adressez-vous à Sciences Po Pôle Evénements - 27 rue Saint Guillaume - 75007 Paris</p>
		    		</div>

		    		<div class="deja_inscrit">
		    			<h3 class="little_bigger">VOUS ÊTES déjà inscrit à cet événement !</h3>
		    			<a href="#" class="bit_big">Retour à la liste dévénements</a>
		    		</div>

		    		<div class="description_evenement">
		    			<h3 class="bigger">Intégration économique et conflit de souveraineté : peut-on circonscrire le politique ?</h3>
		    			<p class="date very_bigger">Samedi 06/07</p>
		    			<p class="horaire very_bigger">de 18:00 à 19:30</p>
		    			<p class="lieu very_bigger">Amphithéâtre</p>
		    		</div>

		    		<div class="plus_de_place">
		    			<h3 class="bit_big">Vous êtes interne à Sciences po</h3>
		    			<p class="bit_small">Il n’y a plus de place «interne» à cet événement</p>
		    		</div>

		    		<div class="formulaire_interne">
		    			<h3 class="bit_big">Vous êtes interne à Sciences po</h3>
		    			<form>
			    			<p class="bit_small"><label for="login">Identifiant :</label><input type="text" id="login" name="login" /></p>
			    			<p class="bit_small"><label for="password">Mot de passe :</label><input type="password" id="password" name="password" /></p>
			    			<p class="erreur bit_small">Échec de connexion : identifiant ou mot de passe incorrect(s), veuillez recommencer.</p>
			    			<p class="bit_small"><a href="#" id="envoyer">Valider</a>
			    		</form>
		    		</div>

		    		<div class="formulaire_externe">
		    			<h3 class="bit_big">Vous êtes externe à Sciences po</h3>
		    		</div>
		    	</div>
		    </script>

		    <script id="validation_form" type="text/html">
		    	<div>
		    		<h2>{{reponse}} et :</h2>
		    		<p>votre login : <strong>{{login}}</strong></p>
		    		<p>votre mot de passe : <strong>{{password}}</strong></p>
		    		<p>votre adresse mail : <strong>{{email}}</strong></p>
		    	</div>
		    </script>
		</div>

		
	</body>

</html>
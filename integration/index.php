<?php
	include_once('../classe/classe_evenement.php');
	include_once('../classe/classe_rubrique.php');
	include_once('../classe/classe_organisme.php');

	$organisme = new organisme();
	$event = new evenement();
	$rubrique = new rubrique();

	if($_GET['lang']=="en"){
		$lang="en";
	}
	else{
		$lang="fr";
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
		<script type="text/javascript">

			// ISOTOPE Centré cf : http://jsfiddle.net/desandro/P6JGY/24/

			$(document).ready(function(){			
				var sauv = 0;
				$('.event>h1>a').click(function(e){
					e.preventDefault();
					
					var nextLastRowEvent;
					var prevFirstRowEvent;
					
					//On remets les éléments du bloc masqué en mode visible et dans l'état où ils étaient avant
					$('.selectedEvent > a').css('display','block');
					$('.selectedEvent > img').css('display','inline-block');
					$('.selectedEvent > p').css('display','block');
					$('.selectedEvent > div').css('display','block');
					$('.selectedEvent > span').css('display','block');
					$('.selectedEvent').height(sauv);
					sauv = $(this).parent().parent().height();
					$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
					$('.event').removeClass('selectedEvent');
					
					nextLastRowEvent = getNextLast($(this).parent().parent());
					prevFirstRowEvent = getPrevFirst($(this).parent().parent());
					var hauteurMaxTest=prevFirstRowEvent.height();
					getHauteurMax(prevFirstRowEvent, hauteurMaxTest, $(this).parent().parent());

					$(this).parent().parent().addClass('selectedEvent');
					
					clickEvent($('.selectedEvent'));
					$('.selectedEvent > a').css('display','none');
					$('.selectedEvent > img').css('display','none');
					$('.selectedEvent > p').css('display','none');
					$('.selectedEvent > div').css('display','none');
					$('.selectedEvent > span').css('display','none');
					
				    // on evite le comportement normal du click
				    
				});

				$origImgW = $('.banniere').width();
				$origImgH = $('.banniere').height();
				$ratioImg = $('.banniere').width() / $('.banniere').height();
				$('.maxDivImg').css('overflow','hidden');
				$('.maxDivImg').css('position','relative');
				$('.banniere').css('position','relative');
				
				redim();

				$(window).resize(function() {
					redim();
				});

				function redim(){
					$ratioDiv = $('.maxDivImg').width()/ $('.maxDivImg').height();
					
					if($ratioDiv>$ratioImg){
						$w = $('.maxDivImg').width();
						$h = $w/$ratioImg;
					}else{
						$h = $('.maxDivImg').height();
						$w = $h*$ratioImg;
					}
					
					$('.banniere').width($w);
					$('.banniere').height($h);
					$xPos = ($('.maxDivImg').width()-$w)/2+$('.maxDivImg').offset().left;
					$yPos = ($('.maxDivImg').height()-$h)/2+$('.maxDivImg').offset().top;

					$('.banniere').offset({left:$xPos,top:$yPos});

				}

				var jPM = $.jPanelMenu({
				    menu: '#menu_smartphone',
				    trigger: '#lien_menu_smartphone',
				    openPosition: '270px'
				});

				var jRes = jRespond([
				    {
				        label: 'small',
				        enter: 0,
				        exit: 692
				    },{
				        label: 'large',
				        enter: 692,
				        exit: 10000
				    }
				]);

				jRes.addFunc({
				    breakpoint: 'small',
				    enter: function() {
				        jPM.on();
				    },
				    exit: function() {
				        jPM.off();
				    }
				});

			})


			$(function(){
				var $container = $('#liste_evenements'),
				$body = $('body'),
				colW = 335,
				columns = null;


				var clickedElement,clickedID;

				// pour ajouter une élément après un autre
				// http://jsfiddle.net/9V2Mj/20/
				//
				// pour ajouter une classe first et une classe last
				// sur le premier et le dernier élément de chaque ligne
				// http://stackoverflow.com/questions/14552017/is-there-a-way-to-target-the-first-and-last-element-in-each-row-using-jquery-iso
				// 
				// var position = clickedElement.data('isotope-item-position');
				// console.log('item position is x: ' + position.x + ', y: ' + position.y  );

				$('#liste_evenements').isotope({
					// options
					resizable: false,
					itemSelector : '.event',
					layoutMode : 'fitRows',
					itemPositionDataEnabled : true,
					animationOptions: {
						duration: 750,
						easing: 'linear',
						queue: false,
					},
					getSortData : {
						number : function($elem) {
							return parseInt($elem.attr('data-sort'), 10);
						}
					},
					sortBy : 'number', // on trie sur le numéro qu'on a créé
					// pour ajouterles classes first et last 
					onLayout: function (elems, instance) {
						console.log('onLayout');
						var items, rows, numRows, row, prev, i;

						// gather info for each element
						items = elems.map(function () {
							var el = $(this), pos = el.data('isotope-item-position');
							return {
								x: pos.x,
								y: pos.y,
								w: el.width(),
								h: el.height(),
								el: el
							};
						});

						// first pass to find the first and last items of each row
						rows = [];
						i = {};
						items.each(function () {
							var y = this.y, r = i[y];
							if (!r) {
								r = {
									y: y,
									first: null,
									last: null
								};
								rows.push(r);
								i[y] = r;
							}
							if (!r.first || this.x < r.first.x) {
								r.first = this;
							}
							if (!r.last || this.x > r.last.x) {
								r.last = this;
							}
						});
						rows.sort(function (a, b) { return a.y - b.y; });
						numRows = rows.length;

						// compare items for each row against the previous row
						for (prev = rows[0], i = 1; i < numRows; prev = row, i++) {
							row = rows[i];
							if (prev.first.x < row.first.x &&
								prev.first.y + prev.first.h > row.y) {
							row.first = prev.first;
							}
							if (prev.last.x + prev.last.w > row.last.x + row.last.w &&
								prev.last.y + prev.last.h > row.y) {
							row.last = prev.last;
							}
						}

						// assign classes to first and last elements
						elems.removeClass('first last');
						$.each(rows, function () {
							this.first.el.addClass('first');
							this.last.el.addClass('last');
						});

						getNextLast($('.selectedEvent'));
						$('.isotope-hidden').removeClass('first last');
				    }
				});

				// filter items when filter link is clicked
				$('.filtre_isotope a').click(function(){
					$('.selectedEvent > a').css('display','block');
					$('.selectedEvent > img').css('display','inline-block');
					$('.selectedEvent > p').css('display','block');
					$('.selectedEvent > div').css('display','block');
					$('.selectedEvent > span').css('display','block');
					$('.selectedEvent').height($('.selectedEvent').height()-15);
					$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
					var selector = $(this).attr('data-filter');
					$container.isotope({ 
						filter: selector,
						resizable: false,
						itemSelector : '.event',
						layoutMode : 'fitRows',
						itemPositionDataEnabled : true,
						animationOptions: {
							duration: 750,
							easing: 'linear',
							queue: false,
						},
						getSortData : {
							number : function($elem) {
								return parseInt($elem.attr('data-sort'), 10);
							}
						},
						sortBy : 'number', // on trie sur le numéro qu'on a créé
						// pour ajouterles classes first et last 
						onLayout: function (elems, instance) {
							console.log('onLayout');
							var items, rows, numRows, row, prev, i;

							// gather info for each element
							items = elems.map(function () {
								var el = $(this), pos = el.data('isotope-item-position');
								alert(pos.x);
								return {
									x: pos.x,
									y: pos.y,
									w: el.width(),
									h: el.height(),
									el: el
								};
							});

							// first pass to find the first and last items of each row
							rows = [];
							i = {};
							items.each(function () {
								var y = this.y, r = i[y];
								if (!r) {
									r = {
										y: y,
										first: null,
										last: null
									};
									rows.push(r);
									i[y] = r;
								}
								if (!r.first || this.x < r.first.x) {
									r.first = this;
								}
								if (!r.last || this.x > r.last.x) {
									r.last = this;
								}
							});
							rows.sort(function (a, b) { return a.y - b.y; });
							numRows = rows.length;

							// compare items for each row against the previous row
							for (prev = rows[0], i = 1; i < numRows; prev = row, i++) {
								row = rows[i];
								if (prev.first.x < row.first.x &&
									prev.first.y + prev.first.h > row.y) {
								row.first = prev.first;
								}
								if (prev.last.x + prev.last.w > row.last.x + row.last.w &&
									prev.last.y + prev.last.h > row.y) {
								row.last = prev.last;
								}
							}

							// assign classes to first and last elements
							elems.removeClass('first last');
							$.each(rows, function () {
								this.first.el.addClass('first');
								this.last.el.addClass('last');
							});

							getNextLast($('.selectedEvent'));
							$('.isotope-hidden').removeClass('first last');
					    }
					});
					var sauv = 0;
					$('.event>h1>a').click(function(e){
						e.preventDefault();
						var nextLastRowEvent;
						var prevFirstRowEvent;
						console.log('click on event');
						//On remets les éléments du bloc masqué en mode visible et dans l'état où ils étaient avant
						$('.selectedEvent > a').css('display','block');
						$('.selectedEvent > img').css('display','inline-block');
						$('.selectedEvent > p').css('display','block');
						$('.selectedEvent > div').css('display','block');
						$('.selectedEvent > span').css('display','block');
						$('.selectedEvent').height(sauv);
						sauv = $(this).parent().parent().height();
						$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
						$('.event').removeClass('selectedEvent');
						nextLastRowEvent = getNextLast($(this).parent().parent());
						prevFirstRowEvent = getPrevFirst($(this).parent().parent());

						var hauteurMaxTest=prevFirstRowEvent.height();
						getHauteurMax(prevFirstRowEvent, hauteurMaxTest, $(this).parent().parent());

						$(this).parent().parent().addClass('selectedEvent');
						
						clickEvent($('.selectedEvent'));
						$('.selectedEvent > a').css('display','none');
						$('.selectedEvent > img').css('display','none');
						$('.selectedEvent > p').css('display','none');
						$('.selectedEvent > div').css('display','none');
						$('.selectedEvent > span').css('display','none');
					    // on evite le comportement normal du click
					    
					});

					
					return false;
				});


				$(window).smartresize(function(){

					// measure the width of all the items
					var itemTotalWidth = 0;
					$container.children().each(function(){
						itemTotalWidth += $(this).outerWidth(true);
					});

					// check if columns has changed
					var bodyColumns = Math.floor( ( $body.width() -10 ) / colW ),
					itemColumns = Math.floor( itemTotalWidth / colW ),
					currentColumns = Math.min( bodyColumns, itemColumns );
					if ( currentColumns !== columns ) {
						// set new column count
						columns = currentColumns;
						// apply width to container manually, then trigger relayout
						$container.width( columns * colW )
						.isotope('reLayout');
					}

				}).smartresize();
			});


			function clickEvent(clickedElement){
				var nextLastRowEvent;
				var prevFirstRowEvent;
				// on eneleve les classe selected et selectedEvent
				$('#liste_evenements').isotope( 'remove', $('.resume') );

				nextLastRowEvent = getNextLast(clickedElement);
				prevFirstRowEvent = getPrevFirst(clickedElement);

				event_data = {
	                titre:   "Le titre à afficher",
	                langue: "la langue ici du français",
	                organisation : "Le CERI ?",
	                inscription : "inscription obligatoire… ou pas !",
	            };

	            // Here's all the magic.
	            //var eventDetail = ich.event_info(event_data);

				// on crée le bloc de résumé des informations (après on va le créer avec iCanHaz + json pour les données) 
				var $newItems = ich.event_info(event_data);
				// on récupère l'ID de l'élément sur lequel on a cliqué et on l'incrémente
				clickedID = parseInt(nextLastRowEvent.attr('data-sort'),10)+1;
				// on attribue le nouvel ID au bloc de résumé
			    $newItems.attr('data-sort', clickedID);
			    $newItems.addClass('rubrique_3');
			    // on ajoute l'élément
			    $('#liste_evenements').isotope('insert', $newItems);
			    console.log('> add resume');

			    // quand on clique sur le bouton de fermeture de bloc événement
			    $('a#close').click(function(e){
					$('.selectedEvent > a').css('display','block');
					$('.selectedEvent > img').css('display','inline-block');
					$('.selectedEvent > p').css('display','block');
					$('.selectedEvent > div').css('display','block');
					$('.selectedEvent > span').css('display','block');
					$('.selectedEvent').height($('.selectedEvent').height()-15);
					$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
					$('#liste_evenements').isotope( 'remove', $('.resume') );
					console.log('> suppr resume');

					// on evite le comportement normal du click
					e.preventDefault();
				});
				$('a#inscription_submit').click(function(e){

					inscription_data = {
		                titre:"titre de l'événement",
		            };

		            inscription = ich.inscription_form(inscription_data);

                    $.fancybox( inscription , {
                        title : 'inscription à un événement',
                    });

                    validFancyBox();

					e.preventDefault();
				});
			}

			function validFancyBox(){
				$('a#envoyer').click(function(e){

            		validation_data = {
		                reponse:"Super ça fonctionne, vous êtes inscrit !",
		                login:$('#login').val(),
		                password:$('#password').val(),
		                email:$('#email').val(),
		            };

		            validation = ich.validation_form(validation_data);

                    $.fancybox( validation , {
                        title : 'validation de l‘inscription',
                    });

					e.preventDefault();
            	});
			}

			function getNextLast(cible){
				var target;
				$('.event').removeClass('nextLastRowItem');

				if( cible.hasClass('last')){
					target = cible;
				}else if( cible.next().hasClass('last')){
					target = cible.next();
				}else{
					var limit = $('.last');
					target = cible.nextUntil(limit, ".event")
						.last()
						.next();
				}

				target.addClass('nextLastRowItem');

				return target;

			}

			/*** Fonction récursive qui attribue à un élément la plus grande hauteur parmi des blocs d'une même ligne ***/
			function getHauteurMax(cible, hauteur, element){
				//on vérifie si la hauteur de la cible est supérieure à la plus grande hauteur actuelle
				if(cible.hasClass('isotope-hidden')){
					getHauteurMax(cible.next(), hauteur, element);
				}
				else{
					if(cible.height()>hauteur){
						hauteur = cible.height();
					}
					//si on est pas en bout de ligne, on exécute à nouveau la fonction
					if(!cible.hasClass('last')){
						getHauteurMax(cible.next(), hauteur, element);
					}
					//sinon on attribue la hauteur max à l'élément cliqué
					else{
						$(element).css('height',hauteur+15);
						return hauteur;
					}
				}
			}

			function getPrevFirst(cible){
				var target;
				$('.event').removeClass('prevFirstRowItem');

				if( cible.hasClass('first')){
					target = cible;
				}else if( cible.prev().hasClass('first')){
					target = cible.prev();
				}else{
					var limit = $('.first');
					target = cible.prevUntil(limit, ".event")
						.first()
						.prev();
				}

				target.addClass('prevFirstRowItem');

				return target;
			}


			$(window).resize(function() {
				console.log('> suppr resume');
				$('#liste_evenements').isotope( 'remove', $('.resume') );
				$('.selectedEvent > a').css('display','block');
				$('.selectedEvent > img').css('display','inline-block');
				$('.selectedEvent > p').css('display','block');
				$('.selectedEvent > div').css('display','block');
				$('.selectedEvent > span').css('display','block');
				$('.selectedEvent').height($('.selectedEvent').height()-15);
				$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
			});

			$(window).resizeend({
				onDragEnd : function(){
					console.log('end resize !!!');
				},
				runOnStart : true,
			});

		</script>

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
					<div id="filtre_categorie_smart" class="mb1">
						<ul>
							<li class="titre_filtre little_bigger">
								<span>Catégories</span>
								<ul id="filtering-nav-categorie">
							<?php		
									while($row = mysql_fetch_array($res)){
							?>
										<li class="" id="entre_cate_<?php echo $row['rubrique_id'];?>">
											<a class="rubrique_<?php echo $row['rubrique_id'];?> carre" href="#rubrique_<?php echo $row['rubrique_id'];?>" style="background:<?php echo $row['rubrique_couleur']; ?>;" id="entree_<?php echo $row['rubrique_id'];?>"></a>
											<a class="rubrique_<?php echo $row['rubrique_id'];?>" href="#rubrique_<?php echo $row['rubrique_id'];?>" id="entree_<?php echo $row['rubrique_id'];?>"><?php echo utf8_encode($row['rubrique_titre']);?></a>
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
				<div id="filtre_mot_smart" class="mb1">
					<ul>
						<li class="titre_filtre little_bigger">
							<span>Mots-clés</span>
							<ul id="filtering-nav-mot">
								<li class="" id="entree_mot_2"><a class="rubrique_2 carre" href="#rubrique_2" style="background:#fff;" id="entree_2"></a><a class="mot_2" href="#mot_2" id="entree_2">Sciences Po Paris</a></li>
								<li class="" id="entree_mot_5"><a class="rubrique_5 carre" href="#rubrique_5" style="background:#fff;" id="entree_5"></a><a class="mot_5" href="#mot_5" id="entree_5">Campus du Havre</a></li>
								<li class="" id="entree_mot_3"><a class="rubrique_3 carre" href="#rubrique_3" style="background:#fff;" id="entree_3"></a><a class="mot_3" href="#mot_3" id="entree_3">Autre mot-clé</a></li>
							</ul>
						</li>
					</ul>	
				</div>
			<?php
				if(count($tableauMois)>0){
			?>
					<div id="filtre_date_smart">
						<ul>
							<li class="titre_filtre little_bigger">
								<span>Dates</span>
								<ul id="filtering-nav-date">
			<?php
									foreach($tableauMois as $mois){
										if($lang=="fr"){
			?>
											<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="mois_<?php echo $mois['unique'];?> carre" href="#mois_<?php echo $mois['unique'];?>" style="background:#fff;" id="entree_mois_smart_<?php echo $mois['unique'];?>"></a><a class="mois_<?php echo $mois['unique'];?>" href="#mois_<?php echo $mois['unique'];?>" id="entree_<?php echo $mois['unique'];?>"><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
			<?php
										}
										else{
			?>
											<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="mois_<?php echo $mois['unique'];?> carre" href="#mois_<?php echo $mois['unique'];?>" style="background:#fff;" id="entree_mois_smart_<?php echo $mois['unique'];?>"></a><a class="mois_<?php echo $mois['unique'];?>" href="#mois_<?php echo $mois['unique'];?>" id="entree_<?php echo $mois['unique'];?>"><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
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
				<input type="submit" value="filtrer" class="very_small"/>
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
							<span class="francais"></span>
							<a href="index.php?lang=en" title="anglais" class="anglais"><span class="anglais"></span></a>
							<input type="text" name="mot_recherche" id="mot_recherche" value="Rechercher" class="small-hidden"/>
							<input type="submit" value="OK" class="valider_recherche small-hidden"/>
							<a href="rss_events.php?lang=fr" class="rss"></a>
							<!--<div id="recherche_avancee">
								<div>
									<p class="avancee ouvert">Recherche avancée</p>
									<ul>
										<li class="" id="entre_liste_2"><a class="rubrique_2 carre" href="#rubrique_2" style="border:2px solid #fab80e;" id="entree_2"><span style="background:#fab80e;"></span></a><a class="rubrique_2" href="#rubrique_2" id="texte_entree_2">Art et culture</a></li>

										<li class="" id="entre_liste_5"><a class="rubrique_5 carre" href="#rubrique_5" style="border:2px solid #aaff55;" id="entree_5"><span style="background:#aaff55;"></span></a><a class="rubrique_5" href="#rubrique_5" id="texte_entree_5">Expositions</a></li>

										<li class="" id="entre_liste_3"><a class="rubrique_3 carre" href="#rubrique_3" style="border:2px solid #e63f81;" id="entree_3"><span style="background:#e63f81;"></span></a><a class="rubrique_3" href="#rubrique_3" id="texte_entree_3">Recherche et débats</a></li>
									</ul>
								</div>
							</div>-->
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
							<div id="filtre_categorie" class="small-hidden filtre_isotope">
								<ul>
									<li class="titre_filtre bit_small">
										<span>Catégories</span>
										<ul id="filtering-nav-categorie">
									<?php		
											while($row = mysql_fetch_array($res)){
									?>
												<li class="" id="entre_cate_<?php echo $row['rubrique_id'];?>">
													<a class="carre" href="#" style="background:<?php echo $row['rubrique_couleur']; ?>;" data-filter=".rubrique_<?php echo $row['rubrique_id'];?>"></a>
													<a class="" href="#" data-filter=".rubrique_<?php echo $row['rubrique_id'];?>"><?php echo utf8_encode($row['rubrique_titre']);?></a>
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
							<div id="filtre_date" class="small-hidden filtre_isotope">
								<ul>
									<li class="titre_filtre bit_small">
										<span>Dates</span>
										<ul id="filtering-nav-date">
					<?php
											foreach($tableauMois as $mois){
												if($lang=="fr"){
					?>
													<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="" href="#" data-filter=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
					<?php
												}
												else{
					?>
													<li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="" href="#" data-filter=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
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

								<p class="organisateur bit_small">
									<span style="background-color:<?php echo $rubrique_couleur; ?>"></span><?php echo $event->get_organisateur($row, $lang);?>									
								</p>
								
								<div class="reseaux">			
									<a href="http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=<?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;picture=<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id']; ?>/mini-<?php echo $row['evenement_image'];?>&amp;name=<?php echo $row['evenement_titre_en']; ?>&amp;caption=<?php echo $horaires; ?>&amp;description=<?php echo $resumeFacebook; ?>&amp;message=Sciences Po | events&amp;redirect_uri=<?php echo CHEMIN_FRONT_OFFICE; ?>" class="reseaux facebook" style="background-color:<?php echo $rubrique_couleur; ?>"  target="_blank">
									</a>

									<a href="http://twitter.com/home?status=Je participe à cet événement Sciences Po :  <?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;lang=en" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" class="reseaux twitter" style="background-color:<?php echo $rubrique_couleur; ?>">
									</a>

									<a href="makeIcal.php?id=<?php echo $row['evenement_id']; ?>" target="_blank" class="reseaux ical" style="background-color:<?php echo $rubrique_couleur; ?>">
									</a>
								</div>

								<a href="#" class="sinscrire bit_big" target="_blank"><span style="background-color:<?php echo $rubrique_couleur; ?>"></span>S'INSCRIRE</a>
							</div>
				<?php
						}
					}
					else{
				?>
						<div id="pasderesultat"><p>Il n'y a aucun événement à venir.</p></div>
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
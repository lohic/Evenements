$(document).ready(function(){
	$maxBoxHeight = 0;

	$('.box').each(function(){
		if($(this).outerHeight()>$maxBoxHeight){
			$maxBoxHeight = $(this).outerHeight();
		}
	});

	$('.box').height($maxBoxHeight);
	
	
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

		/// POUR RENVOYER LES VARIABLES DANS LE DIV #retour_val
		//$('#retour_val').text($xPos+' '+$yPos+' '+$('.maxDivImg').width());
	}
	
	function getUrlVars()
	{
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for(var i = 0; i < hashes.length; i++)
	    {
	        hash = hashes[i].split('=');
	        vars.push(hash[0]);
	        vars[hash[0]] = hash[1];
	    }
	    return vars;
	}

	
	$('#mot_recherche').click(function(){
		$(this).val('');
	});


		$(function(){
			var 
			speed = 750,   // animation speed
			$wall = $('#demo').find('.wrap')
			;

			$wall.masonry({
				columnWidth: 10, 
				// only apply masonry layout to visible elements
				itemSelector: '.box:not(.invis)',
				animate: true,
				animationOptions: {
					duration: speed,
					queue: false
				}
			});
			
			var moisClass = "";
			var colorClass = "";
			var identifiantMenu="entre_liste_10000";
			$('#filtering-nav a').click(function(){
				$('#pasderesultat').hide();
				colorClass = '.' + $(this).attr('class');
				
				if(colorClass=='.all') {
					// show all hidden boxes
					if(moisClass=='.tous'){
						$wall.children('.invis')
						.toggleClass('invis').fadeIn(speed);
					}
					else{
						$wall.children(moisClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
					
				} else {  
					// hide visible boxes 
					$wall.children().not(colorClass).not('.invis')
					.toggleClass('invis').fadeOut(speed);
					// show hidden boxes
					if(moisClass=='.tous'){
						$wall.children(colorClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
					else{
						$wall.children(moisClass+colorClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
				}
				$wall.masonry();

				$("#filtering-nav>li").removeClass("actif");  
				if(typeof(identifiantMenu) == 'undefined'){
					identifiantMenu="entre_liste_10000";
				}      
               	document.getElementById(identifiantMenu).style.background="none";
				var tableau_id=$(this).attr("id").split('_');
				identifiantMenu = "entre_liste_"+tableau_id[1];
				document.getElementById(identifiantMenu).className="actif";
				document.getElementById(identifiantMenu).style.background=document.getElementById("couleurLiens").innerHTML;
                
				
				return false;
			});

			$('#filtering-mois').change(function(){
				$('#pasderesultat').hide();
				var laclasse = $('#filtering-mois').val();

				document.getElementById('filtering-mois').className=laclasse;
				moisClass = '.' + $(this).attr('class');

				if(moisClass=='.tous') {
					if(colorClass=='.all'){
						$wall.children('.invis')
						.toggleClass('invis').fadeIn(speed);
					}
					else{
						$wall.children(colorClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
				} else {  
					// hide visible boxes 
					$wall.children().not(moisClass).not('.invis')
					.toggleClass('invis').fadeOut(speed);
					// show hidden boxes
					if(colorClass=='.all'){
						$wall.children(moisClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
					else{
						$wall.children(colorClass+moisClass+'.invis')
						.toggleClass('invis').fadeIn(speed);
					}
				}
				$wall.masonry();

				return false;
			});


			if(typeof($.address.parameter('id'))!="undefined"){
				$.post("chargeEvenementAJAX.php", { id: $.address.parameter('id') },
				function(data){
					$('#master-event').html(data);
					document.getElementById('demo').style.marginLeft="500px";
					$wall = $('#demo').find('.wrap');
					$wall.masonry();
				});
			}

			var urlEvenement = getUrlVars()["id"];
			if(typeof(urlEvenement)!="undefined"){
				$.post("chargeEvenementAJAX.php", { id: urlEvenement },
				function(data){
					$('#master-event').html(data);
					document.getElementById('demo').style.marginLeft="500px";
					$wall = $('#demo').find('.wrap');
					$wall.masonry();
				});
			}	



		});

		$("a.suite").click(function(e){
			e.preventDefault();
			$('#pasderesultat').hide();
			var tableau_id=$(this).attr("id").split('_');		
			$.post("chargeEvenementAJAX.php", { id: tableau_id[2] },
			function(data){
				$('#master-event').html(data);
				document.getElementById('demo').style.marginLeft="500px";
				$wall = $('#demo').find('.wrap');
				$wall.masonry();


				$('html,body').animate({scrollTop:260}, 'slow');
				$("a.lien_fermeture").click(function(e){
					e.preventDefault();
					$('#master-event').html('');
					document.getElementById('demo').style.marginLeft="0px";	
					$wall = $('#demo').find('.wrap');
					$wall.masonry();
				});

			});	
		});

		$("a.lien_event").click(function(e){
			e.preventDefault();
			$('#pasderesultat').hide();
			var tableau_id=$(this).attr("id").split('_');							
			$.post("chargeEvenementAJAX.php", { id: tableau_id[2] },
			function(data){
				$('#master-event').html(data);
				document.getElementById('demo').style.marginLeft="500px";
				$wall = $('#demo').find('.wrap');
				$wall.masonry();
				$('html,body').animate({scrollTop: 260}, 'slow');



				$("a.lien_fermeture").click(function(e){
					e.preventDefault();
					$('#master-event').html('');
					document.getElementById('demo').style.marginLeft="0px";	
					$wall = $('#demo').find('.wrap');
					$wall.masonry();
					
				});
			});
		});

		$("input.valider_recherche").click(function(e){
			e.preventDefault();	
			$.get("chargeListeEvenementsAJAX.php", { recherche: document.getElementById('mot_recherche').value, filtre: document.getElementById('secteur_recherche').value },
			function(data){
				$('.testMasonry').html(data);
				$wall.masonry();
				//document.getElementById('demo').style.marginLeft="0px";	
			});
		});
});
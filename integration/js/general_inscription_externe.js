// ISOTOPE Centré cf : http://jsfiddle.net/desandro/P6JGY/24/
$.urlParam = function(name){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}


$(document).ready(function(){			
	
	alert(decodeURIComponent($.urlParam('session'))); 

	/*$('a.sinscrire').click(function(e){
		$(this).parent().addClass('sinscrireEvent');
		var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

		if($('.sinscrireEvent').hasClass('en')){
			var la_langue="en";
		}
		else{
			var la_langue="fr";
		}

		var code = "test";
		e.preventDefault();
		$.ajax({
	        url     :"ajax/get_event_infos_inscription.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_event : evenement_id,
	            langue : la_langue,
	            code : code,
	            cache : new Date()
	        }
	    }).done(function (dataJSON) {
	    	console.log(dataJSON.titre);
			inscription_data = {
				id:   dataJSON.evenement_id,
				session_id:   dataJSON.session_id,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        lieu: dataJSON.lieu,
		        casque:   dataJSON.casque,
		        interneOuvert: dataJSON.interneOuvert,
		        interneComplet: dataJSON.interneComplet,
		        externeOuvert:   dataJSON.externeOuvert,
		        externeComplet: dataJSON.externeComplet,
		        toutClos: dataJSON.toutClos,
		        toutComplet:   dataJSON.toutComplet,
		        alerteInterne: dataJSON.alerteInterne,
		        alerteExterne: dataJSON.alerteExterne,
		        code: dataJSON.codeExterne,
		        mention : dataJSON.mention,
	        };

	        inscription = ich.inscription_form(inscription_data);

	        $.fancybox( inscription , {
	            title : 'Inscription',
	        });

	        validFancyBox();
	    });
	});

	$('a.sinscrire_multiple').click(function(e){
		$(this).parent().addClass('sinscrireEvent');
		var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

		if($('.sinscrireEvent').hasClass('en')){
			var la_langue="en";
		}
		else{
			var la_langue="fr";
		}
		var code = "";
		e.preventDefault();
		$.ajax({
	        url     :"ajax/get_event_infos_inscription_multiple.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_event : evenement_id,
	            langue : la_langue,
	            code : code,
	            cache : new Date()
	        }
	    }).done(function (dataJSON) {
	    	console.log(dataJSON.titre);
			inscription_data = {
				id:   dataJSON.evenement_id,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        sessions: dataJSON.sessions,
		        code: dataJSON.codeExterne,
		        mention : dataJSON.mention,
	        };

	        inscription = ich.inscription_form_multiple(inscription_data);

	        $.fancybox( inscription , {
	            title : 'Inscription',
	        });

	        validFancyBox();
	    });
	});*/
})

function clickEvent(clickedElement){
	var nextLastRowEvent;
	var prevFirstRowEvent;
	// on eneleve les classe selected et selectedEvent
	$('#liste_evenements').isotope( 'remove', $('.resume') );

	nextLastRowEvent = getNextLast(clickedElement);
	prevFirstRowEvent = getPrevFirst(clickedElement);

	var evenement_id = clickedElement.find('h1 > a').attr('id').split('_')[2];

	if(clickedElement.hasClass('en')){
		var la_langue="en";
	}
	else{
		var la_langue="fr";
	}

	$.ajax({
        url     :"ajax/get_event_infos.php",
        type    : "GET",
        dataType:'json',
        data    : {
            id_event : evenement_id,
            langue : la_langue
        }
    }).done(function (dataJSON) {
    	event_data = {
	        titre:   dataJSON.titre,
	        date: dataJSON.date,
	        rubrique: dataJSON.rubrique,
	        couleur: dataJSON.couleur,
	        langue: dataJSON.langue,
	        lieu: dataJSON.lieu,
	        batiment: dataJSON.batiment,
	        organisateur: dataJSON.organisateur,
	        coorganisateur: dataJSON.coorganisateur,
	        infos: dataJSON.lien,
	        infos_texte: dataJSON.texte_lien,
	        inscription: dataJSON.inscription,
	        image: dataJSON.image,
	        texte_image: dataJSON.texte_image,
	        texte: dataJSON.texte,
	        facebook: dataJSON.facebook,
	        twitter: dataJSON.twitter,
	        ical: dataJSON.ical,
	        sinscrire: dataJSON.sinscrire,
	    };
	    // on crée le bloc de résumé des informations (après on va le créer avec iCanHaz + json pour les données) 
		var $newItems = ich.event_info(event_data);
		// on récupère l'ID de l'élément sur lequel on a cliqué et on l'incrémente
		clickedID = parseInt(nextLastRowEvent.attr('data-sort'),10)+1;
		// on attribue le nouvel ID au bloc de résumé
	    $newItems.attr('data-sort', clickedID);
	    $newItems.addClass('rubrique_'+dataJSON.rubrique_id);
	    // on ajoute l'élément
	    $('#liste_evenements').isotope('insert', $newItems);
	    console.log('> add resume');
        console.log(dataJSON);

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

		$('a.sinscrire').click(function(e){
			var code = "test";
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code,
		            cache : new Date()
		        }
		    }).done(function (dataJSON) {
		    	console.log(dataJSON.titre);
				inscription_data = {
					id:   dataJSON.evenement_id,
					session_id:   dataJSON.session_id,
		            titre:   dataJSON.titre,
			        date: dataJSON.date,
			        lieu: dataJSON.lieu,
			        casque:   dataJSON.casque,
			        interneOuvert: dataJSON.interneOuvert,
			        interneComplet: dataJSON.interneComplet,
			        externeOuvert:   dataJSON.externeOuvert,
			        externeComplet: dataJSON.externeComplet,
			        toutClos: dataJSON.toutClos,
			        toutComplet:   dataJSON.toutComplet,
			        alerteInterne: dataJSON.alerteInterne,
			        alerteExterne: dataJSON.alerteExterne,
			        code: dataJSON.codeExterne,
			        mention : dataJSON.mention,
		        };

		        inscription = ich.inscription_form(inscription_data);

		        $.fancybox( inscription , {
		            title : 'Inscription',
		        });

		        validFancyBox();
		    });
		});

		$('a.sinscrire_multiple').click(function(e){
			var code = "";
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription_multiple.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code,
		            cache : new Date()
		        }
		    }).done(function (dataJSON) {
		    	console.log(dataJSON.titre);
				inscription_data = {
					id:   dataJSON.evenement_id,
		            titre:   dataJSON.titre,
			        date: dataJSON.date,
			        sessions: dataJSON.sessions,
			        code: dataJSON.codeExterne,
			        mention : dataJSON.mention,
		        };

		        inscription = ich.inscription_form_multiple(inscription_data);

		        $.fancybox( inscription , {
		            title : 'Inscription',
		        });

		        validFancyBox();
		    });
		});
    });
}

function validFancyBox(){
	$('a#envoyer_externe, a#renvoyer_externe').click(function(e){
		e.preventDefault();
		$.ajax({
	        url     :"ajax/make_inscription_externe.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_session : $('#id_session').val(),
	            nom:$('#nom').val(),
	            prenom:$('#prenom').val(),
	            mail:$('#mail').val(),
	            entreprise:$('#entreprise').val(),
	            fonction:$('#fonction').val(),
	            casque:$('#inscrit_casque').val(),
	            titre:$('#titre').val(),
	            date:$('#date').val(),
	            lieu:$('#lieu').val()
	        }
	    }).done(function (dataJSON) {
			validation_data = {
				session_id:   dataJSON.session_id,
				title: dataJSON.titre_bloc,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        lieu: dataJSON.lieu,
		        infos_inscription: dataJSON.infos_inscription,
		        nom:   dataJSON.nom,
		        prenom: dataJSON.prenom,
		        type: dataJSON.type_inscription,
		        numero:   dataJSON.numero,
		        important: dataJSON.important,
		        casque:   dataJSON.casque,
		        alerteExterne: dataJSON.alerteExterne,
		        erreurChamps: dataJSON.erreurChamps,
		        inscriptionOK: dataJSON.inscriptionOK,
		        dejaInscrit: dataJSON.dejaInscrit,
		        completeDerniereMinute: dataJSON.completeDerniereMinute,
	        };

	        validation = ich.validation_externe_form(validation_data);

	        $.fancybox( validation , {
	            title : 'validation de l‘inscription',
	        });

	        validFancyBox();
	    });
	});

	$('a#envoyer_externe_multiple, a#renvoyer_externe_multiple').click(function(e){
		e.preventDefault();

		var inputs = document.getElementsByTagName("input");
		var tabsessions = [];
		var tabcasques = [];
		for(var i=0,l=inputs.length;i<l;i++) {
			if(inputs[i].name == "sessions[]" && inputs[i].checked == true) {
				tabsessions.push(inputs[i].value);
			}
			if(inputs[i].name == "inscrit_casque[]" && inputs[i].checked == true) {
				tabcasques.push(inputs[i].value);
			}
		}

		$.ajax({
	        url     :"ajax/make_inscription_externe_multiple.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            sessions : tabsessions,
	            id_evenement : $('#id_evenement').val(),
	            nom:$('#nom').val(),
	            prenom:$('#prenom').val(),
	            mail:$('#mail').val(),
	            entreprise:$('#entreprise').val(),
	            fonction:$('#fonction').val(),
	            casques : tabcasques,
	            titre:$('#titre').val(),
	            date:$('#date').val(),
	        }
	    }).done(function (dataJSON) {
			validation_data = {
				title: dataJSON.titre_bloc,
				id:   dataJSON.evenement_id,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        infos_inscription: dataJSON.infos_inscription,
		        nom: dataJSON.nom,
		        prenom: dataJSON.prenom,
		        sessions: dataJSON.sessions,
		        toutesLesSessions: dataJSON.toutesLesSessions,
		        important: dataJSON.important,
		        erreurChamps: dataJSON.erreurChamps,
		        inscritPartout: dataJSON.inscritPartout,
				tousDerniereMinute: dataJSON.tousDerniereMinute,
		        mention : dataJSON.mention,
	        };

	        validation = ich.validation_externe_form_multiple(validation_data);

	        $.fancybox( validation , {
	            title : 'validation de l‘inscription',
	        });

	        validFancyBox();
	    });
	});
}
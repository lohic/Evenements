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
	if(decodeURIComponent($.urlParam('code')) != "null"){
		if(decodeURIComponent($.urlParam('session')) != "null" && decodeURIComponent($.urlParam('evenement')) != "null"){
			$.ajax({
		        url     :"ajax/get_event_infos_inscription_externe.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		        	id_session : decodeURIComponent($.urlParam('session')),
		            id_event : decodeURIComponent($.urlParam('evenement')),
		            code : decodeURIComponent($.urlParam('code'))
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
			        externeOuvert:   dataJSON.externeOuvert,
			        externeComplet: dataJSON.externeComplet,
			        alerteExterne: dataJSON.alerteExterne,
			        code: dataJSON.codeExterne,
			        codeErreur: dataJSON.codeErreur,
			        mention : dataJSON.mention,
		        };

		        inscription = ich.inscription_externe_form(inscription_data);

		        $.fancybox( inscription , {
		            title : 'Inscription',
		        });

		        validFancyBox();
		    });
		}
		else{
			if(decodeURIComponent($.urlParam('evenement')) != "null"){
				$.ajax({
			        url     :"ajax/get_event_infos_inscription_multiple_externe.php",
			        type    : "GET",
			        dataType:'json',
			        data    : {
			            id_event : decodeURIComponent($.urlParam('evenement')),
		            	code : decodeURIComponent($.urlParam('code'))
			        }
			    }).done(function (dataJSON) {
			    	console.log(dataJSON.titre);
					inscription_data = {
						id:   dataJSON.evenement_id,
			            titre:   dataJSON.titre,
				        date: dataJSON.date,
				        sessions: dataJSON.sessions,
				        code: dataJSON.codeExterne,
				        codeErreur: dataJSON.codeErreur,
				        externeOuvert: dataJSON.externeOuvert,
				        mention : dataJSON.mention,
			        };

			        inscription = ich.inscription_externe_form_multiple(inscription_data);

			        $.fancybox( inscription , {
			            title : 'Inscription',
			        });

			        validFancyBox();
			    });
			}
		}	
	}
});

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

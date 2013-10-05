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
	var sauv = 0;
	var $container = $('#liste_evenements'), filters = {};
	
	$('.event>h1>a, .event > p > a.suite').click(function(e){
		e.preventDefault();
		
		var nextLastRowEvent;
		var prevFirstRowEvent;
		
		//On remets les éléments du bloc masqué en mode visible et dans l'état où ils étaient avant
		$('.selectedEvent > a').css('display','block');
		$('.selectedEvent > img').css('display','inline-block');
		$('.selectedEvent > p').css('display','block');
		$('.selectedEvent > div').css('display','block');
		$('.selectedEvent > div.triangle_inverse').css('display','none');
		$('.selectedEvent > span').css('display','block');
		$('.selectedEvent').css('background','#fff');
		$('.selectedEvent').height(sauv);
		sauv = $(this).parent().parent().height();
		$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
		$('.event').removeClass('selectedEvent');
		
		nextLastRowEvent = getNextLast($(this).parent().parent());
		prevFirstRowEvent = getPrevFirst($(this).parent().parent());
		var hauteurMaxTest=prevFirstRowEvent.height();
		getHauteurMax(prevFirstRowEvent, hauteurMaxTest, $(this).parent().parent());

		$(this).parent().parent().addClass('selectedEvent');
		
		var classe = $(this).attr('class').split(' ')[1];
		var couleur = classe.split('_')[1];
		couleur = couleur.split('#')[1];
		clickEvent($('.selectedEvent'), sauv);
		$('.selectedEvent').css('background','none');
		$('.selectedEvent > a').css('display','none');
		$('.selectedEvent > img').css('display','none');
		$('.selectedEvent > p').css('display','none');
		$('.selectedEvent > div').css('display','none');
		$('.selectedEvent > span').css('display','none');	
		$('.selectedEvent > div.triangle_inverse').css('display','block');
	    // on evite le comportement normal du click
	    
	});

	$('a.sinscrire').click(function(e){
		$('.isotope-item').removeClass('sinscrireEvent');
		$(this).parent().addClass('sinscrireEvent');
		var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

		if($('.sinscrireEvent').hasClass('en')){
			var la_langue="en";
		}
		else{
			var la_langue="fr";
		}

		var code = "";

		if($(this).attr('id')=="avec_code"){
			code="oui";
		}

		e.preventDefault();
		$.ajax({
	        url     :"ajax/get_event_infos_inscription.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_event : evenement_id,
	            langue : la_langue,
	            code : code
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
		$('.isotope-item').removeClass('sinscrireEvent');
		$(this).parent().addClass('sinscrireEvent');
		var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

		if($('.sinscrireEvent').hasClass('en')){
			var la_langue="en";
		}
		else{
			var la_langue="fr";
		}

		var code = "";

		if($(this).attr('id')=="avec_code"){
			code="oui";
		}
		e.preventDefault();
		$.ajax({
	        url     :"ajax/get_event_infos_inscription_multiple.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_event : evenement_id,
	            langue : la_langue,
	            code : code
	        }
	    }).done(function (dataJSON) {
	    	console.log(dataJSON.titre);
			inscription_data = {
				id:   dataJSON.evenement_id,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        sessions: dataJSON.sessions,
		        code: dataJSON.codeExterne,
		        interneOuvert: dataJSON.interneOuvert,
		        externeOuvert: dataJSON.externeOuvert,
		        toutComplet: dataJSON.toutComplet,
		        mention : dataJSON.mention,
	        };

	        inscription = ich.inscription_form_multiple(inscription_data);

	        $.fancybox( inscription , {
	            title : 'Inscription',
	        });

	        validFancyBox();
	    });
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

	function getComboFilter( filters ) {
		var i = 0;
		var comboFilters = [];
		var message = [];

		for ( var prop in filters ) {
			message.push( filters[ prop ].join(' ') );
			var filterGroup = filters[ prop ];
			// skip to next filter group if it doesn't have any values
			if ( !filterGroup.length ) {
				continue;
			}
			if ( i === 0 ) {
				// copy to new array
				comboFilters = filterGroup.slice(0);
			} 
			else {
				var filterSelectors = [];
				// copy to fresh array
				var groupCombo = comboFilters.slice(0); // [ A, B ]
				// merge filter Groups
				for (var k=0, len3 = filterGroup.length; k < len3; k++) {
					for (var j=0, len2 = groupCombo.length; j < len2; j++) {
						filterSelectors.push( groupCombo[j] + filterGroup[k] ); // [ 1, 2 ]
		    		}

		  		}
				// apply filter selectors to combo filters for next group
				comboFilters = filterSelectors;
			}
			i++;
		}

		var comboFilter = comboFilters.join(', ');
		return comboFilter;
	}

	function manageCheckbox( $checkbox ) {
		var checkbox = $checkbox[0];

		var group = $checkbox.parents('.option-set').attr('data-group');
		// create array for filter group, if not there yet
		var filterGroup = filters[ group ];
		if ( !filterGroup ) {
			filterGroup = filters[ group ] = [];
		}

		var isAll = $checkbox.hasClass('all');
		// reset filter group if the all box was checked
		if ( isAll ) {
			delete filters[ group ];
			if ( !checkbox.checked ) {
				checkbox.checked = 'checked';
			}
		}
		// index of
		var index = $.inArray( checkbox.value, filterGroup );

		if ( checkbox.checked ) {
			var selector = isAll ? 'input' : 'input.all';
			$checkbox.siblings( selector ).removeAttr('checked');


			if ( !isAll && index === -1 ) {
				// add filter to group
				filters[ group ].push( checkbox.value );
			}

		} 
		else if ( !isAll ) {
			// remove filter from group
			filters[ group ].splice( index, 1 );
			// if unchecked the last box, check the all
			if ( !$checkbox.siblings('[checked]').length ) {
				$checkbox.siblings('input.all').attr('checked', 'checked');
			}
		}

	}

	var jPM = $.jPanelMenu({
	    menu: '#menu_smartphone',
	    trigger: '#lien_menu_smartphone, #validation_smart',
	    openPosition: '270px',
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
	        $('#jPanelMenu-menu .checkbox_smart').each(function(){
	    		var nouvel_identifiant = $(this).attr('id')+"_smart";
	    		$(this).attr('id', nouvel_identifiant);
	    		$(this).next().attr('for', nouvel_identifiant);
	    	});
	    	$('#jPanelMenu-menu label').click(function(e){
	    		if($(this).hasClass('all')){
	    			$(this).siblings('label').find('span').addClass('vide');

	    			if($(this).find('span').hasClass('vide')){
		    			$(this).find('span').removeClass('vide');
		    		}
		    		else{
		    			$(this).find('span').addClass('vide');
		    		}
	    		}
	    		else{
	    			$(this).siblings('label.all').find('span').addClass('vide');

	    			if($(this).find('span').hasClass('vide')){
		    			$(this).find('span').removeClass('vide');
		    		}
		    		else{
		    			$(this).find('span').addClass('vide');
		    		}
	    		}
		    });

	    	$('#jPanelMenu-menu #validation_smart').click(function(e){
		        var comboFilter = getComboFilter( filters );
		        $container.isotope({ filter: comboFilter });
		        e.preventDefault();
		    });

	    	$('#jPanelMenu-menu #options_smart').on('change', function( jQEvent ) {
			    var $checkbox = $( jQEvent.target );
			    manageCheckbox( $checkbox );
			});
	    },
	    exit: function() {
	        jPM.off();
	    }
	});

	$('.nom_du_filtre').click(function(e){
		if($(this).text()=="Toutes" || $(this).text()=="Tous"){
			if($(this).parent().parent().attr('id')=='filtering-nav-categorie'){
				$(this).parent().parent().parent().find('span.le_titre_filtre').text('Catégories');
			}
			if($(this).parent().parent().attr('id')=='filtering-nav-date'){
				$(this).parent().parent().parent().find('span.le_titre_filtre').text('Dates');
			}
			if($(this).parent().parent().attr('id')=='filtering-nav-mot'){
				$(this).parent().parent().parent().find('span.le_titre_filtre').text('Mots-clés');
			}
			$(this).parent().parent().parent().find('span.le_titre_filtre').css('text-transform', 'uppercase');
		}
		else{
			$(this).parent().parent().parent().find('span.le_titre_filtre').text($(this).text());
			$(this).parent().parent().parent().find('span.le_titre_filtre').css('text-transform', 'none');
		}
	});  
})


$(function(){  
	var $container = $('#liste_evenements'), filters = {};
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

			//initIsotopeOuvert();
	    }
	});

	// filter items when filter link is clicked
	$('.filtre_isotope a').click(function(){
		$('.selectedEvent > a').css('display','block');
		$('.selectedEvent > img').css('display','inline-block');
		$('.selectedEvent > p').css('display','block');
		$('.selectedEvent > div').css('display','block');
		$('.selectedEvent > div.triangle_inverse').css('display','none');
		$('.selectedEvent > span').css('display','block');
		$('.selectedEvent').css('background','#fff');
		$('.selectedEvent').height($('.selectedEvent').height()-15);
		$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');

		var $this = $(this);
		// don't proceed if already selected
		if ( $this.hasClass('selected') ) {
		return;
		}

		var $optionSet = $this.parents('.option-set');
		// change selected class
		$optionSet.find('.selected').removeClass('selected');
		$this.addClass('selected');

		// store filter value in object
		// i.e. filters.color = 'red'
		var group = $optionSet.attr('data-filter-group');
		filters[ group ] = $this.attr('data-filter-value');
		// convert object into array
		var isoFilters = [];
		for ( var prop in filters ) {
		isoFilters.push( filters[ prop ] )
		}
		var selector = isoFilters.join('');

		$container.isotope({ filter: selector,
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

		return false;

		var sauv = 0;
		$('.event>h1>a, .event > p > a.suite').click(function(e){
			e.preventDefault();
			var nextLastRowEvent;
			var prevFirstRowEvent;
			console.log('click on event');
			//On remets les éléments du bloc masqué en mode visible et dans l'état où ils étaient avant
			
			$('.selectedEvent > a').css('display','block');
			$('.selectedEvent > img').css('display','inline-block');
			$('.selectedEvent > p').css('display','block');
			$('.selectedEvent > div').css('display','block');
			$('.selectedEvent > div.triangle_inverse').css('display','none');
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
			
			var classe = $(this).attr('class').split(' ')[1];
			var couleur = classe.split('_')[1];
			couleur = couleur.split('#')[1];
			clickEvent($('.selectedEvent'), sauv);
			$('.selectedEvent').css('background','none');
			$('.selectedEvent > a').css('display','none');
			$('.selectedEvent > img').css('display','none');
			$('.selectedEvent > p').css('display','none');
			$('.selectedEvent > div').css('display','none');
			$('.selectedEvent > span').css('display','none');	
			$('.selectedEvent > div.triangle_inverse').css('display','block');
		    // on evite le comportement normal du click
		});

		$('a.sinscrire').click(function(e){
			$('.isotope-item').removeClass('sinscrireEvent');
			$(this).parent().addClass('sinscrireEvent');
			var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

			if($('.sinscrireEvent').hasClass('en')){
				var la_langue="en";
			}
			else{
				var la_langue="fr";
			}

			var code = "";

			if($(this).attr('id')=="avec_code"){
				code="oui";
			}
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code
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
			$('.isotope-item').removeClass('sinscrireEvent');
			$(this).parent().addClass('sinscrireEvent');
			var evenement_id = $('.sinscrireEvent').find('h1 > a').attr('id').split('_')[2];

			if($('.sinscrireEvent').hasClass('en')){
				var la_langue="en";
			}
			else{
				var la_langue="fr";
			}
			var code = "";

			if($(this).attr('id')=="avec_code"){
				code="oui";
			}
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription_multiple.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code
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
			        interneOuvert: dataJSON.interneOuvert,
			        externeOuvert: dataJSON.externeOuvert,
			        toutComplet: dataJSON.toutComplet,
		        };

		        inscription = ich.inscription_form_multiple(inscription_data);

		        $.fancybox( inscription , {
		            title : 'Inscription',
		        });

		        validFancyBox();
		    });
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

function initIsotopeOuvert(){
	var sauv=0;
	if(decodeURIComponent($.urlParam('id')) != "null"){	
		var nextLastRowEventTest;
		var prevFirstRowEventTest;
		
		var monBloc = $('#bloc_'+decodeURIComponent($.urlParam('id')));

		//On remet les éléments du bloc masqué en mode visible et dans l'état où ils étaient avant
		$('.selectedEvent > a').css('display','block');
		$('.selectedEvent > img').css('display','inline-block');
		$('.selectedEvent > p').css('display','block');
		$('.selectedEvent > div').css('display','block');
		$('.selectedEvent > div.triangle_inverse').css('display','none');
		$('.selectedEvent > span').css('display','block');
		$('.selectedEvent').height(sauv);
		sauv = monBloc.height();
		$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
		$('.event').removeClass('selectedEvent');
		

		nextLastRowEventTest = getNextLast(monBloc);
		prevFirstRowEventTest = getPrevFirst(monBloc);
		var hauteurMaxTest=prevFirstRowEventTest.height();

		getHauteurMax(prevFirstRowEventTest, hauteurMaxTest, monBloc);

		monBloc.addClass('selectedEvent');
		
		//clickEvent(monBloc);
		$('.selectedEvent > a').css('display','none');
		$('.selectedEvent > img').css('display','none');
		$('.selectedEvent > p').css('display','none');
		$('.selectedEvent > div').css('display','none');
		$('.selectedEvent > div.triangle_inverse').css('display','block');
		$('.selectedEvent > span').css('display','none');
	}
}

function clickEvent(clickedElement, sauv){
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
			$('.selectedEvent > div.triangle_inverse').css('display','none');
			$('.selectedEvent > span').css('display','block');
			$('.selectedEvent').css('background','#fff');

			$('.selectedEvent').height(sauv);
			//$('.selectedEvent').height($('.selectedEvent').height()-15);

			$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
			$('#liste_evenements').isotope( 'remove', $('.resume') );
			console.log('> suppr resume');

			// on evite le comportement normal du click
			e.preventDefault();
		});

		$('a.sinscrire').click(function(e){
			var code = "";

			if($(this).attr('id')=="avec_code"){
				code="oui";
			}
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code
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

			if($(this).attr('id')=="avec_code"){
				code="oui";
			}
			e.preventDefault();
			$.ajax({
		        url     :"ajax/get_event_infos_inscription_multiple.php",
		        type    : "GET",
		        dataType:'json',
		        data    : {
		            id_event : evenement_id,
		            langue : la_langue,
		            code : code
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
			        interneOuvert: dataJSON.interneOuvert,
		        	externeOuvert: dataJSON.externeOuvert,
		        	toutComplet: dataJSON.toutComplet,
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
	$('a#envoyer, a#renvoyer').click(function(e){
		e.preventDefault();
		$.ajax({
	        url     :"ajax/make_inscription.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_session : $('#id_session').val(),
	            login:$('#login').val(),
	            password:$('#password').val(),
	            titre:$('#titre').val(),
	            date:$('#date').val(),
	            lieu:$('#lieu').val(),
	            casque:$('#inscrit_casque').val()
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
		        alerteInterne: dataJSON.alerteInterne,
		        erreurLDAP: dataJSON.erreurLDAP,
		        inscriptionOK: dataJSON.inscriptionOK,
		        champVide: dataJSON.champVide,
		        completeDerniereMinute: dataJSON.completeDerniereMinute,
	        };

	        validation = ich.validation_form(validation_data);

	        $.fancybox( validation , {
	            title : 'validation de l‘inscription',
	        });

	        validFancyBox();
	    });
	});

	$('a#envoyer_externe, a#renvoyer_externe').click(function(e){
		e.preventDefault();
		$.ajax({
	        url     :"ajax/make_inscription_externe.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            id_session : $('#id_session_externe').val(),
	            nom:$('#nom').val(),
	            prenom:$('#prenom').val(),
	            mail:$('#mail').val(),
	            entreprise:$('#entreprise').val(),
	            fonction:$('#fonction').val(),
	            casque:$('#inscrit_casque').val(),
	            titre:$('#titre_externe').val(),
	            date:$('#date_externe').val(),
	            lieu:$('#lieu_externe').val()
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

	$('a#envoyer_multiple, a#renvoyer_multiple').click(function(e){
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
	        url     :"ajax/make_inscription_multiple.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            sessions : tabsessions,
	            id_evenement : $('#id_evenement').val(),
	            login:$('#login').val(),
	            password:$('#password').val(),
	            titre:$('#titre').val(),
	            date:$('#date').val(),
	            casques : tabcasques
	        }
	    }).done(function (dataJSON) {
			validation_data = {
				title:   dataJSON.titre_bloc,
				id:   dataJSON.evenement_id,
	            titre:   dataJSON.titre,
		        date: dataJSON.date,
		        infos_inscription: dataJSON.infos_inscription,
		        nom: dataJSON.nom,
		        prenom: dataJSON.prenom,
		        sessions: dataJSON.sessions,
		        toutesLesSessions: dataJSON.toutesLesSessions,
		        important: dataJSON.important,
		        erreurLDAP: dataJSON.erreurLDAP,
		        champVide: dataJSON.champVide,
		        erreurChamps: dataJSON.erreurChamps,
		        inscritPartout: dataJSON.inscritPartout,
				tousDerniereMinute: dataJSON.tousDerniereMinute,
		        mention : dataJSON.mention,
	        };

	        validation = ich.validation_form_multiple(validation_data);

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
			if(inputs[i].name == "sessions_externe[]" && inputs[i].checked == true) {
				tabsessions.push(inputs[i].value);
			}
			if(inputs[i].name == "inscrit_casque_externe[]" && inputs[i].checked == true) {
				tabcasques.push(inputs[i].value);
			}
		}

		$.ajax({
	        url     :"ajax/make_inscription_externe_multiple.php",
	        type    : "GET",
	        dataType:'json',
	        data    : {
	            sessions : tabsessions,
	            id_evenement : $('#id_evenement_externe').val(),
	            nom:$('#nom').val(),
	            prenom:$('#prenom').val(),
	            mail:$('#mail').val(),
	            entreprise:$('#entreprise').val(),
	            fonction:$('#fonction').val(),
	            casques : tabcasques,
	            titre:$('#titre_externe').val(),
	            date:$('#date_externe').val(),
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
	$('.selectedEvent > div.triangle_inverse').css('display','none');
	$('.selectedEvent > span').css('display','block');
	$('.selectedEvent').css('background','#fff');
	$('.selectedEvent').height($('.selectedEvent').height()-15);
	$('.event').removeClass('nextLastRowItem').removeClass('selectedEvent');
});

$(window).resizeend({
	onDragEnd : function(){
		console.log('end resize !!!');
	},
	runOnStart : true,
});
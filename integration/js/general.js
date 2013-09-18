// ISOTOPE Centré cf : http://jsfiddle.net/desandro/P6JGY/24/

$(document).ready(function(){			
	var sauv = 0;
	var $container = $('#liste_evenements'), filters = {};
	
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
	    trigger: '#lien_menu_smartphone',
	    openPosition: '270px',
	    afterOpen:function(){
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
	    }
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

		return false;

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

	var evenement_id = clickedElement.find('h1 > a').attr('id').split('_')[2];
	$.ajax({
        url     :"ajax/get_event_infos.php",
        type    : "GET",
        dataType:'json',
        data    : {
            id_event : evenement_id
        }
    }).done(function (dataJSON) {
    	console.log('ok');
    	event_data = {
	        titre:   "Le titre à afficher",
	        langue: "la langue ici du français",
	        organisation : "Le CERI ?",
	        inscription : "inscription obligatoire… ou pas !",
	    };
        console.log(dataJSON);
    });

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
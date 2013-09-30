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
})

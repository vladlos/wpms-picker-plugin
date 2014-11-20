jQuery( document ).ready(function($) {
    var w = $('#st_go').outerWidth();
    $('#st_select').outerWidth(210-w);
});

function goto(){
    var url = jQuery('#st_select').val();
    window.location.replace(url);
}


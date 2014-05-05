$(document).ready(function() {
     $('.showInfo').mouseout(
      function(evt) {
        evt.stopImmediatePropagation();
        evt.preventDefault();
        $('.f-dropdown').removeClass('open').css('left', '-99999px');
      });
      
    showLoader();
      
    $( "a" ).click(function() {
        showLoader();
    });
});

$(window).load( function(){
    killLoader();
});

var showLoader = function(){
     $('body').css('opacity', 0.5).append( "<img src='./images/ajax-loader.gif' id='ajax-loader' width='32px' height='32px' style='position: absolute; left: 49%; top: 49%;' />" );
}

var killLoader = function(){
    $('body').css('opacity', 1);
    $('#ajax-loader').remove();
}

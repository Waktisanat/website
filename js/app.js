$(document).ready(function() {
     $('.showInfo').mouseout(
      function(evt) {
        evt.stopImmediatePropagation();
        evt.preventDefault();
        $('.f-dropdown').removeClass('open').css('left', '-99999px');
      });
});

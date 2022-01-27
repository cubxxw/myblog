(function($) {
    "use strict";

    $( document ).ready(function() {
         $('#_ihafs_show_on2,#_ihafs_show_on3,#_ihafs_show_on4,#_ihafs_exclude_pages,#_ihafs_exclude_posts').attr("disabled", true);

     $( 'span.pro' ).click(function() {
     	$( "#ihafs_dialog" ).dialog({
     		modal: true,
     		minWidth: 500,
     		buttons: {
                Ok: function() {
                  $( this ).dialog( "close" );
                }
            }
     	});
     });
         
        
    });

})(jQuery);
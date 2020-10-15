( function( $ ) {
	"use strict";

	// Remove global notice.
	$( document ).on( 'click', '.ngl-notice .notice-dismiss', function( event ) {
		event.preventDefault();
	
		var key = $( this ).parents( '.ngl-notice' ).attr( 'data-key' );

		$.ajax( {
			type : 'post',
			url : ajaxurl,
			data : 'action=newsletterglue_ajax_remove_notice&key=' + key
		} );

		return false;
	} );

} )( jQuery );
( function( $ ) {
	"use strict";

	$( document ).on( 'change', '#ngl_embed_url', function( event ) {

		var container   = $( this ).parents( '.ngl-embed' ).find( '.ngl-embed-content' );
		var block_id 	= $( this ).parents( '.ngl-embed' ).attr( 'data-block-id' );
		var url 		= $( this ).val();
		var data 		= 'action=newsletterglue_ajax_get_embed&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&url=' + encodeURIComponent( url );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				container.empty();
			},
			success: function( response ) {
				console.log( response );
				if ( response.error ) {
					container.html( response.error );
				}
				if ( response.html ) {
					container.html( response.html );
				}
			}
		} );

	} );

} )( jQuery );
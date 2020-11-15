( function( $ ) {
	"use strict";

	$( document ).on( 'change', '#ngl_embed_url', function( event ) {

		var container   = $( this ).parents( '.ngl-embed' ).find( '.ngl-embed-content' );
		var block_id 	= $( this ).parents( '.ngl-embed' ).attr( 'data-block-id' );
		var url 		= $( this ).val();
		var data 		= 'action=newsletterglue_get_embed&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&url=' + encodeURIComponent( url );

		console.log( data );
		return false;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( response ) {
				if ( response.html ) {
					container.html( response.html );
				}
			}
		} );

	} );

} )( jQuery );
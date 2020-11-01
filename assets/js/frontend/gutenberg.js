( function( $ ) {
	"use strict";

	// Block: Form.
	$( document ).on( 'submit', '.ngl-form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var app 	= $( this ).attr( 'data-app' );
		var data 	= theform.serialize() + '&app=' + app + '&action=newsletterglue_block_form_subscribe&security=' + newsletterglue_gutenberg.ajaxnonce;

		var xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_gutenberg.ajaxurl,
			data : data,
			beforeSend: function() {
				
			},
			success: function( response ) {
				console.log( response );
			}
		} );

		return false;
	} );

} )( jQuery );
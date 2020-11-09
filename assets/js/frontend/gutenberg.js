( function( $ ) {
	"use strict";

	// Block: Form.
	$( document ).on( 'submit', '.ngl-form', function( event ) {
		event.preventDefault();

		var theform  = $( this );
		var app 	 = $( this ).attr( 'data-app' );
		var data 	 = theform.serialize() + '&app=' + app + '&action=newsletterglue_block_form_subscribe&security=' + newsletterglue_gutenberg.ajaxnonce;
		var btn		 = theform.find( 'button' );
		var btn_text = btn.html();

		console.log( data );

		var xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_gutenberg.ajaxurl,
			data : data,
			beforeSend: function() {
				btn.html( newsletterglue_gutenberg.please_wait );
			},
			success: function( response ) {

				console.log( response );

				btn.html( btn_text );

				if ( response.success ) {

					theform.find( '.ngl-message-overlay' ).addClass( 'ngl-show' );
					theform.find( 'input[type=text], input[type=email]' ).val( '' );

					setTimeout( function() {
						theform.find( '.ngl-message-overlay' ).removeClass( 'ngl-show' );
					}, 3000 );

				} else {

				}

			}
		} );

		return false;
	} );

} )( jQuery );
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
				btn.html( newsletterglue_gutenberg.please_wait ).addClass( 'ngl-working' );
			},
			success: function( response ) {

				console.log( response );

				$( '.ngl-form-errors' ).remove();
				btn.html( btn_text ).removeClass( 'ngl-working' );

				if ( response.success ) {

					theform.find( '.ngl-message-overlay' ).addClass( 'ngl-show' );
					theform.find( 'input[type=text], input[type=email]' ).val( '' );

					setTimeout( function() {
						theform.find( '.ngl-message-overlay' ).removeClass( 'ngl-show' );
					}, 3000 );

				} else {

					$( '.ngl-form-container' ).before( '<div class="ngl-form-errors">' + response.message + '</div>' );

				}

			}
		} );

		return false;
	} );

} )( jQuery );
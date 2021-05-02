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

	// Remove upgrade notice.
	$( document ).on( 'click', '.ngl-upgrade-dismiss', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-upgrade-notice' );
		$.ajax( {
			type : 'post',
			url : ajaxurl,
			data : 'action=newsletterglue_ajax_remove_upgrade_notice',
			beforeSend: function() {
				el.fadeOut( 'fast' );
			}
		} );
		return false;
	} );

	// Remove upgrade notice.
	$( document ).on( 'click', '.ngl-notice-dismiss', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-upgrade-notice' );
		$.ajax( {
			type : 'post',
			url : ajaxurl,
			data : 'action=newsletterglue_ajax_remove_editor_notice',
			beforeSend: function() {
				el.fadeOut( 'fast' );
			}
		} );
		return false;
	} );

} )( jQuery );
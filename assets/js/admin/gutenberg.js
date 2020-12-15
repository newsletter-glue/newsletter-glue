( function( $ ) {
	"use strict";

	// Calls ajax function to get embed html.
	function ngl_get_embed( el ) {
		var container   = el.parents( '.ngl-embed' ).find( '.ngl-embed-content' );
		var block_id 	= el.parents( '.ngl-embed' ).attr( 'data-block-id' );
		var url 		= el.val();
		var data 		= 'action=newsletterglue_ajax_get_embed&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&url=' + encodeURIComponent( url );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				container.empty();
			},
			success: function( response ) {
				if ( response.error ) {
					container.html( '<div class="ngl-embed-error">' + response.error + '</div>' );
				}
				if ( response.html ) {
					container.html( response.html );
				}
			}
		} );
	}

	// Function to add article via AJAX.
	function ngl_add_article( el ) {
		var block_id 	= el.attr( 'data-block-id' );
		var date_format = el.attr( 'data-date_format' );
		var thepost		= el.find( '.ngl_article_s' ).attr( 'data-post' );
		var preview		= el.find( '.ngl-article-placeholder' );

		if ( ! thepost ) {
			thepost = el.find( '.ngl_article_s' ).val();
		}

		var data = 'action=newsletterglue_ajax_add_article&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&thepost=' + encodeURIComponent( thepost ) + '&date_format=' + encodeURIComponent( date_format );

		console.log( data );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {

			},
			success: function( response ) {
				console.log( response );
				if ( response.date ) {
					var cloned = preview.clone();
					cloned.html( cloned.html().replace( '{excerpt}', response.excerpt ) );
					cloned.html( cloned.html().replace( '{tags}', response.tags ) );
					cloned.html( cloned.html().replace( '{title}', response.title ) );
					cloned.html( cloned.html().replace( '{permalink}', response.permalink ) );
					cloned.html( cloned.html().replace( '{date}', response.date ) );
					cloned.html( cloned.html().replace( '{featured_image}', response.featured_image ) );
					cloned.appendTo( el ).removeClass( 'ngl-article-placeholder' );
				}
			}
		} );
	}

	// Trigger on URL change.
	$( document ).on( 'change', '#ngl_embed_url', function( event ) {
		ngl_get_embed( $( this ) );
	} );

	// Triggered when enter is clicked.
	$( document ).on( 'keyup', '#ngl_embed_url', function( event ) {
		if ( event.key === 'Enter' || event.keyCode === 13 ) {
			ngl_get_embed( $( this ) );
		}
	} );

	// Trigger embed URL.
	if ( $( '.ngl-embed-url' ).length ) {
		var blockLoaded = false;
		var blockLoadedInterval = setInterval( function() {
			var element = $( '.ngl-embed-url' );
			element.each( function() {
				var el	= $( this );
				var div = $( this ).parents( '.ngl-embed' ).find( '.ngl-embed-content' );
				var err = $( this ).parents( '.ngl-embed' ).find( '.ngl-embed-error' );
				if ( el.val() != '' && ! err.length && ! $.trim( div.html() ).length  ) {
					el.trigger( 'change' );
				}
			} );
		}, 500 );
	}

	// When article embed form is submitted.
	$( document ).on( 'submit', '.ngl-article-add', function( event ) {

		event.preventDefault();

		ngl_add_article( $( this ).parents( '.ngl-articles' ) );

		return false;

	} );

} )( jQuery );
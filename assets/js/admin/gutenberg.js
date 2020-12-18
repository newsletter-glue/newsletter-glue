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

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-articles-add' ).find( 'span.ngl-article-error' ).remove();
			},
			success: function( response ) {
				console.log( response );
				if ( response.error ) {
					el.find( '.ngl-articles-add' ).append( '<span class="ngl-article-error">' + response.error + '</span>' );
					el.find( '.ngl_article_s' ).focus();
				}
				if ( response.date ) {
					el.find( '.ngl_article_s' ).val( '' ).attr( 'data-post', '' );
					var cloned = preview.clone();
					cloned.html( cloned.html().replace( '{excerpt}', response.excerpt ) );
					cloned.html( cloned.html().replace( '{tags}', response.tags ) );
					cloned.html( cloned.html().replace( '{title}', response.title ) );
					cloned.html( cloned.html().replace( '{permalink}', response.permalink ) );
					cloned.html( cloned.html().replace( '{date}', response.date ) );
					cloned.html( cloned.html().replace( '{featured_image}', response.featured_image ) );
					cloned.attr( 'data-post-id', response.post_id );
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

	// Change excerpt dynamically.
	$( document ).on( 'focus', '.ngl-article-excerpt[contenteditable]', function() {
		const $this = $(this);
		$this.data('before', $this.html());
	}).on('blur keyup paste input', '.ngl-article-excerpt[contenteditable]', function() {
		const $this = $(this);
		if ($this.data('before') !== $this.html()) {
			$this.data('before', $this.html());
			$this.trigger('change');
		}
	});

	// When excerpt is changed.
	$( document ).on( 'change', '.ngl-article-excerpt[contenteditable]', function() {

		var post_id = $( this ).parents( '.ngl-article' ).attr( 'data-post-id' );
		var excerpt = $( this ).html();
		var data = 'action=newsletterglue_ajax_update_excerpt&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id + '&excerpt=' + encodeURIComponent( excerpt );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data
		} );

	} );

	// Disable links to edit titles.
	$( document ).on( 'click', '.ngl-article-title a', function( event ) {
		event.preventDefault();
		return false;
	} );

	// Change title dynamically.
	$( document ).on( 'focus', '.ngl-article-title span[contenteditable]', function() {
		const $this = $(this);
		$this.data('before', $this.html());
	}).on('blur keyup paste input', '.ngl-article-title span[contenteditable]', function() {
		const $this = $(this);
		if ($this.data('before') !== $this.html()) {
			$this.data('before', $this.html());
			$this.trigger('change');
		}
	});

	// When title is changed.
	$( document ).on( 'change', '.ngl-article-title span[contenteditable]', function() {

		var post_id = $( this ).parents( '.ngl-article' ).attr( 'data-post-id' );
		var title   = $( this ).html();
		var data = 'action=newsletterglue_ajax_update_title&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id + '&title=' + encodeURIComponent( title );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data
		} );

	} );

	var suggestion = '';

	// Article search.
	$( document ).on( 'blur paste input keyup change', '.ngl_article_s', function() {
		var term = $( this ).val();
		var list = $( this ).parents( '.ngl-article-add' ).find( '.ngl-article-suggest' );
		var data = 'action=newsletterglue_ajax_search_articles&security=' + newsletterglue_params.ajaxnonce + '&term=' + encodeURIComponent( term );

		if ( term.length < 3 ) {
			list.hide().empty();
			suggestion = null;
			return false;
		}

		if ( suggestion == term ) {
			console.log( 'not needed' );
			return false;
		}

		console.log( 'searching' );

		suggestion = term;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( response ) {
				if ( response.html ) {
					list.show().html( response.html );
				} else {
					list.hide().empty();
				}
			}
		} );

	} );

	// When a suggestion is clicked.
	$( document ).on( 'click', '.ngl-article-suggest li a', function( event ) {
		event.preventDefault();

		var el = $( this ).parents( '.ngl-article-add' );
		var post_id = $( this ).attr( 'data-post-id' );

		el.find( '.ngl_article_s' ).val( $( this ).html() ).attr( 'data-post', post_id );

		el.find( '.ngl-article-suggest' ).empty().hide();

		el.trigger( 'submit' );

		return false;
	} );

} )( jQuery );
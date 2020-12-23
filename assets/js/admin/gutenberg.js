( function( $ ) {
	"use strict";

	// Reorder keys.
	function ngl_reorder_keys( wrap ) {

		wrap.find( '.ngl-article-list-item' ).each( function() {
			var elements = wrap.find( '.ngl-article-list-item' );
			var theindex = elements.length - elements.index( $( this ) );
			$( this ).attr( 'data-key', theindex );
		} );

		wrap.find( '.ngl-articles-wrap .ngl-article:visible' ).each( function() {
			var elements = wrap.find( '.ngl-article:visible' );
			var theindex = elements.length - elements.index( $( this ) );
			$( this ).attr( 'data-key', theindex );
		} );

		var block_id 	= wrap.attr( 'data-block-id' );
		var keys        = [];
		var values      = [];

		wrap.find( '.ngl-article-list-item' ).each( function() {
			var key = $( this ).attr( 'data-key' );
			var id  = $( this ).attr( 'data-post-id' );
			keys.push( key );
			values.push( id );
		} );

		var data = 'action=newsletterglue_ajax_order_articles&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&keys=' + keys + '&values=' + values;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data
		} );

	}

	// Is valid URL.
	function is_ngl_valid_url( string ) {
		  let url;
		  
		  try {
			url = new URL(string);
		  } catch (_) {
			return false;  
		  }

		  return url.protocol === "http:" || url.protocol === "https:";
	}

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

		var wrap = el.find( '.ngl-article-list-wrap' );
		if ( wrap.find( '.ngl-article-list-item' ).length ) {
			var key = parseInt( wrap.find( '.ngl-article-list-item' ).first().attr( 'data-key' ) ) + 1;
		} else {
			var key = 1;
		}

		var data = 'action=newsletterglue_ajax_add_article&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + key + '&thepost=' + encodeURIComponent( thepost ) + '&date_format=' + encodeURIComponent( date_format );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-articles-add .ngl-article-status' ).removeClass( 'ngl-article-error ngl-article-success' ).empty();
			},
			success: function( response ) {
				if ( response.error ) {
					el.find( '.ngl-articles-add .ngl-article-status' ).addClass( 'ngl-article-error' ).html( response.error );
					el.find( '.ngl_article_s' ).focus();
				}
				if ( response.key ) {

					el.find( '.ngl-articles-add .ngl-article-status' ).addClass( 'ngl-article-success' ).html( response.success );
					el.find( '.ngl_article_s' ).val( '' ).attr( 'data-post', '' );

					var cloned = preview.clone();
					cloned.html( cloned.html().replace( '{excerpt}', response.excerpt ) );
					cloned.html( cloned.html().replace( '{tags}', response.tags ) );
					cloned.html( cloned.html().replace( '{title}', response.title ) );
					cloned.html( cloned.html().replace( '{permalink}', response.permalink ) );
					cloned.html( cloned.html().replace( '{date}', response.date ) );
					cloned.html( cloned.html().replace( '{featured_image}', response.featured_image ) );
					cloned.attr( 'data-post-id', response.post_id );
					cloned.attr( 'data-key', response.key );
					cloned.find( '.ngl-article-featured a' ).attr( 'href', response.permalink );
					cloned.prependTo( el.find( '.ngl-articles-wrap' ) ).removeClass( 'ngl-article-placeholder' );

					if ( el.find( '.ngl-article-list-empty' ).length ) {
						el.find( '.ngl-article-list-empty' ).hide();
					}

					el.find( '.ngl-article-list-wrap' ).prepend( response.item );
					el.find( '.ngl-article-list-empty' ).remove();

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

		var wrap	= $( this ).parents( '.ngl-articles' );
		var post_id = $( this ).parents( '.ngl-article' ).attr( 'data-post-id' );
		var key 	= $( this ).parents( '.ngl-article' ).attr( 'data-key' );
		var title   = $( this ).html();
		var data 	= 'action=newsletterglue_ajax_update_title&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id + '&title=' + encodeURIComponent( title );

		wrap.find( '.ngl-article-list-item[data-key=' + key + '] .ngl-article-list-title' ).html( title );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data
		} );

	} );

	var suggestion = '';

	// Article search.
	$( document ).on( 'blur input keyup change', '.ngl_article_s', function() {
		var term = $( this ).val();
		var list = $( this ).parents( '.ngl-article-add' ).find( '.ngl-article-suggest' );
		var data = 'action=newsletterglue_ajax_search_articles&security=' + newsletterglue_params.ajaxnonce + '&term=' + encodeURIComponent( term );

		if ( is_ngl_valid_url( term ) ) {
			list.hide().empty();
			suggestion = null;
			return false;
		}

		if ( term.length < 3 ) {
			list.hide().empty();
			suggestion = null;
			return false;
		}

		if ( suggestion == term ) {
			return false;
		}

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

	// When a list head is clicked.
	$( document ).on( 'click', '.ngl-article-list-head', function( event ) {

		event.preventDefault();

		var wrap = $( this ).parent().find( '.ngl-article-list-wrap' );

		if ( wrap.is( ':visible' ) ) {
			$( this ).find( 'span' ).html( 'expand_less' );
			wrap.hide();
		} else {
			$( this ).find( 'span' ).html( 'expand_more' );
			wrap.show();
		}

		return false;

	} );

	// Remove an article.
	$( document ).on( 'click', '.ngl-article-list-red', function( event ) {
		event.preventDefault();

		var wrap		= $( this ).parents( '.ngl-article-list-wrap' );
		var item 		= $( this ).parents( '.ngl-article-list-item' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );

		var data = 'action=newsletterglue_ajax_remove_article&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				item.remove();
				$( '.ngl-article[data-key=' + key + ']' ).remove();
				if ( wrap.find( '.ngl-article-list-item' ).length == 0 ) {
					wrap.append( '<div class="ngl-article-list-empty">' + newsletterglue_params.no_posts_found + '</div>' );
				} else {
					wrap.find( '.ngl-article-list-empty' ).remove();
				}
			},
			success: function( response ) {

			}
		} );

		return false;
	} );

	// Move article down.
	$( document ).on( 'click', '.ngl-article-list-move-down a', function( event ) {
		event.preventDefault();
		var ajax = false;
		var wrap = $( this ).parents( '.ngl-articles' );
		var item = $( this ).parents( '.ngl-article-list-item' );
		var key  = item.attr( 'data-key' );
		var post = wrap.find( '.ngl-article[data-key=' + key + ']' );
		var next = item.next();
		var next_post = post.next( '.ngl-article' );
		if ( next.length !== 0 ) {
			item.insertAfter( next );
			post.insertAfter( next_post );
			ngl_reorder_keys( wrap );
		}

		return false;
	} );

	// Move article up.
	$( document ).on( 'click', '.ngl-article-list-move-up a', function( event ) {
		event.preventDefault();
		var ajax = false;
		var wrap = $( this ).parents( '.ngl-articles' );
		var item = $( this ).parents( '.ngl-article-list-item' );
		var key  = item.attr( 'data-key' );
		var post = wrap.find( '.ngl-article[data-key=' + key + ']' );
		var prev = item.prev();
		var prev_post = post.prev( '.ngl-article' );
		if ( prev.length !== 0 ) {
			item.insertBefore( prev );
			post.insertBefore( prev_post );
			ngl_reorder_keys( wrap );
		}

		return false;
	} );

} )( jQuery );
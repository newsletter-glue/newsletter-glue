( function( $ ) {
	"use strict";

	// Function to trigger media library.
	function ngl_change_image( el ) {

		var image_frame;

		if ( image_frame ) {
			image_frame.open();
		}

		image_frame = wp.media( {
			title: newsletterglue_params.select_image,
			multiple : false,
			library : {
				type : 'image',
			}
		} );

		image_frame.on( 'close', function() {
			var selection =  image_frame.state().get( 'selection' );
			var gallery_ids = new Array();
			var my_index = 0;
			selection.each( function( attachment ) {
				gallery_ids[my_index] = attachment[ 'id' ];
				my_index++;
			} );
			var ids = gallery_ids.join( ',' );
			el.attr( 'data-attachment', ids );
			ngl_change_image_ajax( el, el.attr( 'data-post-id' ), ids );
		} );

		image_frame.on( 'open', function() {
			var selection = image_frame.state().get( 'selection' );
			var ids = el.attr( 'data-attachment' ) ? el.attr( 'data-attachment' ).split( ',' ) : '';
			if ( ids ) {
				ids.forEach( function( id ) {
					if ( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					}
				} );
			}
		} );

		image_frame.open();

	}

	// Ajax request to refresh the image preview
	function ngl_change_image_ajax( el, key, ids ) {

		var current = el.find( '.ngl-article-featured img' ).prop( 'src' );

		var data = {
			action: 'newsletterglue_save_article_image',
			key: key,
			ids: ids,
			security:  newsletterglue_params.ajaxnonce
		};

		$.ajax( {
			type	: 'post',
			url		: newsletterglue_params.ajaxurl,
			data	: data,
			success : function( response ) {
				if ( response ) {
					el.find( '.ngl-article-featured img' ).prop( 'src', response.data.url );
					el.attr( 'data-attachment', response.data.id );
					el.find( '.ngl-article-featured-edit i.trash' ).show();
				} else {
					el.find( '.ngl-article-featured img' ).prop( 'src', el.find( '.ngl-article-featured img' ).attr( 'data-original-src' ) );
					el.attr( 'data-attachment', '' );
					el.find( '.ngl-article-featured-edit i.trash' ).hide();
				}
			}
		} );

	}

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
					cloned.html( cloned.html().replace( '{labels}', response.labels ) );
					cloned.html( cloned.html().replace( '{title}', response.title ) );
					cloned.html( cloned.html().replace( '{permalink}', response.permalink ) );
					cloned.html( cloned.html().replace( '{date}', response.date ) );
					cloned.html( cloned.html().replace( '{featured_image}', response.featured_image ) );
					cloned.attr( 'data-post-id', response.post_id );
					cloned.attr( 'data-key', response.key );
					cloned.find( '.ngl-article-featured a' ).attr( 'href', response.permalink );
					cloned.find( '.ngl-article-featured img' ).attr( 'data-original-src', response.featured_image );
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

	// When article featured image is hovered.
	$( document ).on( 'mouseenter', '.ngl-article-featured', function() {
		var edit = $( this ).find( '.ngl-article-featured-edit' );
		var img  = $( this ).find( 'img' );
		if ( img.attr( 'src' ) == img.attr( 'data-original-src' ) ) {
			edit.find( 'i.trash' ).hide();
		} else {
			edit.find( 'i.trash' ).show();
		}
	} );

	// Trigger media upload.
	$( document ).on( 'click', '.ngl-article-featured-edit i.image.outline.icon', function( event ) {

		event.preventDefault();

		ngl_change_image( $( this ).parents( '.ngl-article' ) );

		return false;

	} );

	// Trigger media remove.
	$( document ).on( 'click', '.ngl-article-featured-edit i.trash.outline.icon', function( event ) {

		event.preventDefault();

		ngl_change_image_ajax( $( this ).parents( '.ngl-article' ), $( this ).parents( '.ngl-article' ).attr( 'data-post-id' ), '' );

		return false;

	} );

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

	// Change labels dynamically.
	$( document ).on( 'focus', '.ngl-article-labels[contenteditable]', function() {
		const $this = $(this);
		$this.data('before', $this.html());
		if ( $this.html() == newsletterglue_params.write_labels ) {
			$this.html( '' );
		}
	}).on('blur keyup paste input', '.ngl-article-labels[contenteditable]', function() {
		const $this = $(this);
		if ($this.data('before') !== $this.html()) {
			$this.data('before', $this.html());
			$this.trigger('change');
		}
	}).on( 'blur', '.ngl-article-labels[contenteditable]', function() {
		const $this = $(this);
		if ( $this.html() == '' ) {
			$this.html( newsletterglue_params.write_labels );
		}
	} );

	// When labels is changed.
	$( document ).on( 'change', '.ngl-article-labels[contenteditable]', function() {

		var post_id  = $( this ).parents( '.ngl-article' ).attr( 'data-post-id' );
		var labels   = $( this ).html();
		var data    = 'action=newsletterglue_ajax_update_labels&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id + '&labels=' + encodeURIComponent( labels );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data
		} );

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

	// Refresh an external URL.
	$( document ).on( 'click', '.ngl-article-list-refresh', function( event ) {
		event.preventDefault();

		var el			= $( this );
		var state		= $( this ).html();
		var wrap		= $( this ).parents( '.ngl-article-list-wrap' );
		var item 		= $( this ).parents( '.ngl-article-list-item' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );
		var thepost		= item.attr( 'data-post-id' );
		var post		= $( this ).parents( '.ngl-articles' ).find( '.ngl-article[data-key=' + key + ']' );
		var data 		= 'action=newsletterglue_ajax_clear_cache&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key ) + '&thepost=' + encodeURIComponent( thepost );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'ngl-in-progress' ).html( newsletterglue_params.refreshing_html );
				post.addClass( 'ngl-in-progress' );
			},
			success: function( response ) {
				post.removeClass( 'ngl-in-progress' );
				if ( response ) {
					item.replaceWith( response.item );
					if ( response.title ) {
						post.find( '.ngl-article-title span' ).html( response.title );
					}
					if ( response.featured_image ) {
						post.find( '.ngl-article-featured img' ).attr( 'src', response.featured_image );
					}
					if ( response.excerpt ) {
						post.find( '.ngl-article-excerpt' ).html( response.excerpt );
					}
					var new_item = wrap.find( '.ngl-article-list-item[data-key=' + key + '] .ngl-article-list-refresh' );
					new_item.addClass( 'ngl-refreshed' ).html( newsletterglue_params.refreshed_html );
					setTimeout( function() {
						new_item.removeClass( 'ngl-refreshed' ).html( state );
					}, 2000 );
				}
			}
		} );

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

	// When URL is clicked.
	$( document ).on( 'click', '.ngl-article-list-url-edit', function() {
		$( this ).select();
	} );

	// Change URL dynamically.
	$( document ).on( 'focus', '.ngl-article-list-url-edit[contenteditable]', function() {
		const $this = $(this);
		$this.data('before', $this.html());
	}).on('blur keyup paste input', '.ngl-article-list-url-edit[contenteditable]', function() {
		const $this = $(this);
		if ($this.data('before') !== $this.html()) {
			$this.data('before', $this.html());
			$this.trigger('change');
		}
	});

	// When URL is changed.
	$( document ).on( 'change', '.ngl-article-list-url-edit[contenteditable]', function() {
		
		$( this ).parents( '.ngl-article-list-item' ).find( '.ngl-article-save-state span' ).css( { 'display' : 'none' } );
		$( this ).parents( '.ngl-article-list-item' ).find( '.ngl-article-save' ).css( { 'display' : 'inline-flex' } );

	} );

	// When save is clicked.
	$( document ).on( 'click', '.ngl-article-save', function() {

		var $this       = $( this );
		var urldiv      = $( this ).parents( '.ngl-article-list-url' );
		var url 		= $( this ).parents( '.ngl-article-list-url' ).find( 'div' ).html();
		var wrap		= $( this ).parents( '.ngl-article-list-wrap' );
		var item 		= $( this ).parents( '.ngl-article-list-item' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );
		var cloned		= $( this ).parents( '.ngl-articles' ).find( '.ngl-article[data-key=' + key + ']' );
		var date_format = $( this ).parents( '.ngl-articles' ).attr( 'data-date_format' );
		var data 		= 'action=newsletterglue_ajax_update_url&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key ) + '&url=' + encodeURIComponent( url ) + '&date_format=' + encodeURIComponent( date_format );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$this.css( { 'display' : 'none' } );
				urldiv.find( '.ngl-article-saving' ).css( { 'display' : 'inline-flex' } );
				cloned.addClass( 'ngl-in-progress' );
			},
			success: function( response ) {
				cloned.removeClass( 'ngl-in-progress' );
				if ( ! response.success ) {
					urldiv.find( '.ngl-article-save-state span' ).css( { 'display' : 'none' } );
					urldiv.find( '.ngl-article-unsaved' ).css( { 'display' : 'inline-flex' } );
				} else {
					urldiv.find( '.ngl-article-save-state span' ).css( { 'display' : 'none' } );
					urldiv.find( '.ngl-article-saved' ).css( { 'display' : 'inline-flex' } );

					cloned.find( '.ngl-article-title span' ).html( response.data.title );
					cloned.find( '.ngl-article-featured img' ).attr( 'src', response.data.featured_image );
					cloned.find( '.ngl-article-excerpt' ).html( response.data.excerpt );
					cloned.find( '.ngl-article-labels' ).html( response.data.labels );
					cloned.find( '.ngl-article-date' ).html( response.data.date );
					cloned.attr( 'data-post-id', response.data.post_id );
					cloned.attr( 'data-key', response.data.key );
					cloned.find( '.ngl-article-title a' ).attr( 'href', response.data.permalink );
					cloned.find( '.ngl-article-featured a' ).attr( 'href', response.data.permalink );
					cloned.find( '.ngl-article-featured img' ).attr( 'data-original-src', response.data.featured_image );
					item.replaceWith( response.data.item );

				}
			},
			error: function() {
				urldiv.find( '.ngl-article-save-state span' ).css( { 'display' : 'none' } );
				urldiv.find( '.ngl-article-unsaved' ).css( { 'display' : 'inline-flex' } );
			}
		} );

	} );

} )( jQuery );
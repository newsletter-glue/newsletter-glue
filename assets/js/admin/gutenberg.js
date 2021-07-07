jQuery.fn.selectText = function(){
   var doc = document;
   var element = this[0];
   if (doc.body.createTextRange) {
       var range = document.body.createTextRange();
       range.moveToElementText(element);
       range.select();
   } else if (window.getSelection) {
       var selection = window.getSelection();        
       var range = document.createRange();
       range.selectNodeContents(element);
       selection.removeAllRanges();
       selection.addRange(range);
   }
};

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
	function ngl_reorder_keys( wrap, is_dup = 0 ) {

		wrap.find( '.ngl-articles-wrap .ngl-article:visible' ).each( function() {
			var elements = wrap.find( '.ngl-article:visible' );
			var theindex = elements.length - elements.index( $( this ) );
			$( this ).attr( 'data-key', theindex );
		} );

		var block_id 	= wrap.attr( 'data-block-id' );
		var keys        = [];
		var values      = [];

		wrap.find( '.ngl-article' ).each( function() {
			var key = $( this ).attr( 'data-key' );
			var id  = $( this ).attr( 'data-post-id' );
			keys.push( key );
			values.push( id );
		} );

		var data = 'action=newsletterglue_ajax_order_articles&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&keys=' + keys + '&values=' + values + '&is_dup=' + is_dup;

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

		var wrap = el.find( '.ngl-articles-wrap' );
		if ( wrap.find( '.ngl-article' ).length ) {
			var key = parseInt( wrap.find( '.ngl-article' ).first().attr( 'data-key' ) ) + 1;
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

	// When article featured image is clicked.
	$( document ).on( 'click', '.ngl-article-featured a', function( event ) {
		event.preventDefault();
		return false;
	} );

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
	$( document ).on( 'click', '.ngl-article-add button', function( event ) {

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

		el.find( 'button' ).trigger( 'click' );

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
		var wrap		= $( this ).parents( '.ngl-articles' );
		var item 		= $( this ).parents( '.ngl-article' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );
		var thepost		= item.attr( 'data-post-id' );
		var data 		= 'action=newsletterglue_ajax_clear_cache&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key ) + '&thepost=' + encodeURIComponent( thepost );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'ngl-in-progress ngl-is-toggled' );
				item.find( '.ngl-article-list-link' ).removeClass( 'ngl-is-toggled' );
				item.find( '.ngl-article-list-url-edit' ).removeClass( 'ngl-show-state' );
				item.find( '.ngl-article-state-refreshing' ).addClass( 'ngl-show-state' );
				item.find( '.ngl-article-overlay' ).show();
			},
			success: function( response ) {
				el.removeClass( 'ngl-in-progress' ).addClass( 'ngl-in-done' );
				el.find( 'i' ).removeClass().addClass( 'check icon' );
				item.find( '.ngl-article-state-refreshing' ).html( newsletterglue_params.refreshed_html ).addClass( 'ngl-refreshed' );
				setTimeout( function() {
					item.find( '.ngl-article-overlay' ).hide();
					item.find( '.ngl-article-state-refreshing' ).html( newsletterglue_params.refreshing_html ).removeClass( 'ngl-show-state ngl-refreshed' );
					el.removeClass( 'ngl-in-done ngl-is-toggled' );
					el.find( 'i' ).removeClass().addClass( 'sync icon' );
				}, 1000 );
				if ( response ) {
					if ( response.title ) {
						item.find( '.ngl-article-title span' ).html( response.title );
					}
					if ( response.featured_image ) {
						item.find( '.ngl-article-featured img' ).attr( 'src', response.featured_image );
					}
					if ( response.excerpt ) {
						item.find( '.ngl-article-excerpt' ).html( response.excerpt );
					}
				}
			}
		} );

		return false;
	} );

	// Overlay clicked.
	$( document ).on( 'click', '.ngl-article-overlay', function( event ) {
		var item  = $( this ).parents( '.ngl-article' );
		var remove_state = $( this ).parents( '.ngl-article' ).find( '.ngl-article-state-remove' );
		var update_state = $( this ).parents( '.ngl-article' ).find( '.ngl-article-list-url-edit' );
		if ( remove_state.hasClass( 'ngl-show-state' ) ) {
			item.find( '.ngl-article-list-delete' ).trigger( 'click' );
		} else if ( update_state.hasClass( 'ngl-show-state' ) ) {
			item.find( '.ngl-article-list-link' ).trigger( 'click' );
		}
	} );

	// Remove an article.
	$( document ).on( 'click', '.ngl-article-state-remove', function( event ) {
		event.preventDefault();

		if ( ! $( this ).hasClass( 'ngl-show-state' ) ) {
			return false;
		}

		var item 		= $( this ).parents( '.ngl-article' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );

		var data = 'action=newsletterglue_ajax_remove_article&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				item.remove();
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
		var item = $( this ).parents( '.ngl-article' );
		var key  = item.attr( 'data-key' );
		var next = item.next();
		if ( next.length !== 0 ) {
			item.insertAfter( next );
			ngl_reorder_keys( wrap );
		}
		return false;
	} );

	// Move article up.
	$( document ).on( 'click', '.ngl-article-list-move-up a', function( event ) {
		event.preventDefault();
		var ajax = false;
		var wrap = $( this ).parents( '.ngl-articles' );
		var item = $( this ).parents( '.ngl-article' );
		var key  = item.attr( 'data-key' );
		var prev = item.prev();
		if ( prev.length !== 0 ) {
			item.insertBefore( prev );
			ngl_reorder_keys( wrap );
		}
		return false;
	} );

	// Change URL dynamically.
	$( document ).on( 'focus', '.ngl-article-list-url-edit span[contenteditable]', function() {
		const $this = $(this);
		$this.data('before', $this.html());
	}).on('blur keyup paste input', '.ngl-article-list-url-edit span[contenteditable]', function() {
		const $this = $(this);
		if ($this.data('before') !== $this.html()) {
			$this.data('before', $this.html());
			$this.trigger('change');
		}
	});

	// When URL is changed.
	$( document ).on( 'click', '.ngl-article-list-url-edit a', function( event ) {

		event.preventDefault();

		var $this       = $( this );
		var url 		= $( this ).parent().find( 'span' ).html();
		var item 		= $( this ).parents( '.ngl-article' );
		var block_id 	= $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
		var key			= item.attr( 'data-key' );
		var date_format = $( this ).parents( '.ngl-articles' ).attr( 'data-date_format' );
		var data 		= 'action=newsletterglue_ajax_update_url&security=' + newsletterglue_params.ajaxnonce + '&block_id=' + block_id + '&key=' + encodeURIComponent( key ) + '&url=' + encodeURIComponent( url ) + '&date_format=' + encodeURIComponent( date_format );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {

			},
			success: function( response ) {
				item.find( '.ngl-article-overlay' ).hide();
				item.find( '.ngl-article-list-url-edit' ).removeClass( 'ngl-show-state' );
				item.find( '.ngl-article-list-link' ).removeClass( 'ngl-is-toggled' );
				if ( ! response.success ) {

				} else {

					item.find( '.ngl-article-title span' ).html( response.data.title );
					item.find( '.ngl-article-featured img' ).attr( 'src', response.data.featured_image );
					item.find( '.ngl-article-excerpt' ).html( response.data.excerpt );
					item.find( '.ngl-article-labels' ).html( response.data.labels );
					item.find( '.ngl-article-date' ).html( response.data.date );
					item.attr( 'data-post-id', response.data.post_id );
					item.attr( 'data-key', response.data.key );
					item.find( '.ngl-article-title a' ).attr( 'href', response.data.permalink );
					item.find( '.ngl-article-featured a' ).attr( 'href', response.data.permalink );
					item.find( '.ngl-article-featured img' ).attr( 'data-original-src', response.data.featured_image );

				}
			},
			error: function() {

			}
		} );

		return false;

	} );

	// When URL is changed.
	$( document ).on( 'change', '.ngl-article-list-url-edit span[contenteditable]', function() {

		$( this ).parent().find( 'a' ).show();

	} );

	// When mouse leaves article item.
	$( document ).on( 'mouseleave', '.ngl-article', function( event ) {
		if ( $( this ).find( '.ngl-article-state-remove' ).hasClass( 'ngl-show-state' ) ) {

		}
	} );

	// When the update url icon is clicked first time.
	$( document ).on( 'click', '.ngl-article-list-link', function( event ) {
		event.preventDefault();

		var item 		= $( this ).parents( '.ngl-article' );
		var state		= item.find( '.ngl-article-list-url-edit' );

		if ( state.hasClass( 'ngl-show-state' ) ) {
			item.find( '.ngl-article-overlay' ).hide();
			state.removeClass( 'ngl-show-state' );
			$( this ).removeClass( 'ngl-is-toggled' );
		} else {
			item.find( '.ngl-article-overlay' ).show();
			state.addClass( 'ngl-show-state' );
			state.find( 'span' ).selectText();
			$( this ).addClass( 'ngl-is-toggled' );
		}

		return false;
	} );

	// When the delete icon is clicked first time.
	$( document ).on( 'click', '.ngl-article-list-delete', function( event ) {
		event.preventDefault();

		var item 		= $( this ).parents( '.ngl-article' );
		var state		= item.find( '.ngl-article-state-remove' );

		if ( state.hasClass( 'ngl-show-state' ) ) {
			item.find( '.ngl-article-overlay' ).hide();
			state.removeClass( 'ngl-show-state' );
			item.find( '.ngl-article-list-move, .ngl-article-list-refresh, .ngl-article-list-link' ).show();
			$( this ).removeClass( 'ngl-is-toggled' );
		} else {
			item.find( '.ngl-article-overlay' ).show();
			state.addClass( 'ngl-show-state' );
			item.find( '.ngl-article-list-move, .ngl-article-list-refresh, .ngl-article-list-link' ).hide();
			$( this ).addClass( 'ngl-is-toggled' );
		}

		return false;
	} );

	// Update embeds interval.
	function updateEmbeds() {
		var inp = $( '.ngl_article_s' );
		if ( inp.length == 0 ) {
			return false;
		}
		var theid = ''
		var count = 0;
		inp.each( function() {

			var curr_block_id = $( this ).parents( 'div[data-type="newsletterglue/article"]' ).attr( 'data-block' );
			var orig_block_id = $( this ).parents( '.ngl-articles' ).attr( 'data-block-id' );
			var thearticles = $( this ).parents( '.ngl-articles' );
			var $theblock = wp.data.select( 'core/block-editor' ).getBlock( curr_block_id );
			var scope = $theblock && $theblock.attributes.scope ? $theblock.attributes.scope : 'regular';
			if ( scope == 'pattern' && typenow && typenow != 'ngl_pattern' ) {
				var new_id = curr_block_id.replace( /-/g, '' );
				$theblock.attributes.block_id = new_id;
				$theblock.attributes.scope = 'regular';
				thearticles.attr( 'data-block-id', new_id );
				wp.data.dispatch( 'core/block-editor' ).updateBlock( $theblock.clientId, $theblock.attributes );
				ngl_reorder_keys( thearticles, orig_block_id );
				thearticles.parents( 'div[data-type="newsletterglue/article"]' ).find( '.ngl-ajax-block-id' ).html( 'ID: ' + new_id );
				thearticles.parents( 'div[data-type="newsletterglue/article"]' ).find( '.ngl-ajax-block-scope' ).html( 'regular' );
			}

			var itms = $( this ).parents( '.ngl-articles' );
			var theid = itms.attr( 'data-block-id' );
			var count = $( '.ngl-articles[data-block-id="' + theid + '"]' ).length;
			if ( count > 1 ) {
				var original_id = $( '.ngl-articles[data-block-id="' + theid + '"]:first' );
				var to_update   = $( '.ngl-articles[data-block-id="' + theid + '"]:last' );
				var to_update_id = to_update.parents( 'div[data-type="newsletterglue/article"]' ).attr( 'data-block' );
				var $block = wp.data.select( 'core/block-editor' ).getBlock( to_update_id );
				var new_instance_id = to_update_id.replace( /-/g, '' );
				$block.attributes.block_id = new_instance_id;
				to_update.attr( 'data-block-id', new_instance_id );
				wp.data.dispatch( 'core/block-editor' ).updateBlock( $block.clientId, $block.attributes );
				ngl_reorder_keys( to_update, original_id.attr( 'data-block-id' ) );
				to_update.parents( 'div[data-type="newsletterglue/article"]' ).find( '.ngl-ajax-block-id' ).html( 'ID: ' + new_instance_id );
			}

		} );
	}
	var update_embed_handler = setInterval( updateEmbeds, 100 );

	// Fix blocks.
	function fixBlocks() {
		if ( $( '.block-editor-writing-flow [data-block].has-warning' ).length ) {
			$( '.block-editor-writing-flow [data-block].has-warning' ).each( function() {
				$( this ).find( 'button.components-button.is-primary' ).trigger( 'click' );
			} );
		}
		if ( $( '.ngl-metadata' ).length ) {
			$( '.ngl-metadata' ).each( function() {
				var last = $( this ).children().last();
				if ( last.hasClass( 'ngl-metadata-sep' ) ) {
					last.hide();
				}
			} );
		}
	}
	var fix_blocks = setInterval( fixBlocks, 100 );

} )( jQuery );
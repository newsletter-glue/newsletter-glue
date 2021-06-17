( function( $ ) {
	"use strict";

	// Close the pop-over.
	function ngl_close_popover() {
		var pop = $( '.ngl-gutenberg-pop' );
		pop.removeClass( 'is-open' );
		pop.css( {
			left: 0,
			top: 0
		} );
		pop.find( '.ngl-fallback' ).hide();
		pop.find( 'button.ngl-submenu-item' ).removeClass( 'is-active' );
	}

	// Fix scroll.
	setTimeout( function() {
		$( '.interface-interface-skeleton__content' ).on( 'scroll', function() {
			var pop = $( '.ngl-gutenberg-pop' );
			if ( pop.hasClass( 'is-open' ) ) {
				if ( $( '.block-editor-block-toolbar' ).hasClass( 'is-showing-movers' ) ) {
					var topgap = $( '.ngl-toolbar-mergetags' ).offset().top + $( '.ngl-toolbar-mergetags' ).height() + 24;
					var leftgap = $( '.block-editor-block-toolbar' ).offset().left;
				} else {
					var topgap = $( '.block-editor-block-toolbar' ).offset().top + $( '.ngl-toolbar-mergetags' ).height() + 24;
					var leftgap = $( '.block-editor-block-toolbar' ).offset().left;
				}
				pop.css( {
					top: topgap + 'px',
				} );
			}
		} );
	}, 1000 );

	// Open merge tags list.
	$( document ).on( 'click', '.ngl-toolbar-mergetags', function() {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( this ).parents( '.block-editor-block-toolbar' ).hasClass( 'is-showing-movers' ) ) {
			var topgap = $( this ).offset().top + $( this ).height() + 24;
			var leftgap = $( this ).offset().left;
		} else {
			var topgap = $( this ).parents( '.block-editor-block-toolbar' ).offset().top + $( this ).height() + 24;
			var leftgap = $( this ).offset().left;
		}
		if ( ! pop.hasClass( 'is-open' ) ) {
			pop.addClass( 'is-open' );
			pop.css( {
				left: leftgap + 'px',
				top: topgap + 'px',
			} );
		} else {
			ngl_close_popover();
		}
	} );

	// When clicked in body.
	$( 'body' ).on( 'click', function(event) {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( event.target ).parents( '.ngl-toolbar-mergetags' ).length || $( event.target ).hasClass( 'ngl-toolbar-mergetags' ) ) {
			return true;
		}
		if( ! $( event.target ).is( '.ngl-gutenberg-pop' ) && ! $( event.target ).parents( '.ngl-gutenberg-pop' ).length ){
			ngl_close_popover();
		}
	} );

	// Open sub menu.
	$( document ).on( 'click', '.ngl-submenu-trigger', function() {
		if ( ! $( this ).hasClass( 'is-triggered' ) ) {
			$( this ).parents( 'div[role=group]' ).find( '.ngl-submenu-item' ).css( { display: 'inline-flex' } );
			$( this ).parents( 'div[role=group]' ).find( '.ngl-outside-helper' ).css( { display: 'block' } );
			$( this ).addClass( 'is-triggered' );
			$( this ).find( 'svg' ).html( '<path d="M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z"></path>' );
		} else {
			$( this ).parents( 'div[role=group]' ).find( '.ngl-submenu-item' ).css( { display: 'none' } );
			$( this ).parents( 'div[role=group]' ).find( '.ngl-outside-helper' ).css( { display: 'none' } );
			$( this ).removeClass( 'is-triggered' );
			$( this ).find( 'svg' ).html( '<path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>' );
			$( '.ngl-fallback' ).hide();
			$( '.ngl-submenu-item' ).removeClass( 'is-active' );
		}
	} );

	// Add the tag.
	$( document ).on( 'click', '.ngl-submenu-item', function() {

		var tag 	= $( this ).attr( 'data-ngl-tag' );
		var tag_id 	= $( this ).attr( 'data-tag-id' );
		var fallback = $( 'input[type=text]#__fallback_' + tag_id );
		var $block = wp.data.select( 'core/block-editor' ).getSelectedBlock();
		var btn = $( this );
		var uniqid = Math.round( new Date().getTime() + ( Math.random() * 100 ) );

		if ( btn.attr( 'data-require-fb' ) & btn.attr( 'data-require-fb' ) == 1 && fallback.val() == '' ) {
			btn.find( '.ngl-gutenberg-icon' ).trigger( 'click' );
			fallback.addClass( 'is-mandatory' );
			return false;
		}

		ngl_close_popover();

		// Make tag markup.
		if ( tag_id == 'unsubscribe_link' || tag_id == 'webversion' || tag_id == 'blog_post' || tag_id == 'update_preferences' ) {
			var link_text = fallback.val() ? fallback.val() : $( this ).attr( 'data-default-link-text' );
			tag = '<a href="' + tag + '">' + link_text + '</a><i class="ngl-tag-spacer" id="ngl-tag-spacer-' + uniqid + '">&nbsp;</i>';
		} else {
			if ( fallback.length ) {
				tag = tag.replace( ' }}', ',fallback=' + fallback.val() + ' }}' );
			}
			tag = '<span class="ngl-tag">' + tag + '</span><i class="ngl-tag-spacer" id="ngl-tag-spacer-' + uniqid + '">&nbsp;</i>';
		}

		// Get selection.
		var startIndex = wp.data.select('core/block-editor').getSelectionStart().offset;
		var endIndex   = wp.data.select('core/block-editor').getSelectionEnd().offset;

		// Insert tag at specified caret.
		var html  = $block.attributes.content;
		var value = wp.richText.create( { html } );

		value = wp.richText.insert( value, tag, startIndex, endIndex );

		$block.attributes.content = wp.richText.toHTMLString( { value } ).replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

		wp.data.dispatch( 'core/block-editor' ).updateBlock( $block.clientId, $block.attributes );

		// Make a dummy element to keep editing.
		var el = document.getElementById( 'ngl-tag-spacer-' + uniqid );
		var range = document.createRange();
		var sel = window.getSelection();
		range.setStartAfter( el, 0 );
		range.collapse( true );
		sel.removeAllRanges();
		sel.addRange( range );
		el.focus();
	} );

	// Open sub menu.
	$( document ).on( 'click', '.ngl-gutenberg-icon', function( event ) {
		event.stopPropagation();
		var id = $( this ).parents( 'button' ).attr( 'data-tag-id' );
		$( '.ngl-fallback:not([data-tag=' + id + '])' ).hide();
		$( 'button.ngl-submenu-item' ).removeClass( 'is-active' );
		if ( $( '.ngl-fallback[data-tag=' + id + ']' ).is( ':hidden' ) ) {
			$( '.ngl-fallback[data-tag=' + id + ']' ).show();
			$( '.ngl-fallback[data-tag=' + id + ']' ).find( 'input' ).focus();
			$( this ).parents( 'button' ).addClass( 'is-active' );
		} else {
			$( '.ngl-fallback[data-tag=' + id + ']' ).hide();
			$( this ).parents( 'button' ).removeClass( 'is-active' );
		}
	} );

	// When clicked enter after fallback input.
	$( document ).on( 'keyup', '.ngl-fallback-input input', function ( e ) {
		if ( e.key === 'Enter' || e.keyCode === 13 ) {
			var id = $( this ).attr( 'data-tag-input-id' );
			var btn = $( '.ngl-submenu-item[data-tag-id=' + id + ']' );
			if ( $( this ).hasClass( 'is-mandatory' ) && $( this ).val() == '' ) {
				return false;
			}
			btn.trigger( 'click' );
		}
	} );

	// When fallback input is changed.
	$( document ).on( 'change', '.ngl-fallback-input input', function( event ) {
		if ( $( this ).val() != '' ) {
			$( this ).removeClass( 'is-mandatory' );
		} else {
			$( this ).addClass( 'is-mandatory' );
		}
	} );

	// Save fallbacks.
	$( document ).on( 'change', '.ngl-fallback-input input[type=text]', function() {
		var id 		= $( this ).attr( 'data-tag-input-id' );
		var val 	= $( this ).val();
		var data 	= 'action=newsletterglue_update_merge_tag&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + encodeURIComponent( val );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( ) { }
		} );
	} );

	// Default patterns row actions.
	$( document ).on( 'click', '.type-ngl_pattern span.edit a', function( event ) {
		if ( $( this ).parents( 'td' ).find( '.ngl-pattern-state' ).length ) {
			var row = $( this ).parents( 'td' ).find( '.row-actions' );
			if ( row.find( '.ngl-pattern-row' ).length == 0 ) {
				event.preventDefault();
				row.find( 'span.ngl_duplicate' ).addClass( 'ngl-bold' );
				var html = newsletterglue_params.pattern_edit;
				row.append( html );
				return false;
			}
		}
	} );

	// Go back - default patterns text.
	$( document ).on( 'click', '.ngl-pattern-bk', function( event ) {
		event.preventDefault();
		var row = $( this ).parents( 'td' ).find( '.row-actions' );
		row.find( 'span.ngl_duplicate' ).removeClass( 'ngl-bold' );
		$( this ).parents( '.ngl-pattern-row' ).remove();
		return false;
	} );

	// Show patterns reset UI on load.
	$( '.ngl-pattern-reset' ).show().insertBefore( '#ajax-response' );

	// Toggle the reset pattern.
	$( document ).on( 'click', '.ngl-pattern-reset-toggle', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-pattern-reset' ).find( '.ngl-pattern-reset-ui' );
		if ( el.is( ':visible' ) ) {
			el.hide();
		} else {
			el.show();
		}
		return false;
	} );

	// When reset pattern select is changed.
	$( document ).on( 'change', '.ngl-pattern-reset-ui select', function( event ) {
		event.preventDefault();
		var selected = $( this ).find( ':selected' ).attr( 'data-url' );
		$( this ).parents( '.ngl-pattern-reset-ui' ).find( '.ngl-pattern-reset-start' ).attr( 'href', selected );
		return false;
	} );

} ) ( jQuery );
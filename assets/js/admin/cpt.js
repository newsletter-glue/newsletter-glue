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

	// Open merge tags list.
	$( document ).on( 'click', '.ngl-toolbar-mergetags', function() {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( this ).parents( '.block-editor-block-toolbar' ).hasClass( '.is-showing-movers' ) ) {
			var topgap = 30;
		} else {
			var topgap = 25;
		}
		if ( ! pop.hasClass( 'is-open' ) ) {
			pop.addClass( 'is-open' );
			pop.css( {
				left: $( this ).offset().left,
				top: $( this ).offset().top + $( this ).height() + topgap + 'px'
			} );
		} else {
			ngl_close_popover();
		}
	} );

	// When clicked in body.
	$( 'body' ).on( 'click', function(event) {
		var pop = $( '.ngl-gutenberg-pop' );
		if ( $( event.target ).parents( '.ngl-toolbar-mergetags' ).length ) {
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

		var $block = wp.data.select( 'core/block-editor' ).getSelectedBlock();

		ngl_close_popover();

		var tag 	= $( this ).attr( 'data-ngl-tag' );
		var tag_id 	= $( this ).attr( 'data-tag-id' );

		var fallback = $( 'input[type=text]#__fallback_' + tag_id );

		var uniqid = Math.round( new Date().getTime() + ( Math.random() * 100 ) );

		// Make tag markup.
		if ( tag_id == 'unsubscribe_link' || tag_id == 'webversion' || tag_id == 'blog_post' ) {
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
			$( '.ngl-submenu-item[data-tag-id=' + id + ']' ).trigger( 'click' );
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

} ) ( jQuery );
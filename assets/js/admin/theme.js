( function( $ ) {
	"use strict";

	// Ajax request to refresh the image preview
	function newsletterglue_refresh_image( elem_id, the_id ) {

		var data = {
			action: 'newsletterglue_ajax_save_image',
			elem_id: elem_id,
			id: the_id,
			security:  newsletterglue_params.ajaxnonce
		};

		$.ajax( {
			type	: 'post',
			url		: newsletterglue_params.ajaxurl,
			data	: data,
			beforeSend : function() {

			},
			success : function( response ) {
				if ( response.success === true) {
					$( '#' + elem_id ).parents( '.ngl-theme-upload' ).find( '.ngl-theme-upload-name' ).html( response.data.html );
					$( '#' + elem_id ).val( response.data.url );
					$( '#' + elem_id + '_id' ).val( response.data.id );
					if ( $( '#ngl_add_logo' ).is( ':checked' ) ) {
						$( '.ngl-email-logo' ).removeClass( 'is-hidden' ).find( 'img' ).attr( 'src', response.data.url );
					} else {
						$( '.ngl-email-logo' ).addClass( 'is-hidden' ).find( 'img' ).attr( 'src', response.data.url );
					}
				} else {
					$( '#' + elem_id ).parents( '.ngl-theme-upload' ).find( '.ngl-theme-upload-name' ).html( newsletterglue_params.no_image_set );
					$( '#' + elem_id ).val( '' );
					$( '#' + elem_id + '_id' ).val( '' );
					$( '.ngl-email-logo' ).addClass( 'is-hidden' ).find( 'img' ).attr( 'src', '' );
				}
			}
		} );

	}

	// Function to trigger media library.
	function newsletterglue_open_media( el ) {

		var image_frame;

		var elem_id = el.attr( 'data-id' );

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
			var selection =  image_frame.state().get('selection');
			var gallery_ids = new Array();
			var my_index = 0;
			selection.each( function( attachment ) {
				gallery_ids[my_index] = attachment['id'];
				my_index++;
			} );
			var ids = gallery_ids.join(",");
			$( 'input#' + elem_id + '_id' ).val(ids);
			newsletterglue_refresh_image( elem_id, ids );
		});

		image_frame.on( 'open', function() {
			var selection = image_frame.state().get('selection');
			var ids = $( 'input#' + elem_id + '_id' ).val().split(',');
			ids.forEach( function( id ) {
				if ( id ) {
					var attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				}
			} );
		} );

		image_frame.open();

	}

	// Update input.
	function newsletterglue_update_input( t ) {
		if ( t.parents( '.ngl-settings-accent' ).length ) {
			return false;
		}

		var el 		= t.parents( '.components-base-control' );
		var id 		= t.attr( 'data-option' );
		var value 	= t.val();
		var input	= t;

		if ( $( '.ngl-email.ngl-mobile' ).is( ':visible' ) ) {
			var email = $( '.ngl-email.ngl-mobile' );
		} else {
			var email = $( '.ngl-email.ngl-desktop' );
		}

		if ( t.hasClass( 'dropdown' ) ) {
			id    = t.parents( '.components-base-control' ).attr( 'data-option' );
			value = t.dropdown( 'get value' );
		}

		// Dealing with checkboxes.
		if ( t.is( ':checkbox' ) ) {
			if ( t.is( ':checked' ) ) {
				value = 1;
			} else {
				value = 0;
			}
		}

		var data 	= 'action=newsletterglue_ajax_save_theme_setting&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value;

		// apply font face.
		if ( id == 'font' ) {
			if ( value && value != '0' ) {
				var font = t.parents( '.components-base-control' ).find( '.ui .text' ).html();
				email.css( { 'font-family' : font } );
			} else {
				email.css( { 'font-family' : 'inherit' } );
			}
		}

		// changing align.
		if ( id == 'h1_align' || id == 'h2_align' || id == 'h3_align' || id == 'h4_align' || id == 'h5_align' || id == 'h6_align' || id == 'p_align' ) {
			var attr = id.replace( '_align', '' );
			$( '.ngl-email' ).find( attr ).css( { 'text-align' : value } );
		}

		// changing font-size.
		if ( id == 'h1_size' || id == 'h2_size' || id == 'h3_size' || id == 'h4_size' || id == 'h5_size' || id == 'h6_size' || id == 'p_size' ) {
			var attr = id.replace( '_size', '' );
			email.find( attr ).not( '.ngl-mobile' ).css( { 'font-size' : value + 'px' } );
		}

		// changing font size in mobile.
		if ( id == 'mobile_h1_size' || id == 'mobile_h2_size' || id == 'mobile_h3_size' || id == 'mobile_h4_size' || id == 'mobile_h5_size' || id == 'mobile_h6_size' || id == 'mobile_p_size' ) {
			var attr = id.replace( '_size', '' ).replace( 'mobile_', '' );
			email.find( attr ).not( '.ngl-desktop' ).css( { 'font-size' : value + 'px' } );
		}

		// changing color.
		if ( id == 'h1_colour' || id == 'h2_colour' || id == 'h3_colour' || id == 'h4_colour' || id == 'h5_colour' || id == 'h6_colour' || id == 'p_colour' || id == 'a_colour' ) {
			var attr = id.replace( '_colour', '' );
			email.find( attr ).not( '.wp-block-button__link' ).css( { 'color' : value } );
		}

		// changing backgrounds.
		if ( id == 'email_bg' ) {
			$( '.ngl-email' ).css( { 'background-color' : value } );
		}
		if ( id == 'container_bg' ) {
			email.find( '.ngl-email-container' ).css( { 'background-color' : value } );
			$( '.ngl-email-container' ).css( { 'background-color' : value } );
		}

		// button colour.
		if ( id == 'btn_bg' ) {
			email.find( '.wp-block-button__link' ).css( { 'background-color' : value } );
		}
		if ( id == 'btn_colour' ) {
			email.find( '.wp-block-button__link' ).css( { 'color' : value } );
		}
		if ( id == 'btn_border' ) {
			email.find( '.wp-block-button__link' ).css( { 'border-color' : value } );
		}
		if ( id == 'btn_radius' ) {
			email.find( '.wp-block-button__link' ).css( { 'border-radius' : value + 'px' } );
		}
		if ( id == 'btn_width' || id == 'mobile_btn_width' ) {
			var width = value + 'px';
			if ( value == 0 ) {
				width = 'auto';
			}
			email.find( '.wp-block-button__link' ).css( { 'min-width' : width } );
		}
		if ( id == 'max_logo_w' || id == 'mobile_max_logo_w' ) {
			var width = value + 'px';
			if ( value == 0 ) {
				width = '100%';
			}
			email.find( '.ngl-email-logo img' ).css( { 'max-width' : width } );
		}

		// container
		if ( id == 'container_padding1' || id == 'mobile_container_padding1' ) {
			email.find( '.ngl-email-container' ).css( { 'padding-top' : value + 'px' } );
		}
		if ( id == 'container_padding2' || id == 'mobile_container_padding2' ) {
			email.find( '.ngl-email-container' ).css( { 'padding-bottom' : value + 'px' } );
		}
		if ( id == 'container_margin1' || id == 'mobile_container_margin1' ) {
			email.find( '.ngl-email-container' ).css( { 'margin-top' : value + 'px' } );
		}
		if ( id == 'container_margin2' || id == 'mobile_container_margin2' ) {
			email.find( '.ngl-email-container' ).css( { 'margin-bottom' : value + 'px' } );
		}

		// credits.
		if ( id == 'ngl_credits' ) {
			if ( value == 1 ) {
				$( '.ngl-email' ).find( '.ngl-credits' ).removeClass( 'is-hidden' );
			} else {
				$( '.ngl-email' ).find( '.ngl-credits' ).addClass( 'is-hidden' );
			}
		}

		// show/hide logo.
		if ( id == 'ngl_add_logo' ) {
			if ( value == 1 && $( '#newsletterglue_logo_id' ).val() > 0 ) {
				$( '.ngl-email' ).find( '.ngl-email-logo' ).removeClass( 'is-hidden' );
			} else {
				$( '.ngl-email' ).find( '.ngl-email-logo' ).addClass( 'is-hidden' );
			}
		}

		// show/hide post title.
		if ( id == 'ngl_add_title' ) {
			if ( value == 1 ) {
				$( '.ngl-email' ).find( 'h1:first' ).removeClass( 'is-hidden' );
			} else {
				$( '.ngl-email' ).find( 'h1:first' ).addClass( 'is-hidden' );
			}
		}

		// show/hide featured image.
		if ( id == 'ngl_add_featured' ) {
			if ( value == 1 ) {
				$( '.ngl-email' ).find( '.ngl-masthead' ).removeClass( 'is-hidden' );
			} else {
				$( '.ngl-email' ).find( '.ngl-masthead' ).addClass( 'is-hidden' );
			}
		}

		if ( id == 'ngl_position_featured' ) {
			if ( value == 'above' ) {
				$( '.ngl-email' ).each( function() {
					var el = $( this ).find( '.ngl-masthead-below' );
					el.insertBefore( $( this ).find( 'h1.ngl-primary-h' ) );
					el.removeClass( 'ngl-masthead-below' ).addClass( 'ngl-masthead-above' );
				} );
			} else {
				$( '.ngl-email' ).each( function() {
					var el = $( this ).find( '.ngl-masthead-above' );
					el.insertAfter( $( this ).find( 'h1.ngl-primary-h' ) );
					el.removeClass( 'ngl-masthead-above' ).addClass( 'ngl-masthead-below' );
				} );
			}
		}

		if ( id == 'ngl_position_logo' ) {
			$( '.ngl-email-logo' ).removeClass( 'ngl-logo-left ngl-logo-center ngl-logo-right ngl-logo-full' ).addClass( 'ngl-logo-' + value );
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				input.attr( 'disabled', 'disabled' );
			},
			success: function( response ) {
				el.find( '.ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
				input.removeAttr( 'disabled' );
				setTimeout( function() {
						el.find( '.ngl-process' ).addClass( 'is-hidden' );
				}, 1500 );
			}
		} );
	}

	// Init color.
	function newsletterglue_init_color() {
		$( '.ngl-color-field' ).each( function() {
			var el = $( this );
			el.spectrum( {
				type: 'color',
				showPalette: false,
				showInput: true,
				showButtons: false,
				showAlpha: false,
				allowEmpty: true,
				clickoutFiresChange: true,
				move: function( color ) {
					if ( color ) {
						var thecolor = color.toHexString();
					} else {
						var thecolor = '';
					}
					var id = el.parents( '.ngl-theme-color' ).find( 'input.ngl-theme-input' ).attr( 'data-option' );
					newsletterglue_update_input( $( 'input[data-option=' + id + ']' ) );
					if ( $( '.ngl-customize-preview' ).length ) {
						$( '.ngl-customize-preview' ).find( 'blockquote' ).css( { 'border-color' : thecolor } );
						$( '.ngl-customize-preview' ).find( 'p a' ).css( { 'color' : thecolor } );
						$( '.ngl-customize-preview' ).find( 'p.ngl-customize-button a' ).css( { 'background-color' : thecolor } );
					}
				}
			} );
		} );
	}

	// Open image selection.
	$( document ).on( 'click', '.ngl-theme-upload-button a, .ngl-image-trigger', function( event ) {
		event.preventDefault();
		newsletterglue_open_media( $( this ).parents( '.ngl-theme-upload' ) );
		return false;
	} );

	// Remove image selection.
	$( document ).on( 'click', '.ngl-image-remove', function( event ) {
		event.preventDefault();
		var elem_id = $( this ).parents( '.ngl-theme-upload' ).attr( 'data-id' );
		newsletterglue_refresh_image( elem_id, 0 );
		if ( elem_id == 'newsletterglue_logo' ) {
			$( '.ngl-email-logo' ).addClass( 'is-hidden' );
		}
		return false;
	} );

	// Show or hide theme options.
	$( document ).on( 'ngl-theme-options', function() {
		if ( $( '#ngl_credits' ).is( ':checked' ) ) {
			$( '.ngl-email' ).find( '.ngl-credits' ).removeClass( 'is-hidden' );
		} else {
			$( '.ngl-email' ).find( '.ngl-credits' ).addClass( 'is-hidden' );
		}
		if ( $( '#ngl_add_featured' ).is( ':checked' ) ) {
			$( '.ngl-email' ).find( '.ngl-masthead' ).removeClass( 'is-hidden' );
		} else {
			$( '.ngl-email' ).find( '.ngl-masthead' ).addClass( 'is-hidden' );
		}
	} ).trigger( 'ngl-theme-options' );

	// Min / max.
    $( document ).on( 'keyup', '.ngl-theme-px input[type="number"]',function() {
        var v = parseInt($(this).val());
        var min = parseInt($(this).attr('min'));
        var max = parseInt($(this).attr('max'));
		if ( v < min ) {
			$(this).val(min);
        }
		if ( v > max ) {
            $(this).val(max);
        }
    } );

	// AJAX saving.
	$( document ).on( 'change', '.ngl-theme-input', function() {

		newsletterglue_update_input( $( this ) );

	} );

	// Prevent default behaviour.
	$( document ).on( 'click', '.ngl-email a', function( event ) {
		event.preventDefault();
		return false;
	} );

	// Init color picker.
	newsletterglue_init_color();

	// Show/hide more.
	$( document ).on( 'click', '.ngl-theme-more', function( event ) {
		event.preventDefault();
		$( this ).hide();
		$( '.ngl-theme-hidden' ).show();
	} );

	$( document ).on( 'click', '.ngl-theme-less', function( event ) {
		event.preventDefault();
		$( '.ngl-theme-more' ).show();
		$( '.ngl-theme-hidden' ).hide();
	} );

	// Reset theme.
	$( document ).on( 'click', '.ngl-theme-reset-link', function() {
		event.preventDefault();

		var el 		= $( this ).parents( '.ngl-theme-reset' );

		if ( el.find( '.ngl-theme-reset-confirm' ).hasClass( 'is-hidden' ) ) {
			el.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).removeClass( 'is-hidden' );
		} else {
			el.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );
		}

		return false;
	} );

	// Back link.
	$( document ).on( 'click', '.ngl-theme-reset-back', function( event ) {
		event.preventDefault();

		var el 		= $( this ).parents( '.ngl-theme-reset' );

		el.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );

		return false;
	} );

	// Reset confirmation.
	$( document ).on( 'click', '.ngl-theme-reset-do', function( event ) {
		event.preventDefault();

		var el 		= $( this ).parents( '.ngl-theme-reset' );
		var data 	= 'action=newsletterglue_ajax_reset_theme&security=' + newsletterglue_params.ajaxnonce;

		var mobile  = false;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$( '.ngl-theme' ).addClass( 'ngl-load' );
				el.find( '.ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				el.find( '.ngl-theme-reset-confirm, .ngl-theme-reset-btns' ).addClass( 'is-hidden' );
				if ( $( '.ngl-theme' ).hasClass( 'ngl-mobile' ) ) {
					mobile = true;
				}
			},
			success: function( response ) {
				if ( $( '.ngl-theme' ).length == 0 ) {
					var thecolor = '#0088A0';
					el.find( '.ngl-process.is-waiting' ).addClass( 'is-hidden' );
					el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					el.parents( '.ngl-metabox-flex' ).find( '.ngl-color-field' ).spectrum( 'set', thecolor );
					if ( $( '.ngl-customize-preview' ).length ) {
						$( '.ngl-customize-preview' ).find( 'blockquote' ).css( { 'border-color' : thecolor } );
						$( '.ngl-customize-preview' ).find( 'p a' ).css( { 'color' : thecolor } );
						$( '.ngl-customize-preview' ).find( 'p.ngl-customize-button a' ).css( { 'background-color' : thecolor } );
					}
					setTimeout( function() {
						el.find( '.ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );
				} else {
					$( '.ngl-theme' ).replaceWith( response );
					$( '.ngl-theme' ).find( '.ui.dropdown, .ui.dropdown' ).dropdown();
					$( '.ngl-theme' ).find( '.ngl-theme-reset .ngl-process' ).addClass( 'is-hidden' );
					$( '.ngl-theme' ).find( '.ngl-theme-reset .ngl-process.is-valid' ).removeClass( 'is-hidden' );
					if ( mobile ) {
						$( '.ngl-theme' ).addClass( 'ngl-mobile' );
						$( '.ngl-theme .ngl-desktop' ).hide();
					} else {
						$( '.ngl-theme' ).removeClass( 'ngl-mobile' );
						$( '.ngl-theme .ngl-desktop' ).show();
					}
					newsletterglue_init_color();
					setTimeout( function() {
						$( '.ngl-theme' ).find( '.ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );
				}
			}
		} );

		return false;
	} );

	// Switch desktop / mobile.
	$( document ).on( 'click', '.ngl-theme-toggle span.ngl-mobile', function() {
		var el = $( '.ngl-theme-toggle' );
		el.find( 'span.ngl-bulb' ).css( { 'left' : '80px' } ).html ( $( this ).html() );
		$( '.ngl-theme' ).addClass( 'ngl-mobile' );
		$( '.ngl-theme .ngl-desktop' ).hide();
	} );

	$( document ).on( 'click', '.ngl-theme-toggle span.ngl-desktop', function() {
		var el = $( '.ngl-theme-toggle' );
		el.find( 'span.ngl-bulb' ).css( { 'left' : '0' } ).html ( $( this ).html() );
		$( '.ngl-theme' ).removeClass( 'ngl-mobile' );
		$( '.ngl-theme .ngl-desktop' ).show();
	} );

	// Switch checkbox status.
	if ( $( document ).find( '#ngl_add_title' ).length ) {
		if ( ! $( document ).find( '#ngl_add_title' ).is( ':checked' ) ) {
			$( document ).find( '#ngl_link_title' ).prop( 'disabled', true );
			$( document ).find( '#ngl_link_title' ).parents( '.components-base-control__field' ).addClass( 'disabled' );
		}
	}

	if ( $( document ).find( '#ngl_add_featured' ).length ) {
		if ( ! $( document ).find( '#ngl_add_featured' ).is( ':checked' ) ) {
			$( document ).find( '#ngl_link_featured' ).prop( 'disabled', true );
			$( document ).find( '#ngl_link_featured' ).parents( '.components-base-control__field' ).addClass( 'disabled' );
		}
	}

	$( document ).on( 'change', '#ngl_add_title', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( document ).find( '#ngl_link_title' ).prop( 'disabled', false );
			$( document ).find( '#ngl_link_title' ).parents( '.components-base-control__field' ).removeClass( 'disabled' );
		} else {
			$( document ).find( '#ngl_link_title' ).prop( 'disabled', true );
			$( document ).find( '#ngl_link_title' ).parents( '.components-base-control__field' ).addClass( 'disabled' );
		}
	} );

	$( document ).on( 'change', '#ngl_add_featured', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( document ).find( '#ngl_link_featured' ).prop( 'disabled', false );
			$( document ).find( '#ngl_link_featured' ).parents( '.components-base-control__field' ).removeClass( 'disabled' );
		} else {
			$( document ).find( '#ngl_link_featured' ).prop( 'disabled', true );
			$( document ).find( '#ngl_link_featured' ).parents( '.components-base-control__field' ).addClass( 'disabled' );
		}
	} );

} ) ( jQuery );
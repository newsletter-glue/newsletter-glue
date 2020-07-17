( function( $ ) {
	"use strict";

	var ngl_service;
	var ngl_back_screen;
	var xhr;

	// Show different connect screens.
	function ngl_show_first_screen() {
		$( '.ngl-card-add' ).removeClass( 'ngl-hidden' );
		$( '.ngl-card-state, .ngl-card-add2, .ngl-card-view' ).addClass( 'ngl-hidden' );
	}

	function ngl_show_testing_screen() {
		$( '.ngl-card-state.is-testing' ).removeClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
	}

	function ngl_show_not_connected_screen() {
		$( '.ngl-card-state.is-testing' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-invalid' ).removeClass( 'ngl-hidden' );
	}

	function ngl_show_connected_screen() {

		$( '.ngl-card-state.is-testing' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-working' ).removeClass( 'ngl-hidden' );

		if ( $( '.ngl-card-view' ).length ) {
			setTimeout( function() {
				$( '.ngl-card-state, .ngl-card-add2' ).addClass( 'ngl-hidden' );
				$( '.ngl-card-view-' + ngl_service ).removeClass( 'ngl-hidden' );
			}, 2000 );
		} else {
			// We are in onboarding.
			$( '.ngl-boarding:visible' ).addClass( 'is-hidden' );
			$( '.ngl-boarding[data-screen=3]' ).removeClass( 'is-hidden' );
			ngl_onboarding_settings( ngl_service );
		}

	}

	// Get onboarding settings.
	function ngl_onboarding_settings( api ) {
		var data = 'action=newsletterglue_ajax_get_onboarding_settings&security=' + newsletterglue_params.ajaxnonce + '&api=' + api;
		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( response ) {
				if ( response ) {
					$( '.ngl-modal:visible' ).prepend( response );
					$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
				}
			}
		} );
	}

	function ngl_open_modal( el ) {
		$( '.ngl-modal-overlay' ).removeClass( 'off' );
		var td = el.parents( 'td' );
		$( '.ngl-modal-content' ).html( td.find( '.ngl-modal-contents' ).html() );
	}

	function ngl_close_modal() {
		$( '.ngl-modal-overlay' ).addClass( 'off' );
	}

	// Validates the form and output correct notice.
	function ngl_validate_form() {
		var f = $( '.ngl-metabox' );

		if ( f.length == 0 ) {
			return false;
		}

		if ( $( '.ngl-settings' ).length ) {
			return false;
		}

		var ready = true;

		f.find( 'input[type=text].is-required' ).each( function() {
			if ( $( this ).val() == '' || $( this ).attr( 'data-force-unready' ) ) {
				ready = false;
				$( this ).closest( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( this ).closest( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		} );

		f.find( '.dropdown.is-required' ).each( function() {
			if ( $( this ).dropdown( 'get value' ) == '' ) {
				ready = false;
				$( this ).closest( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( this ).closest( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		} );

		// Is form ready?
		if ( ready ) {
			$( '.ngl-ready' ).removeClass( 'is-hidden' );
			$( '.ngl-not-ready' ).addClass( 'is-hidden' );
			$( '.editor-post-publish-button__button.is-primary' ).removeAttr( 'disabled' );
			$( '.ngl-newsletter-errors' ).remove();
		} else {
			$( '.ngl-ready' ).addClass( 'is-hidden' );
			$( '.ngl-not-ready' ).removeClass( 'is-hidden' );
			$( '.editor-post-publish-button__button.is-primary' ).attr( 'disabled', 'disabled' );
			if ( $( '.ngl-newsletter-errors' ).length == 0 ) {
				$( '.edit-post-header__settings' ).prepend( '<span class="ngl-newsletter-errors">' + newsletterglue_params.publish_error + '</span>' );
			}
		}
		
		if ( ! $( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			$( '.ngl-newsletter-errors' ).remove();
			$( '.editor-post-publish-button__button.is-primary' ).removeAttr( 'disabled' );
		}
	}

	// validate the email.
	function ngl_validate_email() {

		var email 	= $( '#ngl_from_email' ).val();
		var service = $( '#ngl_provider' ).val();

		var data = 'action=newsletterglue_ajax_verify_email&security=' + newsletterglue_params.ajaxnonce + '&email=' + email + '&service=' + service;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$( '.ngl-process' ).addClass( 'is-hidden' );
				$( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
			},
			success: function( response ) {
				$( '.ngl-process' ).addClass( 'is-hidden' );
				if ( response.success ) {
					$( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					$( '.ngl-process.is-valid .ngl-process-text' ).html( response.success );
					$( '#ngl_from_email' ).parent().parent().parent().removeClass( 'is-error' );
					$( '#ngl_from_email' ).removeAttr( 'data-force-unready' );
				} else {
					$( '.ngl-process.is-invalid' ).removeClass( 'is-hidden' );
					$( '.ngl-process.is-invalid .ngl-process-text' ).html( response.failed );
					$( '#ngl_from_email' ).parent().parent().parent().addClass( 'is-error' );
					$( '#ngl_from_email' ).attr( 'data-force-unready', true );
				}
				ngl_validate_form();
			}
		} );
	}

	// Init fields.
	$( '.ngl .ui.dropdown, .ngl-metabox .ui.dropdown' ).dropdown( { onChange: function() { ngl_validate_form(); } } );
	$( '.ngl .ui.checkbox' ).checkbox();

	// Date and time picker.
	$( '.ngl-date' ).flatpickr( {
		enableTime: true,
		dateFormat: "Y-m-d H:i:s",
		altInput: true,
		enableSeconds: true,
		altFormat: "H:i:s, Y/m/d",
		minDate: "today",
		onChange: function() { ngl_validate_form(); }
	} );

	// Trigger modal manually.
	$( document ).on( 'click', '.ngl-request-modal', function( event ) {
		srv.openModal( true, '81611450cdc75d0c082f2ad30dc8d7bb9836d515' );
	} );

	// When user clicks to add new connection.
	$( document ).on( 'click', '.ngl-card-add', function( event ) {
		$( this ).addClass( 'ngl-hidden' );
		$( '.ngl-card-base' ).removeClass( 'ngl-hidden' );
	} );

	// When a service is selected.
	$( '.ngl-service' ).dropdown( 'setting', 'onChange', function( val ) {
		$( this ).parents( '.ngl-card-base' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-' + val ).removeClass( 'ngl-hidden' );
		ngl_service = val;
	} );

	// Back one screen.
	$( document ).on( 'click', '.ngl-back', function( event ) {
		
		if ( ! ngl_back_screen ) {
			var screen = $( this ).attr( 'data-screen' );
			$( '.ngl-service' ).dropdown( 'clear' );
			$( this ).parent().parent().addClass( 'ngl-hidden' );
			$( '.' + screen ).removeClass( 'ngl-hidden' );
		} else {
			$( this ).parent().parent().addClass( 'ngl-hidden' );
		}
	} );

	// Connection form.
	$( document ).on( 'submit', '.ngl-fields form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var service = $( this ).parents( '.ngl-card-add2' ).attr( 'data-service' );
		var data 	= theform.serialize() + '&action=newsletterglue_ajax_connect_api&security=' + newsletterglue_params.ajaxnonce + '&service=' + service;

		var stop_form = false;
		if ( ! $( '.ngl-card-' + service ).hasClass( 'ngl-hidden' ) ) {
			theform.find( 'input[type=text]:visible' ).each( function() {
				if ( $( this ).val() == '' ) {
					$( this ).addClass( 'error' ).focus();
					stop_form = true;
				}
			} );
		}

		if ( stop_form ) {
			return false;
		}

		xhr = $.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				ngl_show_testing_screen();
			},
			success: function( result ) {
				setTimeout( function() {
					if ( result.response === 'invalid' ) {
						ngl_show_not_connected_screen();
					}
					if ( result.response === 'successful' ) {
						ngl_show_connected_screen();
					}
				}, 1000 );
			}
		} );

		return false;
	} );

	// Stop test.
	$( document ).on( 'click', '.ngl-ajax-stop-test', function( event ) {
		event.preventDefault();
		var el = $( this ).parents( '.ngl-card-state' );
		el.addClass( 'ngl-hidden' );
		xhr.abort();
		return false;
	} );

	// Test connection.
	$( document ).on( 'click', '.ngl-ajax-test-connection', function( event ) {
		event.preventDefault();
		ngl_service = $( this ).parents( '.ngl-card-view' ).attr( 'data-service' );
		$( '.ngl-card-add2.ngl-card-' + ngl_service + ' .ngl-fields form' ).trigger( 'submit' );
		return false;
	} );

	// Test again.
	$( document ).on( 'click', '.ngl-ajax-test-again', function( event ) {
		event.preventDefault();
		$( '.ngl-card-add2 .ngl-fields form' ).trigger( 'submit' );
		return false;
	} );

	// Close not connecting test.
	$( document ).on( 'click', '.ngl-ajax-test-close', function( event ) {
		event.preventDefault();
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
		return false;
	} );

	// Edit connection details.
	$( document ).on( 'click', '.ngl-ajax-edit-connection', function( event ) {
		event.preventDefault();
		$( '.ngl-card-state.is-invalid' ).addClass( 'ngl-hidden' );
		if ( $( this ).parents( '.ngl-card-view' ).is( ':visible' ) ) {
			ngl_service = $( this ).parents( '.ngl-card-view' ).attr( 'data-service' );
			ngl_back_screen = $( this ).parents( '.ngl-card-view-' + ngl_service );
		}
		$( '.ngl-card-add2.ngl-card-' + ngl_service ).removeClass( 'ngl-hidden' );
		return false;
	} );

	// Remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove-connection', function( event ) {
		event.preventDefault();
		ngl_service = $( this ).parents( '.ngl-card-view' ).attr( 'data-service' );
		$( '.ngl-ajax-remove' ).attr( 'data-ngl_service', ngl_service );
		$( '.ngl-card-state.confirm-remove' ).removeClass( 'ngl-hidden' );
		return false;
	} );

	// Confirm remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove', function( event ) {
		event.preventDefault();
		$( '.ngl-card-state.confirm-remove' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-removed' ).removeClass( 'ngl-hidden' );
		$( '.ngl-service' ).dropdown( 'clear' );

		var data = 'action=newsletterglue_ajax_remove_api&security=' + newsletterglue_params.ajaxnonce + '&service=' + $( this ).attr( 'data-ngl_service' );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( result ) {
				setTimeout( function() {
					ngl_show_first_screen();
				}, 2000 );
			}
		} );

		return false;
	} );

	// Toggle metabox options.
	$( document ).on( 'change', '#ngl_send_newsletter', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '.ngl-metabox-if-checked' ).removeClass( 'is-hidden' );
		} else {
			$( '.ngl-metabox-if-checked' ).addClass( 'is-hidden' );
		}
		ngl_validate_form();
	} );

	// Revalidate email.
	$( document ).on( 'change', '#newsletter_glue_metabox #ngl_from_email', function() {
		ngl_validate_email();
	} );

	// JS limit for some text inputs.
	$( '.ngl-metabox input[type=text].js-limit' ).each( function( i, obj ) {
		var str = $( this ).val();
		$( this ).attr( 'data-value', str );
		if ( str.length > 60 ) {
			$( this ).parent().find( '.ngl-limit' ).show();
		} else {
			$( this ).parent().find( '.ngl-limit' ).hide();
		}
	} );

	$( document ).on( 'focus', '.ngl-metabox input[type=text].js-limit', function( event ) {
		$( this ).parent().find( '.ngl-limit' ).hide();
	} );

	$( document ).on( 'click', '.ngl-metabox .ngl-limit', function( event ) {
		$( this ).parents( '.ui.input' ).find( '.js-limit' ).focus();
	} );

	$( document ).on( 'blur', '.ngl-metabox input[type=text].js-limit', function( event ) {
		var str = $( this ).val();
		$( this ).attr( 'data-value', str );
		if ( str.length > 60 ) {
			$( this ).parent().find( '.ngl-limit' ).show();
		} else {
			$( this ).parent().find( '.ngl-limit' ).hide();
		}
	} );

	// Run form validation when user edit metabox fields.
	$( document ).on( 'change', '.ngl-metabox input[type=text]', function() {
		ngl_validate_form();
	} );

	// Copy post title into newsletter subject.
	$( document ).on( 'blur', '.editor-post-title__input', function() {
		if ( $( this ).val() ) {
			$( '#ngl_subject' ).val( $( this ).val() ).trigger( 'change' );
		}
	} );

	// Reset newsletter.
	$( document ).on( 'click', '.ngl-reset-newsletter', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );

		var data = 'action=newsletterglue_ajax_reset_newsletter&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.addClass( 'loading' );
			},
			success: function( result ) {
				el.removeClass( 'loading' );
				$( '.ngl-reset, .ngl-msgbox-wrap' ).addClass( 'is-hidden' );
				$( '.ngl-send' ).removeClass( 'is-hidden' );
				if ( ! $( '#ngl_send_newsletter' ).is( ':checked' ) ) {
					$( '#ngl_send_newsletter' ).trigger( 'click' );
				}
			}
		} );

		return false;
	} );

	// Test newsletter.
	$( document ).on( 'click', '.ngl-test-email', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );
		var mb = el.parents( '.ngl-metabox' );

		var data = 'action=newsletterglue_ajax_test_email&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		mb.find( 'input[type=text], select, input[type=hidden]' ).each( function() {
			data = data + '&' + $( this ).attr( 'id' ) + '=' + $( this ).val();
		} );

		mb.find( 'input[type=checkbox]' ).each( function() {
			if ( $( this ).is( ':checked' ) ) {
				data = data + '&' + $( this ).attr( 'id' ) + '=1';
			}
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				$( '.ngl-is-default' ).hide();
				$( '.ngl-is-sending' ).show();
				$( '.ngl-test-result' ).hide();
			},
			success: function( response ) {
				$( '.ngl-is-sending' ).hide();
				$( '.ngl-action-link' ).show();
				if  ( response.success ) {
					$( '.ngl-is-valid' ).show();
					$( '.ngl-test-result.ngl-is-valid' ).show().html( response.success );
				} else {
					$( '.ngl-is-invalid' ).show();
					$( '.ngl-test-result.ngl-is-invalid' ).show().html( response.fail );
				}
			}
		} );

		return false;
	} );

	// Retest.
	$( document ).on( 'click', '.ngl-retest', function( event ) {
		event.preventDefault();
		$( '.ngl-action-link, .ngl-action button, .ngl-is-valid, .ngl-is-invalid' ).hide();
		$( '.ngl-is-default' ).show();
		return false;
	} );

	// Scroll to newsletter form.
	$( document ).on( 'click', '.ngl-newsletter-errors a', function( event ) {
		event.preventDefault();
		$( '.ngl-metabox .is-error' ).find( 'input:first' ).focus();
		return false;
	} );

	// Show modal.
	$( document ).on( 'click', '.ngl-modal-log', function( event ) {
		event.preventDefault();
		ngl_open_modal( $( this ) );
		return false;
	} );

	// Close modal.
	$( document ).on( 'click', '.ngl-modal-close', function( event ) {
		event.preventDefault();
		ngl_close_modal();
		return false;
	} );

	// When the overlay is clicked.
	$( document ).on( 'click', '.ngl-modal-overlay:not(.onboarding)', function( event ) {
		event.preventDefault();
		ngl_close_modal();
		return false;
	} );

	// When the overlay is clicked.
	$( document ).on( 'click', '.ngl-modal', function( event ) {
		event.stopPropagation();
	} );

	// Trigger newsletter sent message.
	$( document ).on( 'click', '.editor-post-publish-button', function( event ) {
		var metabox = $( '.ngl-send' );
		if ( metabox.find( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			metabox.addClass( 'is-hidden' );
			$( '.ngl-msgbox-wrap' ).removeClass( 'is-hidden' );
		}
	} );

	// Textarea tab indent.
	$( document ).delegate('.ngl-textarea', 'keydown', function(e) {
	  var keyCode = e.keyCode || e.which;

	  if (keyCode == 9) {
		e.preventDefault();
		var start = this.selectionStart;
		var end = this.selectionEnd;

		// set textarea value to: text before caret + tab + text after caret
		$(this).val($(this).val().substring(0, start)
					+ "\t"
					+ $(this).val().substring(end));

		// put caret at right position again
		this.selectionStart =
		this.selectionEnd = start + 1;
	  }
	});

	// Save settings.
	$( document ).on( 'click', '.ngl-settings-save:not(.saved)', function( event ) {

		event.preventDefault();

		$( '.ngl-settings input[type=text], .ngl-settings textarea, .ngl-settings input[type=checkbox], .ngl-settings select' ).trigger( 'change' );

		return false;

	} );

	// AJAX saving.
	$( document ).on( 'change', '.ngl-settings input[type=text], .ngl-settings textarea, .ngl-settings input[type=checkbox], .ngl-settings select', function() {

		var el 		= $( this ).parents( '.ngl-ajax-field' );
		var savebtn = $( '.ngl-settings-save' );
		var id 		= $( this ).attr( 'id' );
		var value 	= $( this ).val();

		if ( $( this ).is( ':checkbox' ) ) {
			if ( $( this ).is( ':checked' ) ) {
				value = 1;
			} else {
				value = 0;
			}
		}

		var data = 'action=newsletterglue_ajax_save_field&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				savebtn.html( newsletterglue_params.saving );

				if ( id == 'ngl_from_email' ) {
					if ( $( '.ngl-boarding' ).length ) {
						$( '.ngl-boarding-next' ).addClass( 'disabled' );
					}
				}
			},
			success: function( response ) {

				savebtn.addClass( 'saved' ).html( newsletterglue_params.saved );

				setTimeout( function() {
					savebtn.removeClass( 'saved' ).html( newsletterglue_params.save );
				}, 2000 );

				el.find( '.ngl-process' ).addClass( 'is-hidden' );

				if ( response.failed ) {
					el.find( '.ngl-process.is-invalid' ).removeClass( 'is-hidden' );
					el.find( '.ngl-process.is-invalid .ngl-process-text' ).html( response.failed );
					el.parent().parent().addClass( 'is-error' );
				} else if ( response.success ) {
					el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					el.find( '.ngl-process.is-valid .ngl-process-text' ).html( response.success );
					el.parent().parent().removeClass( 'is-error' );
				} else {
					el.parent().parent().removeClass( 'is-error' );
					el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					setTimeout( function() {
						el.find( '.ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );

				}

				if ( ! el.parent().parent().hasClass( 'is-error' ) ) {
					if ( id == 'ngl_from_email' ) {
						if ( $( '.ngl-boarding' ).length ) {
							setTimeout( function() {
								$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
							}, 1500 );
						}
					}
				}
			}
		} );

	} );

} )( jQuery );
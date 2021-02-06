( function( $ ) {
	"use strict";

	var ngl_app;
	var ngl_back_screen;
	var xhr;

	// Close popup.
	function ngl_close_popup() {
		var block_id = $( '.ngl-popup-panel' ).find( '.ngl-popup-settings' ).attr( 'data-block' );
		var block_id_demo = $( '.ngl-popup-panel' ).find( '.ngl-popup-demo' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).removeClass( 'ngl-active' );
		$( 'body' ).removeClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-panel .ngl-popup-settings' ).appendTo( $( '.ngl-block[data-block=' + block_id + ']' ) );
		$( '.ngl-popup-panel .ngl-popup-demo' ).appendTo( $( '.ngl-block[data-block=' + block_id_demo + ']' ) );
	}

	// Show different connect screens.
	function ngl_show_first_screen() {
		$( '.ngl-card-add' ).removeClass( 'ngl-hidden' );
		$( '.ngl-card-state, .ngl-card-add2, .ngl-card-view' ).addClass( 'ngl-hidden' );
		if ( $( '.ngl-card-license-form' ).length ) {
			$( '.ngl-card-license-form' ).removeClass( 'ngl-hidden' );
		}
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
				if ( $( '.ngl-card-view-' + ngl_app ).length ) {
					$( '.ngl-card-view-' + ngl_app ).removeClass( 'ngl-hidden' );
				} else {
					$( '.ngl-card-view' ).removeClass( 'ngl-hidden' );
				}
			}, 2000 );
		} else {
			// We are in onboarding.
			$( '.ngl-boarding:visible' ).addClass( 'is-hidden' );
			$( '.ngl-boarding[data-screen=3]' ).removeClass( 'is-hidden' );
			ngl_onboarding_settings( ngl_app );
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
					$( '.ngl-modal:visible' ).attr( 'data-app', api );
					$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
				}
			}
		} );
	}

	function ngl_open_modal( el ) {
		var overlay = $( '.ngl-modal-overlay' );

		overlay.removeClass( 'off' );

		var modal	= $( '.ngl-modal-loader' );
		var post_id = el.attr( 'data-post-id' );
		var data = 'action=newsletterglue_ajax_get_log&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				overlay.html( '<div class="ngl-modal ngl-modal-loader"><div class="ngl-loading"></div></div>' );
			},
			success: function( response ) {
				overlay.find( '.ngl-modal-loader' ).replaceWith( response );
			}
		} );

	}

	function ngl_close_modal() {
		$( '.ngl-modal-overlay' ).addClass( 'off' );
	}

	// Validates the form and output correct notice.
	function ngl_validate_form() {
		var f = $( '.ngl-metabox' );

		var app = f.find( '#ngl_app' ).val();

		if ( f.length == 0 ) {
			return false;
		}

		if ( $( '.ngl-settings' ).length ) {
			return false;
		}

		var ready = true;

		f.find( 'input[type=text].is-required' ).each( function() {
			if ( $( this ).val() == '' || ( $( this ).attr( 'data-force-unready' ) == '1' ) ) {
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

		if ( f.find( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter2' ).prop( 'checked', true );
			$( '#ngl_double_confirm' ).val( 'yes' );
		} else {
			$( '#ngl_send_newsletter2' ).prop( 'checked', false );
			$( '#ngl_double_confirm' ).val( 'no' );
		}

		// Campaign Monitor.
		if ( app === 'campaignmonitor' ) {
			var lists = $( '#ngl_lists' ).parent().dropdown( 'get value' );
			var segments = $( '#ngl_segments' ).parent().dropdown( 'get value' );
			if ( ( ! lists || lists.length == 0 ) && ( ! segments || segments.length == 0 ) ) {
				ready = false;
				$( '#ngl_lists, #ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			} else {
				$( '#ngl_lists, #ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			}
		}

		// Is form ready?
		if ( ready ) {
			$( '.ngl-ready' ).removeClass( 'is-hidden' );
			$( '.ngl-not-ready' ).addClass( 'is-hidden' );
			$( '.ngl-not-ready' ).parents( '.ngl-metabox-flex.alt3' ).removeClass( 'ngl-unready' );
			$( '.ngl-newsletter-errors' ).remove();
			$( '.ngl-top-checkbox' ).removeClass( 'disable-send' );
		} else {
			$( '.ngl-ready' ).addClass( 'is-hidden' );
			$( '.ngl-not-ready' ).removeClass( 'is-hidden' );
			$( '.ngl-not-ready' ).parents( '.ngl-metabox-flex.alt3' ).addClass( 'ngl-unready' );
			if ( $( '.ngl-newsletter-errors' ).length == 0 ) {
				$( '.edit-post-header__settings' ).prepend( '<span class="ngl-newsletter-errors">' + newsletterglue_params.publish_error + '</span>' );
			}
			$( '.ngl-top-checkbox' ).addClass( 'disable-send' );
			$( '#ngl_double_confirm' ).val( 'no' );
		}
		
		if ( ! $( '#ngl_send_newsletter' ).is( ':checked' ) ) {
			$( '.ngl-newsletter-errors' ).remove();
			$( '.ngl-top-checkbox' ).removeClass( 'disable-send' );
		}
	}

	// validate the email.
	function ngl_validate_email() {

		if ( $( '#ngl_from_email' ).length == 0 ) {
			return false;
		}

		var email_  = $( '#ngl_from_email' );
		var email 	= email_.val();
		var elem    = email_.parent().parent().parent();
		var app 	= $( '#ngl_app' ).val();

		var data = 'action=newsletterglue_ajax_verify_email&security=' + newsletterglue_params.ajaxnonce + '&email=' + email + '&app=' + app;

		if ( elem.parents( '.ngl-metabox-if-checked' ).hasClass( 'ngl-metabox-placeholder' ) ) {
			return false;
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				elem.find( '.ngl-process' ).addClass( 'is-hidden' );
				elem.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				elem.find( '.ngl-label-more' ).empty();
			},
			success: function( response ) {
				console.log( response );
				elem.find( '.ngl-process' ).addClass( 'is-hidden' );
				if ( response.success || response === true ) {
					if ( response.success ) {
						elem.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
						elem.find( '.ngl-process.is-valid .ngl-process-text' ).html( response.success );
					}
					email_.parent().parent().parent().removeClass( 'is-error' );
					email_.attr( 'data-force-unready', '0' );
				} else {
					elem.find( '.ngl-process.is-invalid' ).removeClass( 'is-hidden' );
					elem.find( '.ngl-process.is-invalid .ngl-process-text' ).html( response.failed );
					if ( response.failed_details ) {
						elem.find( '.ngl-label-more' ).html( response.failed_details );
					}
					email_.parent().parent().parent().addClass( 'is-error' );
					email_.attr( 'data-force-unready', '1' );
				}
				ngl_validate_form();
			}
		} );
	}

	// Init fields.
	$( '.ngl .ui.dropdown, .ngl-metabox .ui.dropdown' ).dropdown( { onChange: function() { ngl_validate_form(); } } );
	$( '.ngl .ui.checkbox' ).checkbox();

	// When a list is changed for Campaign Monitor.
	$( document ).on( 'change', '.ngl-modal[data-app=campaignmonitor] #ngl_lists', function() {
		var val = $( this ).val();
		var continuethis = false;
		if ( val && val.length ) {
			continuethis = true;
		} else {
			var next_val = $( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ui' ).dropdown( 'get value' );
			if ( ! next_val || next_val.length == 0 ) {
				continuethis = false
			}
		}
		if ( continuethis ) {
			$( this ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
		} else {
			$( this ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-boarding-next' ).addClass( 'disabled' ).removeClass( 'ready' );
		}
	} );

	// When a segment is changed for Campaign Monitor.
	$( document ).on( 'change', '.ngl-modal[data-app=campaignmonitor] #ngl_segments', function() {
		var val = $( this ).val();
		var continuethis = false;
		if ( val && val.length ) {
			continuethis = true;
		} else {
			var next_val = $( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ui' ).dropdown( 'get value' );
			if ( ! next_val || next_val.length == 0 ) {
				continuethis = false
			}
		}
		if ( continuethis ) {
			$( this ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
			$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
		} else {
			$( this ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-modal[data-app=campaignmonitor] #ngl_lists' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
			$( '.ngl-boarding-next' ).addClass( 'disabled' ).removeClass( 'ready' );
		}
	} );

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

	// When user clicks to add new connection.
	$( document ).on( 'click', '.ngl-card-add', function( event ) {
		$( this ).addClass( 'ngl-hidden' );
		$( '.ngl-card-base' ).removeClass( 'ngl-hidden' );
	} );

	// When a app is selected.
	$( '.ngl-app' ).dropdown( 'setting', 'onChange', function( val ) {
		$( this ).parents( '.ngl-card-base' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-' + val ).removeClass( 'ngl-hidden' );
		ngl_app = val;
	} );

	// Back one screen.
	$( document ).on( 'click', '.ngl-back', function( event ) {
		
		if ( ! ngl_back_screen ) {
			var screen = $( this ).attr( 'data-screen' );
			$( '.ngl-app' ).dropdown( 'clear' );
			$( this ).parent().parent().addClass( 'ngl-hidden' );
			$( '.' + screen ).removeClass( 'ngl-hidden' );
		} else {
			$( this ).parent().parent().addClass( 'ngl-hidden' );
		}
	} );

	// License form.
	$( document ).on( 'submit', '.ngl-license-form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var data 	= theform.serialize() + '&action=newsletterglue_check_license&security=' + newsletterglue_params.ajaxnonce;

		var stop_form = false;
		theform.find( 'input[type=text]:visible' ).each( function() {
			if ( $( this ).val() == '' ) {
				$( this ).addClass( 'error' ).focus();
				stop_form = true;
			}
		} );

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
				
				console.log( result );

				setTimeout( function() {
					if ( result.status === 'invalid' ) {
						theform.parents( '.ngl-cards' ).find( '.ngl-card-state.is-invalid .ngl-card-state-text' ).html( result.message );
						ngl_show_not_connected_screen();
					}
					if ( result.status === 'valid' ) {
						ngl_show_connected_screen();
					}
				}, 1000 );

			},
			error: function() {
				ngl_show_not_connected_screen();
			}
		} );

		return false;
	} );

	// Connection form.
	$( document ).on( 'submit', '.ngl-fields form', function( event ) {
		event.preventDefault();

		var theform = $( this );
		var app 	= $( this ).parents( '.ngl-card-add2' ).attr( 'data-app' );
		var data 	= theform.serialize() + '&action=newsletterglue_ajax_connect_api&security=' + newsletterglue_params.ajaxnonce + '&app=' + app;

		var stop_form = false;
		if ( ! $( '.ngl-card-' + app ).hasClass( 'ngl-hidden' ) ) {
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
				console.log( result );
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

		ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );

		if ( ngl_app ) {
			$( '.ngl-card-add2.ngl-card-' + ngl_app + ' .ngl-fields form' ).trigger( 'submit' );
		} else {
			$( '.ngl-card-add2 .ngl-license-form' ).trigger( 'submit' );
		}

		return false;

	} );

	// Test again.
	$( document ).on( 'click', '.ngl-ajax-test-again', function( event ) {
		event.preventDefault();
		if ( $( '.ngl-card-add2 .ngl-fields form' ).length ) {
			$( '.ngl-card-add2 .ngl-fields form' ).trigger( 'submit' );
		}
		if ( $( '.ngl-card-add2 .ngl-license-form' ).length ) {
			$( '.ngl-card-add2 .ngl-license-form' ).trigger( 'submit' );
		}
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
			ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );
			ngl_back_screen = $( this ).parents( '.ngl-card-view-' + ngl_app );
		}
		if ( ngl_app ) {
			$( '.ngl-card-add2.ngl-card-' + ngl_app ).removeClass( 'ngl-hidden' );
		} else {
			$( '.ngl-card-add2.ngl-hidden' ).removeClass( 'ngl-hidden' );
		}
		return false;
	} );

	// Remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove-connection', function( event ) {
		event.preventDefault();
		ngl_app = $( this ).parents( '.ngl-card-view' ).attr( 'data-app' );
		$( '.ngl-ajax-remove' ).attr( 'data-ngl_app', ngl_app );
		$( '.ngl-card-state.confirm-remove' ).removeClass( 'ngl-hidden' );
		return false;
	} );

	// Confirm remove connection.
	$( document ).on( 'click', '.ngl-ajax-remove', function( event ) {
		event.preventDefault();

		$( '.ngl-card-state.confirm-remove' ).addClass( 'ngl-hidden' );
		$( '.ngl-card-state.is-removed' ).removeClass( 'ngl-hidden' );
		$( '.ngl-app' ).dropdown( 'clear' );

		var app = $( this ).attr( 'data-ngl_app' );
		if ( app ) {
			var action = 'newsletterglue_ajax_remove_api';
		} else {
			var action = 'newsletterglue_deactivate_license';
		}

		if ( app ) {
			var data = 'action=' + action + '&security=' + newsletterglue_params.ajaxnonce + '&app=' + app;
		} else {
			var data = 'action=' + action + '&security=' + newsletterglue_params.ajaxnonce;
		}

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
		ngl_validate_form();
		if ( ! $( this ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter2' ).prop( 'checked', false );
		} else {
			$( '.ngl-top-checkbox' ).removeClass( 'is-hidden' );
		}
	} );

	// Toggle for top send newsletter checkbox.
	$( document ).on( 'change', '#ngl_send_newsletter2', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter' ).prop( 'checked', true ).trigger( 'change' );
		} else {
			$( '#ngl_send_newsletter' ).prop( 'checked', false ).trigger( 'change' );
		}
	} );

	// Revalidate email.
	$( document ).on( 'change', '#newsletter_glue_metabox #ngl_from_email', function() {
		ngl_validate_email();
	} );

	// Run form validation when user edit metabox fields.
	$( document ).on( 'change', '.ngl-metabox input[type=text]', function() {
		ngl_validate_form();
	} );

	// Copy post title into newsletter subject.
	$( document ).on( 'blur', '.editor-post-title__input', function() {
		if ( $( this ).val() ) {
			if ( $( '#ngl_subject' ).val() == '' ) {
				$( '#ngl_subject' ).val( $( this ).val() ).trigger( 'change' );
			}
		}
	} );

	// Review button.
	$( document ).on( 'click', '.ngl-review-link', function( event ) {

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_clicked_review_button&security=' + newsletterglue_params.ajaxnonce
		} );

	} );

	// Reset newsletter.
	$( document ).on( 'click', '.ngl-reset-newsletter', function( event ) {
		event.preventDefault();

		var el = $( this );
		var post_id = $( this ).attr( 'data-post_id' );

		var data = 'action=newsletterglue_ajax_reset_newsletter&security=' + newsletterglue_params.ajaxnonce + '&post_id=' + post_id;

		$( '#ngl_double_confirm' ).val( 'no' );

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
			data = data + '&' + $( this ).attr( 'id' ) + '=' + encodeURIComponent( $( this ).val() );
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
				console.log( response );
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
	$( document ).on( 'click', 'a[href="#ngl-status-log"]', function( event ) {
		event.preventDefault();
		var trigger = $( this );
		var post_id = $( this ).attr( 'data-post-id' );
		ngl_open_modal( trigger );
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

		// Add message box.
		if ( $( '#ngl_send_newsletter2' ).is( ':checked' ) ) {
			$( '#ngl_send_newsletter, #ngl_send_newsletter2' ).prop( 'checked', false );
			$( '.ngl-msgbox-wrap' ).removeClass( 'is-hidden' );
			$( '.ngl-reset' ).addClass( 'is-hidden' );
			$( '.ngl-top-checkbox' ).addClass( 'is-hidden' );
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
	$( document ).on( 'change', '.ngl-settings input[type=text], .ngl-settings textarea, .ngl-settings input[type=checkbox], .ngl-settings select, .ngl-boarding .ngl-metabox-segment select', function() {

		if ( $( 'body' ).find( '.ngl-theme' ).length ) {
			return;
		}

		var el 		= $( this ).closest( '.ngl-metabox-flex' );
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

		value = encodeURIComponent( value );

		var data = 'action=newsletterglue_ajax_save_field&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			beforeSend: function() {
				el.find( '.ngl-process' ).addClass( 'is-hidden' );
				el.find( '.ngl-process.is-waiting' ).removeClass( 'is-hidden' );
				el.find( '.ngl-label-more' ).empty();
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
					el.addClass( 'is-error' );
					if ( response.failed_details ) {
						el.find( '.ngl-label-more' ).html( response.failed_details );
					}
				} else if ( response.success ) {
					el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					el.find( '.ngl-process.is-valid .ngl-process-text' ).html( response.success );
					el.removeClass( 'is-error' );
				} else {
					el.removeClass( 'is-error' );
					el.find( '.ngl-process.is-valid' ).removeClass( 'is-hidden' );
					setTimeout( function() {
						el.find( '.ngl-process' ).addClass( 'is-hidden' );
					}, 1500 );

				}

				if ( ! el.hasClass( 'is-error' ) ) {
					if ( id == 'ngl_from_email' ) {
						if ( $( '.ngl-boarding' ).length ) {
							setTimeout( function() {
								$( '.ngl-boarding-next' ).removeClass( 'disabled' ).addClass( 'ready' );
							}, 1500 );
						}
					}
				}

				var modal = $( '.ngl-modal[data-app=campaignmonitor]:visible' );
				if ( modal.length ) {
					var selectedLists = modal.find( '#ngl_lists' ).parents( '.ui' ).dropdown( 'get value' );
					var selectedSegments = modal.find( '#ngl_segments' ).parents( '.ui' ).dropdown( 'get value' );
					if ( ( ! selectedLists || selectedLists.length == 0 ) && ( ! selectedSegments || selectedSegments.length == 0 ) ) {
						modal.find( '#ngl_lists' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
						modal.find( '#ngl_segments' ).parents( '.ngl-metabox-flex' ).addClass( 'is-error' );
					} else {
						modal.find( '#ngl_lists' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
						modal.find( '#ngl_segments' ).parents( '.ngl-metabox-flex' ).removeClass( 'is-error' );
					}
				}

			}
		} );

	} );

	// Use all blocks.
	$( document ).on( 'click', '.ngl-block-useall', function( event ) {
		event.preventDefault();

		$( '.ngl-block' ).each( function() {
			$( this ).removeClass( 'ngl-block-unused' ).addClass( 'ngl-block-used' );
			$( this ).find( 'input[type=checkbox]' ).prop( 'checked', true );
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_use_all_blocks&security=' + newsletterglue_params.ajaxnonce
		} );

		return false;
	} );

	// Disable all blocks.
	$( document ).on( 'click', '.ngl-block-disableall', function( event ) {
		event.preventDefault();

		$( '.ngl-block' ).each( function() {
			$( this ).removeClass( 'ngl-block-used' ).addClass( 'ngl-block-unused' );
			$( this ).find( 'input[type=checkbox]' ).prop( 'checked', false );
		} );

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_disable_all_blocks&security=' + newsletterglue_params.ajaxnonce
		} );

		return false;
	} );

	// Change block state.
	$( document ).on( 'change', '.ngl-block-use input[type=checkbox]', function( event ) {

		var id = $( this ).attr( 'id' );

		if ( $( this ).is( ':checked' ) ) {
			var value = 'yes';
			$( this ).parents( '.ngl-block' ).removeClass( 'ngl-block-unused' ).addClass( 'ngl-block-used' );
		} else {
			var value = 'no';
			$( this ).parents( '.ngl-block' ).removeClass( 'ngl-block-used' ).addClass( 'ngl-block-unused' );
		}

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : 'action=newsletterglue_ajax_use_block&security=' + newsletterglue_params.ajaxnonce + '&id=' + id + '&value=' + value
		} );

	} );

	// Open block demo.
	$( document ).on( 'click', '.ngl-block-demo', function( event ) {
		event.preventDefault();
		var block_id = $( this ).parents( '.ngl-block' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).addClass( 'ngl-active' );
		$( 'body' ).addClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ).empty();
		$( '.ngl-popup-demo[data-block=' + block_id + ']' ).appendTo( $( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ) );
		$( '.ngl-popup-overlay.ngl-active' ).addClass( 'ngl-popup-overlay-demo' );
		$( '.ngl-popup-overlay.ngl-active' ).removeClass( 'ngl-popup-overlay-settings' );
		return false;
	} );

	// Open block defaults.
	$( document ).on( 'click', '.ngl-block-defaults a', function( event ) {
		event.preventDefault();
		var block_id = $( this ).parents( '.ngl-block' ).attr( 'data-block' );
		$( '.ngl-popup-overlay' ).addClass( 'ngl-active' );
		$( 'body' ).addClass( 'ngl-popup-hidden' );
		$( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ).empty();
		$( '.ngl-popup-settings[data-block=' + block_id + ']' ).appendTo( $( '.ngl-popup-overlay.ngl-active' ).find( '.ngl-popup-panel' ) );
		$( '.ngl-popup-overlay.ngl-active' ).removeClass( 'ngl-popup-overlay-demo' );
		$( '.ngl-popup-overlay.ngl-active' ).addClass( 'ngl-popup-overlay-settings' );
		return false;
	} );

	// Close popup.
	$( document ).on( 'click', '.ngl-popup-panel', function( event ) {
		event.stopPropagation();
	} );

	// Close popup.
	$( document ).on( 'click', function() {
		ngl_close_popup();
	} );

	// Close popup with icon.
	$( document ).on( 'click', '.ngl-popup-close', function( event ) {
		event.preventDefault();
		ngl_close_popup();
	} );

	$( document ).on( 'click', '.ngl-edit-more a', function( event ) {
		event.preventDefault();
		var more = $( '.ngl-edit-more-box' );
		if ( more.hasClass( 'is-hidden' ) ) {
			more.removeClass( 'is-hidden' );
			$( this ).find( 'i' ).removeClass( 'down' ).addClass( 'up' );
		} else {
			more.addClass( 'is-hidden' );
			$( this ).find( 'i' ).addClass( 'down' ).removeClass( 'up' );
		}
		return false;
	} );

	// When block defaults are changed.
	$( document ).on( 'change', '.ngl-popup-settings input[type=checkbox]', function( event ) {

		var id  	= $( this ).parents( '.ngl-popup-settings' ).attr( 'data-block' );
		var data 	= $( this ).parents( 'form' ).serialize() + '&action=newsletterglue_ajax_save_block&security=' + newsletterglue_params.ajaxnonce + '&id=' + id;

		$.ajax( {
			type : 'post',
			url : newsletterglue_params.ajaxurl,
			data : data,
			success: function( response ) {

			}
		} );

	} );
	
	$( document ).on( 'change', '.ngl-settings-mailchimp #ngl_audience, .ngl-mb-mailchimp #ngl_audience', function( event ) {

	} );

	// Show top toolbar checkbox.
	$( window ).on( 'load', function() {
		if ( $( '#ngl_send_newsletter' ).length ) {
			$( '.edit-post-header__settings' ).prepend( '<div class="ngl-top-checkbox"><label><input type="checkbox" name="ngl_send_newsletter2" id="ngl_send_newsletter2" value="1">' + newsletterglue_params.send_newsletter + '</label></div>' );
		}
		ngl_validate_email();
	} );

} ) ( jQuery );
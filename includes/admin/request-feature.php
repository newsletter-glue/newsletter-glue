<?php
/**
 * Request Feature.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_feature_nonce = wp_create_nonce( 'newsletterglue-feature-nonce' ); ?>

<div class="newsletterglue-popup-overlay" data-overlay="feature-request">

	<div class="newsletterglue-serveypanel">
		<form action="#" method="post" id="newsletterglue-feature-request-form">
			<div class="newsletterglue-popup-header">
				<h2><?php _e( 'Request feature', 'newsletter-glue' ); ?></h2>
			</div>
			<div class="newsletterglue-popup-body">

				<input type="hidden" class="newsletterglue_feature_nonce" name="newsletterglue_feature_nonce" value="<?php echo $newsletterglue_feature_nonce; ?>">

				<div class="newsletterglue-popup-field">
					<label for="_feature_details"><?php _e( 'What feature would you like, and what will it help you do?*', 'newsletter-glue' ); ?></label>
					<textarea name="_feature_details" id="_feature_details" required ></textarea>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_feature_name"><?php _e( 'Name*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_feature_name" id="_feature_name" required />
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_feature_email"><?php _e( 'Email*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_feature_email" id="_feature_email" required />
				</div>

			</div>
			<div class="newsletterglue-popup-footer">
				<div class="action-btns">
					<span class="newsletterglue-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
					<input type="submit" class="button button-primary button-feature-request newsletterglue-popup-allow-feature-request" value="<?php _e( 'Submit', 'newsletter-glue' ); ?>">
					<a href="#" class="button button-secondary newsletterglue-popup-button-close"><?php _e( 'Cancel', 'newsletter-glue' ); ?></a>
				</div>
			</div>
		</form>
	</div>

</div>

<script>
(function( $ ) {

$(function() {

	$(document).on('click', '.ngl-request-feature', function(e){
		e.preventDefault();
		$('.newsletterglue-popup-overlay[data-overlay="feature-request"]').addClass('newsletterglue-active');
		$('body').addClass('newsletterglue-hidden');
		return false;
	});

	$(document).on('click', '.newsletterglue-popup-button-close', function () {
		close_popup();
	});

	$(document).on('click', ".newsletterglue-serveypanel",function(e){
		e.stopPropagation();
	});

	$( document ).on( 'click', function() {
		close_popup();
	} );

	function close_popup() {
		$('.newsletterglue-popup-overlay[data-overlay="feature-request"]').removeClass('newsletterglue-active');
		$('body').removeClass('newsletterglue-hidden');
	}

	$(document).on('submit', '#newsletterglue-feature-request-form', function(event) {
		event.preventDefault();

		var theform =  $( this );

		var data = theform.serialize() + '&action=newsletterglue_feature_request&security=' + $('.newsletterglue_feature_nonce').val();

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function(){
				$(".newsletterglue-spinner").show();
				$( '.newsletterglue-popup-allow-feature-request' ).attr( 'disabled', 'disabled' );
			}
		}).done(function() {
            $(".newsletterglue-spinner").hide();
			$( '.newsletterglue-popup-allow-feature-request' ).removeAttr( 'disabled' );
			theform.find( 'input[type=text], select, textarea' ).val( '' );
			close_popup();
		});

	});

});

})( jQuery );
</script>
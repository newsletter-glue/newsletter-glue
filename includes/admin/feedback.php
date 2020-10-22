<?php
/**
 * Plugin Deactivation.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_feedback_nonce = wp_create_nonce( 'newsletterglue-feedback-nonce' ); ?>

<div class="newsletterglue-popup-overlay" data-overlay="feedback">

	<div class="newsletterglue-serveypanel">
		<form action="#" method="post" id="newsletterglue-feedback-form">
			<div class="newsletterglue-popup-header">
				<h2><?php _e( 'Vote for new connection', 'newsletter-glue' ); ?></h2>
			</div>
			<div class="newsletterglue-popup-body">

				<input type="hidden" class="newsletterglue_feedback_nonce" name="newsletterglue_feedback_nonce" value="<?php echo $newsletterglue_feedback_nonce; ?>">

				<div class="newsletterglue-popup-field">
					<label for="_software"><?php _e( 'Select email software*', 'newsletter-glue' ); ?></label>
					<select name="_software" id="_software" required>
						<option value=""></option>
						<option value="Aweber">Aweber</option>
						<option value="ActiveCampaign">ActiveCampaign</option>
						<option value="Constant Contact">Constant Contact</option>
						<option value="Drip">Drip</option>
						<option value="EmailOctopus">EmailOctopus</option>
						<option value="GetResponse">GetResponse</option>
						<option value="Klaviyo">Klaviyo</option>
						<option value="MailPoet">MailPoet</option>
						<option value="Mailjet">Mailjet</option>
						<option value="Mailster">Mailster</option>
						<option value="Sendy">Sendy</option>
						<option value="Something else">Something else</option>
					</select>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_details"><?php _e( 'Share more details. Help us better understand your needs. (optional)', 'newsletter-glue' ); ?></label>
					<textarea name="_details" id="_details" placeholder="<?php esc_attr_e( 'Your message...', 'newsletter-glue' ); ?>"></textarea>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_name"><?php _e( 'Name*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_name" id="_name" required />
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_email"><?php _e( 'Email*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_email" id="_email" required />
				</div>

			</div>
			<div class="newsletterglue-popup-footer">
				<div class="action-btns">
					<span class="newsletterglue-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
					<input type="submit" class="button button-primary button-feedback newsletterglue-popup-allow-feedback" value="<?php _e( 'Submit', 'newsletter-glue' ); ?>">
					<a href="#" class="button button-secondary newsletterglue-popup-button-close"><?php _e( 'Cancel', 'newsletter-glue' ); ?></a>
				</div>
			</div>
		</form>
	</div>

</div>

<script>
(function( $ ) {

$(function() {

	$(document).on('click', '.ngl-request-modal', function(e){
		e.preventDefault();
		$('.newsletterglue-popup-overlay[data-overlay="feedback"]').addClass('newsletterglue-active');
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
		$('.newsletterglue-popup-overlay[data-overlay="feedback"]').removeClass('newsletterglue-active');
		$('body').removeClass('newsletterglue-hidden');
	}

	$(document).on('submit', '#newsletterglue-feedback-form', function(event) {
		event.preventDefault();

		var theform =  $( this );

		var data = theform.serialize() + '&action=newsletterglue_send_feedback&security=' + $('.newsletterglue_feedback_nonce').val();

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function(){
				$(".newsletterglue-spinner").show();
				$( '.newsletterglue-popup-allow-feedback' ).attr( 'disabled', 'disabled' );
			}
		}).done(function() {
            $(".newsletterglue-spinner").hide();
			$( '.newsletterglue-popup-allow-feedback' ).removeAttr( 'disabled' );
			theform.find( 'input[type=text], select, textarea' ).val( '' );
			close_popup();
		});

	});

});

})( jQuery );
</script>
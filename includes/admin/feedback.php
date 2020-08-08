<?php
/**
 * Plugin Deactivation.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_feedback_nonce = wp_create_nonce( 'newsletterglue-feedback-nonce' ); ?>

<style>

.newsletterglue-hidden {
	overflow: hidden;
}

.newsletterglue-popup-overlay .newsletterglue-internal-message {
	margin: 3px 0 3px 22px;
	display: none;
}

.newsletterglue-reason-input {
	margin: 6px 0 3px 32px;
	display: none;
}

.newsletterglue-reason-input input[type="text"] {
	width: 100%;
	display: block;
}

.newsletterglue-popup-overlay {
	background: rgba(0,0,0, .8);
	position: fixed;
	top:0;
	left: 0;
	height: 100%;
	width: 100%;
	z-index: 1000000;
	overflow: auto;
	visibility: hidden;
	opacity: 0;
	transition: opacity 0.3s ease-in-out;
	display: flex;
	justify-content: center;
	align-items: center;
}

.newsletterglue-popup-overlay.newsletterglue-active {
	opacity: 1;
	visibility: visible;
}

.newsletterglue-serveypanel {
	width: 600px;
	background: #fff;
	margin: 0 auto 0;
	border-radius: 3px;
}

.newsletterglue-popup-header {
	background: #f1f1f1;
	padding: 20px;
	border-bottom: 1px solid #ccc;
}

.newsletterglue-popup-header h2 {
	margin: 0;
	background: url( <?php echo NGL_PLUGIN_URL . 'assets/images/menu.png'; ?> ) no-repeat left center;
	background-size: 24px 24px;
	min-height: 24px;
	padding-left: 36px;
	line-height: 22px;
}

.newsletterglue-popup-body {
	padding: 10px 20px;
}

.newsletterglue-popup-footer {
	background: #f9f3f3;
	padding: 10px 20px;
	border-top: 1px solid #ccc;
}

.newsletterglue-popup-footer:after {
	content:"";
	display: table;
	clear: both;
}

.action-btns {
	float: right;
}

.newsletterglue-anonymous {
	display: none;
}

.attention, .error-message {
	color: red;
	font-weight: 600;
	display: none;
}

.newsletterglue-spinner {
	display: none;
	position: relative;
    top: 2px;
	margin: 0 10px 0 0;
}

.newsletterglue-spinner img {
	margin-top: 3px;
}

.newsletterglue-pro-message {
	padding-left: 24px;
	color: red;
	font-weight: 600;
	display: none;
}

.newsletterglue-popup-header {
	background: none;
	padding: 18px 30px;
	-webkit-box-shadow: 0 0 8px rgba(0,0,0,.1);
	box-shadow: 0 0 8px rgba(0,0,0,.1);
	border: 0;
}

.newsletterglue-popup-body h3 {
	margin-top: 0;
	margin-bottom: 20px;
	font-weight: 700;
	font-size: 15px;
	color: #495157;
	line-height: 1.4;
}

.newsletterglue-reason {
	font-size: 13px;
	color: #6d7882;
	margin-bottom: 15px;
}

.newsletterglue-reason input[type="radio"] {
	margin-right: 15px;
}

.newsletterglue-popup-body {
	padding: 30px 30px 0;
}

.newsletterglue-popup-footer {
	background: none;
	border: 0;
	padding: 30px;
}

.newsletterglue-popup-body h4 {
	margin-bottom: 10px;
}

.newsletterglue-popup-body textarea {
	width: 100%;
	box-sizing: border-box;
	height: 60px;
	padding: 6px 8px;
}

.newsletterglue-popup-field {
	padding: 10px 0;
}

.newsletterglue-popup-field label {
	font-size: 1.1em;
	display: block;
	margin: 0 0 8px 0;
}

.newsletterglue-popup-field select,
.newsletterglue-popup-field input[type=text] {
	width: 100%;
	max-width: 100%;
	min-height: 40px;
}

</style>

<div class="newsletterglue-popup-overlay">

	<div class="newsletterglue-serveypanel">
		<form action="#" method="post" id="newsletterglue-feedback-form">
			<div class="newsletterglue-popup-header">
				<h2><?php _e( 'Vote for new connection', 'newsletter-glue' ); ?></h2>
			</div>
			<div class="newsletterglue-popup-body">

				<input type="hidden" class="newsletterglue_feedback_nonce" name="newsletterglue_feedback_nonce" value="<?php echo $newsletterglue_feedback_nonce; ?>">

				<div class="newsletterglue-popup-field">
					<label for="_software"><?php _e( 'Select email software *', 'newsletter-glue' ); ?></label>
					<select name="_software" id="_software" required>
						<option value=""></option>
						<option value="Aweber">Aweber</option>
						<option value="ActiveCampaign">ActiveCampaign</option>
						<option value="Campaign Monitor">Campaign Monitor</option>
						<option value="Constant Contact">Constant Contact</option>
						<option value="Drip">Drip</option>
						<option value="EmailOctopus">EmailOctopus</option>
						<option value="GetResponse">GetResponse</option>
						<option value="Klaviyo">Klaviyo</option>
						<option value="MailPoet">MailPoet</option>
						<option value="Mailjet">Mailjet</option>
						<option value="Mailster">Mailster</option>
						<option value="Sendinblue">Sendinblue</option>
						<option value="Sendy">Sendy</option>
						<option value="Something else">Something else</option>
					</select>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_details"><?php _e( 'Share more details. Help us better understand your needs. (optional)', 'newsletter-glue' ); ?></label>
					<textarea name="_details" id="_details" placeholder="<?php esc_attr_e( 'Your message...', 'newsletter-glue' ); ?>"></textarea>
				</div>

				<div class="newsletterglue-popup-field">
					<input type="text" name="_name" id="_name" placeholder="<?php esc_attr_e( 'Name (required)', 'newsletter-glue' ); ?>" required />
				</div>

				<div class="newsletterglue-popup-field">
					<input type="text" name="_email" id="_email" placeholder="<?php esc_attr_e( 'Email (required)', 'newsletter-glue' ); ?>" required />
				</div>

			</div>
			<div class="newsletterglue-popup-footer">
				<div class="action-btns">
					<span class="newsletterglue-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
					<input type="submit" class="button button-primary button-feedback newsletterglue-popup-allow-feedback" value="<?php _e( 'Vote', 'newsletter-glue' ); ?>">
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
		$('.newsletterglue-popup-overlay').addClass('newsletterglue-active');
		$('body').addClass('newsletterglue-hidden');
		return false;
	});

	$(document).on('click', '.newsletterglue-popup-button-close', function () {
		close_popup();
	});

	$(document).on('click', ".newsletterglue-serveypanel",function(e){
		e.stopPropagation();
	});

	$(document).click(function(){
		close_popup();
	});

	function close_popup() {
		$('.newsletterglue-popup-overlay').removeClass('newsletterglue-active');
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
<?php
/**
 * Bug report.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_bug_nonce = wp_create_nonce( 'newsletterglue-bug-nonce' ); ?>

<div class="newsletterglue-popup-overlay" data-overlay="bug-report">

	<div class="newsletterglue-serveypanel">
		<form action="#" method="post" id="newsletterglue-bug-report-form">
			<div class="newsletterglue-popup-header">
				<h2><?php _e( 'Report bug', 'newsletter-glue' ); ?></h2>
			</div>
			<div class="newsletterglue-popup-body">

				<input type="hidden" class="newsletterglue_bug_nonce" name="newsletterglue_bug_nonce" value="<?php echo $newsletterglue_bug_nonce; ?>">

				<div class="newsletterglue-popup-field">
					<label for="_bug_details"><?php _e( 'What bug did you find?*', 'newsletter-glue' ); ?></label>
					<textarea name="_bug_details" id="_bug_details" placeholder="<?php esc_attr_e( 'Where did you find the bug? What did the bug cause? What happened on your site?', 'newsletter-glue' ); ?>" required ></textarea>
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_bug_name"><?php _e( 'Name*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_bug_name" id="_bug_name" required />
				</div>

				<div class="newsletterglue-popup-field">
					<label for="_bug_email"><?php _e( 'Email*', 'newsletter-glue' ); ?></label>
					<input type="text" name="_bug_email" id="_bug_email" required />
				</div>

			</div>
			<div class="newsletterglue-popup-footer">
				<div class="action-btns">
					<span class="newsletterglue-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
					<input type="submit" class="button button-primary button-bug-report newsletterglue-popup-allow-bug-report" value="<?php _e( 'Submit', 'newsletter-glue' ); ?>">
					<a href="#" class="button button-secondary newsletterglue-popup-button-close"><?php _e( 'Cancel', 'newsletter-glue' ); ?></a>
				</div>
			</div>
		</form>
	</div>

</div>

<script>
(function( $ ) {

$(function() {

	$(document).on('click', '.ngl-bug-report', function(e){
		e.preventDefault();
		$('.newsletterglue-popup-overlay[data-overlay="bug-report"]').addClass('newsletterglue-active');
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
		$('.newsletterglue-popup-overlay[data-overlay="bug-report"]').removeClass('newsletterglue-active');
		$('body').removeClass('newsletterglue-hidden');
	}

	$(document).on('submit', '#newsletterglue-bug-report-form', function(event) {
		event.preventDefault();

		var theform =  $( this );

		var data = theform.serialize() + '&action=newsletterglue_bug_report&security=' + $('.newsletterglue_bug_nonce').val();

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function(){
				$(".newsletterglue-spinner").show();
				$( '.newsletterglue-popup-allow-bug-report' ).attr( 'disabled', 'disabled' );
			}
		}).done(function() {
            $(".newsletterglue-spinner").hide();
			$( '.newsletterglue-popup-allow-bug-report' ).removeAttr( 'disabled' );
			theform.find( 'input[type=text], select, textarea' ).val( '' );
			close_popup();
		});

	});

});

})( jQuery );
</script>
<?php
/**
 * Plugin Deactivation.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$newsletterglue_deactivate_nonce = wp_create_nonce( 'newsletterglue-deactivate-nonce' ); ?>

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

</style>

<div class="newsletterglue-popup-overlay">

  <div class="newsletterglue-serveypanel">
    <form action="#" method="post" id="newsletterglue-deactivate-form">
    <div class="newsletterglue-popup-header">
      <h2><?php _e( 'Quick feedback about Newsletter Glue', 'newsletter-glue' ); ?></h2>
    </div>
    <div class="newsletterglue-popup-body">
      <h3><?php _e( 'If you have a moment, please let us know why you are deactivating:', 'newsletter-glue' ); ?></h3>
      <input type="hidden" class="newsletterglue_deactivate_nonce" name="newsletterglue_deactivate_nonce" value="<?php echo $newsletterglue_deactivate_nonce; ?>">
      <ul id="newsletterglue-reason-list">
        <li class="newsletterglue-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="1">
            </span>
            <span><?php _e( 'I only needed the plugin for a short period', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
        </li>
        <li class="newsletterglue-reason has-input" data-input-type="textfield">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="2">
            </span>
            <span><?php _e( 'I found a better plugin', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
          <div class="newsletterglue-reason-input"><span class="message error-message "><?php _e( 'Kindly tell us the Plugin name.', 'newsletter-glue' ); ?></span><input type="text" name="better_plugin" placeholder="What's the plugin's name?"></div>
        </li>
        <li class="newsletterglue-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="3">
            </span>
            <span><?php _e( 'The plugin broke my site', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
        </li>
        <li class="newsletterglue-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="4">
            </span>
            <span><?php _e( 'The plugin suddenly stopped working', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
        </li>
        <li class="newsletterglue-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="5">
            </span>
            <span><?php _e( 'I no longer need the plugin', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
        </li>
        <li class="newsletterglue-reason" data-input-type="" data-input-placeholder="">
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="6">
            </span>
            <span><?php _e( "It's a temporary deactivation. I'm just debugging an issue.", 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
        </li>
        <li class="newsletterglue-reason has-input" data-input-type="textfield" >
          <label>
            <span>
              <input type="radio" name="newsletterglue-selected-reason" value="7">
            </span>
            <span><?php _e( 'Other', 'newsletter-glue' ); ?></span>
          </label>
          <div class="newsletterglue-internal-message"></div>
          <div class="newsletterglue-reason-input"><span class="message error-message "><?php _e( 'Kindly tell us the reason so we can improve.', 'newsletter-glue' ); ?></span><input type="text" name="other_reason" placeholder="Kindly tell us the reason so we can improve."></div>
        </li>
      </ul>

		<h4><?php _e( 'Have more feedback about the plugin? Don&rsquo;t hold back.', 'newsletter-glue' ); ?></h4>
		<textarea name="newsletterglue-feedback" id="newsletterglue-feedback"></textarea>
		<br /><a href="mailto:hi@memberhero.pro"><?php _e( 'Or email us for support.', 'newsletter-glue' ); ?></a>

    </div>
    <div class="newsletterglue-popup-footer">
      <label class="newsletterglue-anonymous"><input type="checkbox" /><?php _e( 'Anonymous feedback', 'newsletter-glue' ); ?></label>
        <input type="button" class="button button-secondary button-skip newsletterglue-popup-skip-feedback" value="<?php _e( 'Skip & Deactivate', 'newsletter-glue' ); ?>" >
      <div class="action-btns">
        <span class="newsletterglue-spinner"><img src="<?php echo admin_url( '/images/spinner.gif' ); ?>" alt=""></span>
        <input type="submit" class="button button-secondary button-deactivate newsletterglue-popup-allow-deactivate" value="<?php _e( 'Submit & Deactivate', 'newsletter-glue' ); ?>" disabled="disabled">
        <a href="#" class="button button-primary newsletterglue-popup-button-close"><?php _e( 'Cancel', 'newsletter-glue' ); ?></a>

      </div>
    </div>
  </form>
    </div>

</div>

<script>
(function( $ ) {

$(function() {

	var plugin_ = '<?php echo plugin_basename( NGL_PLUGIN_FILE ); ?>';

	$(document).on('click', 'tr[data-plugin="' + plugin_ + '"] .deactivate', function(e){
		e.preventDefault();
		$('.newsletterglue-popup-overlay').addClass('newsletterglue-active');
		$('body').addClass('newsletterglue-hidden');
	});

	$(document).on('click', '.newsletterglue-popup-button-close', function () {
		close_popup();
	});

	$(document).on('click', ".newsletterglue-serveypanel,tr[data-plugin='" + plugin_ + "'] .deactivate",function(e){
		e.stopPropagation();
	});

	$(document).click(function(){
		close_popup();
	});

	$('.newsletterglue-reason label').on('click', function(){
		if ( $(this).find('input[type="radio"]').is(':checked') ) {
			$(this).next().next('.newsletterglue-reason-input').show().end().end().parent().siblings().find('.newsletterglue-reason-input').hide();
		}
	});

	$('input[type="radio"][name="newsletterglue-selected-reason"]').on('click', function(event) {
		$(".newsletterglue-popup-allow-deactivate").removeAttr('disabled');
		$(".newsletterglue-popup-skip-feedback").removeAttr('disabled');
		$('.message.error-message').hide();
		$('.newsletterglue-pro-message').hide();
	});

	$('.newsletterglue-reason-pro label').on('click', function(){
		if( $(this).find('input[type="radio"]').is(':checked')){
			$(this).next('.newsletterglue-pro-message').show().end().end().parent().siblings().find('.newsletterglue-reason-input').hide();
			$(this).next('.newsletterglue-pro-message').show()
			$('.newsletterglue-popup-allow-deactivate').attr('disabled', 'disabled');
			$('.newsletterglue-popup-skip-feedback').attr('disabled', 'disabled');
		}
	});

	$(document).on('submit', '#newsletterglue-deactivate-form', function(event) {
		event.preventDefault();

		var _reason =  $('input[type="radio"][name="newsletterglue-selected-reason"]:checked').val();
		var _reason_details = '';
		var _feedback = $( '#newsletterglue-feedback' ).val();

		var deactivate_nonce = $('.newsletterglue_deactivate_nonce').val();

		if ( _reason == 2 ) {
			_reason_details = $("input[type='text'][name='better_plugin']").val();
		} else if ( _reason == 7 ) {
			_reason_details = $("input[type='text'][name='other_reason']").val();
		}

		if ( ( _reason == 7 || _reason == 2 ) && _reason_details == '' ) {
			$('.message.error-message').show();
			return;
		}

		var data = {
			action        : 'newsletterglue_deactivate',
			reason        : _reason,
			reason_detail : _reason_details,
			feedback		: _feedback,
			security      : deactivate_nonce
		};

		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			beforeSend: function(){
				$(".newsletterglue-spinner").show();
				$(".newsletterglue-popup-allow-deactivate").attr("disabled", "disabled");
			}
		}).done(function() {
            $(".newsletterglue-spinner").hide();
            $(".newsletterglue-popup-allow-deactivate").removeAttr("disabled");
            window.location.href =  $("tr[data-plugin='"+ plugin_ +"'] .deactivate a").attr('href');
		});

	});

	$('.newsletterglue-popup-skip-feedback').on('click', function(e){
		window.location.href =  $("tr[data-plugin='"+ plugin_ +"'] .deactivate a").attr('href');
	});

	function close_popup() {
		$('.newsletterglue-popup-overlay').removeClass('newsletterglue-active');
		$('#newsletterglue-deactivate-form').trigger("reset");
		$(".newsletterglue-popup-allow-deactivate").attr('disabled', 'disabled');
		$(".newsletterglue-reason-input").hide();
		$('body').removeClass('newsletterglue-hidden');
		$('.message.error-message').hide();
		$('.newsletterglue-pro-message').hide();
	}

});

})( jQuery );
</script>
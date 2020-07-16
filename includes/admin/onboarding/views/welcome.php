<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding" data-screen="1">

	<div class="ngl-boarding-logo">
		<div class="ngl-logo"></div>
		<div class="ngl-label"><?php _e( '<span>Newsletter</span> Glue', 'newsletter-glue' ); ?></div>
	</div>

	<p>👋</p>
	<p><?php _e( 'Hi friend,', 'newsletter-glue' ); ?></p>
	<p><?php _e( 'Ready to send a post to your subscribers?', 'newsletter-glue' ); ?></p>
	<p><?php _e( 'Let&rsquo;s get you started!', 'newsletter-glue' ); ?></p>

	<div class="ngl-boarding-btn ngl-btn">
		<button class="ui primary button ngl-boarding-change" data-go-to-screen="2"><?php _e( 'Get started', 'newsletter-glue' ); ?></button>
	</div>

	<div class="ngl-boarding-helper"><?php _e( 'This will only take a minute.', 'newsletter-glue' ); ?></div>

</div>
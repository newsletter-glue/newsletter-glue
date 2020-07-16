<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden ngl-boarding-completed" data-screen="6">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<p style="font-size: 36px;">🎉</p>

	<h3 style="padding-bottom: 5px;"><?php _e( 'Set up complete!', 'newsletter-glue' ); ?></h3>

	<h3><?php _e( 'You&rsquo;re ready to send a post to your subscribers.', 'newsletter-glue' ); ?>
		<span style="margin-top: 24px;"><?php _e( 'We&rsquo;ve created a demo post for you.', 'newsletter-glue' ); ?></span>
		<span><?php _e( 'Let&rsquo;s head over there now.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-boarding-btn ngl-btn">
		<a href="<?php echo add_query_arg( 'ngl_onboarding', 'complete' ); ?>" class="ui primary button ngl-boarding-complete"><?php _e( 'Try demo', 'newsletter-glue' ); ?></a>
	</div>

</div>
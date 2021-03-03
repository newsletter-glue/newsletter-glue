<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$show_getstarted = newsletterglue_is_free_version() ? true : false;

?>

<div class="ngl-boarding" data-screen="1">

	<div class="ngl-boarding-logo">
		<div class="ngl-logo"><img src="<?php echo NGL_PLUGIN_URL . '/assets/images/top-bar-logo.svg'; ?>" /></div>
	</div>

	<p style="font-size:30px;">👋</p>
	<p><?php _e( 'Hi friend,', 'newsletter-glue' ); ?></p>
	<p><?php _e( 'Ready to send your first newsletter?', 'newsletter-glue' ); ?></p>
	<p><?php _e( 'Let&rsquo;s get you set up!', 'newsletter-glue' ); ?></p>

	<?php do_action( 'newsletterglue_onboarding_welcome' ); ?>

	<div class="ngl-boarding-btn ngl-btn" style="<?php if ( ! $show_getstarted ) echo 'display: none;'; ?>">
		<button class="ui primary button ngl-boarding-change" data-go-to-screen="2"><?php _e( 'Get started', 'newsletter-glue' ); ?></button>
	</div>
	<div class="ngl-boarding-helper" style="<?php if ( ! $show_getstarted ) echo 'display: none;'; ?>"><?php _e( 'This will only take a minute.', 'newsletter-glue' ); ?></div>

</div>
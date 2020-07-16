<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden" data-screen="2">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<div class="ngl-boarding-step"><?php _e( 'Step 1 of 3', 'newsletter-glue' ); ?></div>

	<h3><?php _e( 'Let&rsquo;s begin by connecting your email software...', 'newsletter-glue' ); ?></h3>

	<div class="ngl">
		<?php include_once NGL_PLUGIN_DIR . 'includes/admin/connect/views/connect-card.php'; ?>
	</div>

	<div class="ngl-boarding-next disabled"><span class="material-icons">arrow_forward</span><span class="ngl-boarding-next-text"><?php _e( 'next', 'newsletter-glue' ); ?></span></div>
	<div class="ngl-boarding-prev"><span class="material-icons">arrow_back</span><span class="ngl-boarding-prev-text"><?php _e( 'prev', 'newsletter-glue' ); ?></span></div>

</div>
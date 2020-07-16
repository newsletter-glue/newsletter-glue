<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden" data-screen="3">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<h3><?php _e( 'Great, you&rsquo;re connected!', 'newsletter-glue' ); ?></h3>

	<div class="ngl">
		<div class="ngl-cards">
			<div class="ngl-card">
				<div class="ngl-card-state is-working">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="check circle icon"></i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Connected!', 'newsletter-glue' ); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="ngl-boarding-next disabled"><span class="material-icons">arrow_forward</span><span class="ngl-boarding-next-text"><?php _e( 'next', 'newsletter-glue' ); ?></span></div>
	<div class="ngl-boarding-prev"><span class="material-icons">arrow_back</span><span class="ngl-boarding-prev-text"><?php _e( 'prev', 'newsletter-glue' ); ?></span></div>

</div>
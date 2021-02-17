<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex alt2">

	<div class="ngl-metabox-flex">
		<div class="ngl-test-result-wrap">
			<div class="ngl-test-result ngl-is-valid is-hidden"></div>
			<div class="ngl-test-result ngl-is-invalid is-hidden"></div>
			<?php if ( $this->test_email_by_wordpress() ) : ?>
			<div class="ngl-test-notice">
				<?php _e( 'This test email is sent by WordPress. Formatting and deliverability might differ from your email service.', 'newsletter-glue' ); ?>
			</div>
			<?php endif; ?>
		</div>
		<div class="ngl-field" style="min-width: 130px;">
			<div class="ngl-action-link is-hidden">
				<a href="#" class="ngl-link ngl-retest"><?php esc_html_e( 'Start again', 'newsletter-glue' ); ?></a>
			</div>
		</div>
	</div>

	<div class="ngl-metabox-flex">

	</div>

</div>
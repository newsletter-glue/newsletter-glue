<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $post_type;

?>

<div class="ngl-metabox-flex alt2" <?php if ( $post_type === 'ngl_pattern' ) echo 'style="min-height:40px;padding-bottom:30px;"'; ?>>

	<div class="ngl-metabox-flex">
		<div class="ngl-test-result-wrap">
			<div class="ngl-test-result ngl-is-valid is-hidden"></div>
			<div class="ngl-test-result ngl-is-invalid is-hidden"></div>
			<?php if ( $this->test_email_by_wordpress() ) : ?>
			<div class="ngl-test-notice">
				<?php echo sprintf( __( 'This test email is sent by WordPress. Formatting and deliverability might differ slightly from email campaigns sent by %s.', 'newsletter-glue' ), newsletterglue_get_name( $this->app ) ); ?>
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
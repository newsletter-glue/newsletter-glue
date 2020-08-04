<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-reset <?php if ( ! isset( $settings->sent ) ) echo 'is-hidden'; ?>">

	<div class="ngl-metabox-flex ngl-metabox-alt">

		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Send as newsletter', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field">
			<?php if ( isset( $settings->schedule ) && $settings->schedule === 'draft' ) : ?>
			<?php printf( __( 'You&rsquo;ve already saved an email for this post as a draft in %s. Want to create another one?', 'newsletter-glue' ), newsletterglue_get_name( $app ) ); ?>
			<?php else : ?>
			<?php _e( 'You&rsquo;ve already sent this post as a newsletter. Want to send it again?', 'newsletter-glue' ); ?>
			<?php endif; ?>
		</div>

	</div>

	<div class="ngl-metabox-flex ngl-metabox-flex3">
		<a href="#" class="ui button basic ngl-reset-newsletter" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php echo isset( $settings->schedule ) && $settings->schedule === 'draft' ? __( 'Create new newsletter', 'newsletter-glue' ) : __( 'Send another newsletter', 'newsletter-glue' ); ?></a>
	</div>

</div>
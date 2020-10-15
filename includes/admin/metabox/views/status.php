<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-msgbox-wrap is-hidden">

	<div class="ngl-msgbox">

		<div class="">
			<?php _e( 'Your email newsletter is on its way!', 'newsletter-glue' ); ?>
			<span class="ngl-msgbox-link"><a href="#" class="ngl-reset-newsletter" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php _e( 'Edit and send again...', 'newsletter-glue' ); ?></a></span>
		</div>

		<div class=""><object style="width: 80px;" data="<?php echo NGL_PLUGIN_URL . 'assets/images/sending.svg'; ?>" type="image/svg+xml"></object></div>

		<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank" class="ngl-get-help"><i class="question circle outline icon"></i><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>

	</div>

	<?php echo newsletterglue_get_review_button_html( 'post' ); ?>

</div>
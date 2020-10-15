<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox">

	<div class="ngl-metabox-flex is-disabled">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Send as newsletter', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field">
			<label>
				<input type="checkbox" />
				<?php _e( 'Send this blog post as an email newsletter...', 'newsletter-glue' ); ?>
			</label>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-msg is-notice"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-connect' ) ); ?>" target="_blank"><?php _e( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' ); ?></a></div>
	</div>

</div>
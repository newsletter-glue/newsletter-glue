<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

global $post_type;

?>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>

<?php if ( $post_type != 'ngl_pattern' ) : ?>
<div class="ngl-metabox ngl-metabox-flex alt3 ngl-sending-box <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<div class="ngl-metabox-flex ngl-metabox-flex-toggle">

		<div class="ngl-field ngl-field-master">
			<input type="hidden" name="ngl_double_confirm" id="ngl_double_confirm" value="no" />
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
			<label for="ngl_send_newsletter"><?php _e( 'Send as newsletter', 'newsletter-glue' ); ?> <span class="ngl-field-master-help"><?php _e( '(when post is published/updated)', 'newsletter-glue' ); ?></span></label>
		</div>

	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-not-ready is-hidden">
			<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready. Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
		</div>
	</div>

</div>
<?php endif; ?>
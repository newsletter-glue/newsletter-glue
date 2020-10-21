<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-header">
		<?php esc_html_e( 'Send as newsletter', 'newsletter-glue' ); ?>
	</div>

	<div class="ngl-field">
		<label>
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
			<?php _e( 'Send this blog post as an email newsletter...', 'newsletter-glue' ); ?>
		</label>
	</div>

</div>
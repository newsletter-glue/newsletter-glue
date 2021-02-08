<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-metabox-flex alt3">

	<div class="ngl-metabox-flex ngl-metabox-flex-toggle">

		<div class="ngl-field ngl-field-master">
			<input type="hidden" name="ngl_double_confirm" id="ngl_double_confirm" value="no" />
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
			<label for="ngl_send_newsletter"><?php _e( 'Send as newsletter', 'newsletter-glue' ); ?></label>
		</div>

	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-not-ready is-hidden">
			<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready. Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
		</div>
	</div>

</div>
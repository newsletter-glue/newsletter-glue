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
		</div>
		<div class="ngl-field">
			<div class="ngl-action-link is-hidden">
				<a href="#" class="ngl-link ngl-retest"><?php esc_html_e( 'Start again', 'newsletter-glue' ); ?></a>
			</div>
		</div>
	</div>
	
	<div class="ngl-metabox-flex">
	</div>

</div>
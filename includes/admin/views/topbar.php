<?php
/**
 * Admin Top Bar.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-bar">

	<div class="ngl-flex ngl-left">
		<div class="ngl-logo"></div>
		<div class="ngl-label"><?php _e( '<span>Newsletter</span> Glue', 'newsletter-glue' ); ?></div>
	</div>

	<div class="ngl-flex ngl-right">
		<a href="https://docs.memberhero.pro" target="_blank" class="ngl-icon-link"><i class="question circle outline icon"></i><?php esc_html_e( 'Help &amp; Documentation', 'newsletter-glue' ); ?></a>
	</div>

</div>

<div class="wrap newsletterglue-wrap">
	<h1></h1>
</div>
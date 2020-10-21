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
		<div class="ngl-label"><?php echo wp_kses_post( __( '<span>Newsletter</span> Glue', 'newsletter-glue' ) ); ?></div>
	</div>

	<?php echo newsletterglue_get_review_button_html( 'top-bar' ); ?>

	<div class="ngl-flex ngl-right">
		<a href="#ngl-report-bug" class="ngl-bug-report ngl-icon-link"><span class="material-icons">error_outline</span><?php esc_html_e( 'Report bug', 'newsletter-glue' ); ?></a>
		&nbsp;|&nbsp;
		<a href="#ngl-request-feature" class="ngl-request-feature ngl-icon-link"><span class="material-icons">add_circle_outline</span><?php esc_html_e( 'Request feature', 'newsletter-glue' ); ?></a>
		&nbsp;|&nbsp;
		<a href="https://docs.newsletterglue.com" target="_blank" class="ngl-icon-link"><span class="material-icons">help_outline</span><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>
	</div>

</div>

<div class="wrap newsletterglue-wrap">
	<h1></h1>
</div>
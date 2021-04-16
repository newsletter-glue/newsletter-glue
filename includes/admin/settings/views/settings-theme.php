<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ui large header">

	<?php esc_html_e( 'Newsletter theme designer', 'newsletter-glue' ); ?>

	<div class="sub header"><?php esc_html_e( 'These style options only show up in emails, not in posts.', 'newsletter-glue' ); ?></div>

	<div class="ngl-theme-toggle">
		<span class="ngl-desktop"><?php esc_html_e( 'Desktop', 'newsletter-glue' ); ?></span>
		<span class="ngl-mobile"><?php esc_html_e( 'Mobile', 'newsletter-glue' ); ?></span>
		<span class="ngl-bulb">Desktop</span>
	</div>

</div>

<div class="ngl-theme">

	<div class="ngl-theme-reset">
		<div class="ngl-theme-reset-status">
			<span class="ngl-process is-hidden is-waiting">
				<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
			</span>
			<span class="ngl-process is-hidden is-valid">
				<span class="ngl-process-icon"><i class="check circle icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Saved', 'newsletter-glue' ); ?></strong></span>
			</span>
		</div>
		<div class="ngl-theme-reset-link"><?php _e( 'Reset to default style', 'newsletter-glue' ); ?></div>
		<div class="ngl-theme-reset-confirm is-hidden"><?php _e( 'Confirm reset (you can&rsquo;t undo after this)', 'newsletter-glue' ); ?></div>
		<div class="ngl-theme-reset-btns is-hidden"><a href="#" class="ngl-theme-reset-do"><?php _e( 'Reset', 'newsletter-glue' ); ?></a><span>|</span><a href="#" class="ngl-theme-reset-back"><?php _e( 'Go back', 'newsletter-glue' ); ?></a></div>
	</div>

	<div class="ngl-theme-preview">

		<?php include_once( 'settings-theme-preview.php' ); ?>

	</div>

	<div class="ngl-theme-panel">

		<?php include_once( 'settings-theme-panel.php' ); ?>

	</div>

</div>
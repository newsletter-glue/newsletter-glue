<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ui large header">

	<?php esc_html_e( 'Email Defaults', 'newsletter-glue' ); ?>

	<div class="sub header"><?php echo wp_kses_post( __( 'New newsletters will default to the details you&rsquo;ve chosen here.<br />Change details for individual newsletters at the bottom of each new post.', 'newsletter-glue' ) ); ?></div>

</div>

<div class="ngl-metabox">

	<?php if ( ! $app ) : ?>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-msg is-notice"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings&tab=connect' ) ); ?>"><?php _e( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' ); ?></a></div>
	</div>

	<?php else : ?>

	<?php do_action( 'newsletterglue_email_defaults_settings', $api ); ?>

	<?php include_once newsletterglue_get_path( $app ) . '/settings.php'; ?>

	<?php include NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-general.php'; ?>

	<?php endif; ?>

</div>
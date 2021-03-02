<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<nav class="ngl-tab-wrapper nav-tab-wrapper">

	<?php foreach( newsletterglue_settings_tabs() as $key => $value ) : ?>

	<a href="<?php echo add_query_arg( 'tab', $key ); ?>" class="nav-tab <?php if ( $tab === $key ) echo 'nav-tab-active'; ?>"><?php echo esc_html( $value ); ?></a>

	<?php endforeach; ?>

</nav>

<div class="ngl ngl-wrap ngl-settings <?php if ( $app ) echo 'ngl-settings-' . esc_attr( $app ); ?>">

	<?php
		$file = 'settings-' . $tab . '.php';
		if ( file_exists( NGL_PLUGIN_DIR . 'includes/admin/settings/views/' . $file ) ) {
			include( NGL_PLUGIN_DIR . 'includes/admin/settings/views/' . $file );
		}
		do_action( "newsletterglue_settings_tab_{$tab}" );
	?>

	<?php if ( $app || $tab == 'css' || $tab == 'additional' ) : ?>

		<?php if ( $tab != 'theme' && apply_filters( "newsletterglue_settings_tab_{$tab}_save_button", true ) ) : ?>
		<div class="ngl-metabox ngl-metabox-flex ngl-metabox-flex2">
			<a href="#" class="ui button ngl-settings-save"><?php _e( 'Save', 'newsletter-glue' ); ?></a>
		</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'newsletterglue_common_action_hook' ); ?>

	<?php echo newsletterglue_get_review_button_html( 'settings' ); ?>

</div>
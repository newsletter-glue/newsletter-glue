<?php
/**
 * Settings page.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin page view.
 */
function newsletterglue_settings_page() {

	$integrations = get_option( 'newsletterglue_integrations' );

	$tab = newsletterglue_settings_tab();

	$app = newsletterglue_default_connection();

	// Load into memory.
	if ( $app ) {

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $app );
		$api		= new $classname();

		if ( method_exists( $api, 'connect' ) ) {
			$api->connect();
		}

	} else {
		include_once NGL_PLUGIN_DIR . 'includes/integrations/core/init.php';
		$api = new NGL_Integration_Core();
	}

	// Header.
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	// Settings UI.
	require_once NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings.php';

	echo '<input type="hidden" name="ngl_app" id="ngl_app" value="' . $app . '" />';
}

/**
 * Setting tabs.
 */
function newsletterglue_settings_tabs() {

	$tabs = array();

	return apply_filters( 'newsletterglue_settings_tabs', $tabs );
}

/**
 * Get current tab.
 */
function newsletterglue_settings_tab() {
	if ( isset( $_GET[ 'tab' ] ) && ! empty( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( newsletterglue_settings_tabs() ) ) ) {
		return $_GET[ 'tab' ];
	} else {
		return 'defaults';
	}
}
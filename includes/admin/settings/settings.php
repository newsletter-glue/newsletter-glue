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

	$connection = newsletterglue_default_connection();

	// Load into memory.
	if ( $connection ) {
		include_once NGL_PLUGIN_DIR . 'includes/integrations/' . $connection . '/init.php';
		$classname 	= 'NGL_' . ucfirst( $connection );
		$api		= new $classname();
		$api->connect();
	}

	// Header.
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	// Settings UI.
	require_once NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings.php';
}

/**
 * Setting tabs.
 */
function newsletterglue_settings_tabs() {

	$tabs = array(
		'defaults'	=> __( 'Email Defaults', 'newsletter-glue' ),
		'css' 		=> __( 'Custom CSS', 'newsletter-glue' ),
	);

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
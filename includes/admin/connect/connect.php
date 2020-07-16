<?php
/**
 * Connections page.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin page view.
 */
function newsletterglue_connect_page() {

	$integrations = get_option( 'newsletterglue_integrations' );

	// Header.
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	// Connections UI.
	require_once NGL_PLUGIN_DIR . 'includes/admin/connect/views/connect.php';
}
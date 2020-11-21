<?php
/**
 * Blocks page.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin page view.
 */
function newsletterglue_blocks_page() {

	$blocks = newsletterglue_get_blocks();

	// Header.
	require_once NGL_PLUGIN_DIR . 'includes/admin/views/topbar.php';

	// UI.
	require_once NGL_PLUGIN_DIR . 'includes/admin/blocks/views/blocks.php';

}
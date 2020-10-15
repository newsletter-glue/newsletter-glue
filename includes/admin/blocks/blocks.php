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

/**
 * Block settings.
 */
function newsletterglue_include_block_settings( $block_id ) {

	$file 		= str_replace( '_', '-', str_replace( 'newsletterglue_block_', '', $block_id ) );
	$file_url 	= NGL_PLUGIN_DIR . 'includes/admin/blocks/views/settings-' . $file . '.php';

	$include = apply_filters( 'newsletterglue_include_block_settings', $file_url, $block_id );

	if ( file_exists( $include ) ) {
		include_once( $include );
	}

}

/**
 * Block demo.
 */
function newsletterglue_include_block_demo( $block_id ) {

	$file 		= str_replace( '_', '-', str_replace( 'newsletterglue_block_', '', $block_id ) );
	$file_url 	= NGL_PLUGIN_DIR . 'includes/admin/blocks/views/demo-' . $file . '.php';

	$include = apply_filters( 'newsletterglue_include_block_demo', $file_url, $block_id );

	if ( file_exists( $include ) ) {
		include_once( $include );
	}

}
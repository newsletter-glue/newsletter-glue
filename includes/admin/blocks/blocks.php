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

	$file_url 	= NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/settings.php';

	$include 	= apply_filters( 'newsletterglue_include_block_settings', $file_url, $block_id );
	$include 	= apply_filters( $block_id . '_settings_template', $include );

	if ( file_exists( $include ) ) {
		include_once( $include );
	}

}

/**
 * Block demo.
 */
function newsletterglue_include_block_demo( $block_id ) {

	$file_url 	= NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/demo.php';

	$include 	= apply_filters( 'newsletterglue_include_block_demo', $file_url, $block_id );
	$include 	= apply_filters( $block_id . '_demo_template', $include );

	if ( file_exists( $include ) ) {
		include_once( $include );
	}

}
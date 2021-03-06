<?php
/**
 * Gutenberg.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get blocks.
 */
function newsletterglue_get_blocks() {

	$blocks = array(
		'newsletterglue_block_show_hide_content' 	=> array(),
		'newsletterglue_block_form' 				=> array(),
		'newsletterglue_block_article' 				=> array(),
		'newsletterglue_block_author' 				=> array(),
		'newsletterglue_block_callout' 				=> array(),
		'newsletterglue_block_metadata' 			=> array(),
		'newsletterglue_block_share' 				=> array(),
	);

	return apply_filters( 'newsletterglue_get_blocks', $blocks );

}

/**
 * Add our block category.
 */
function newsletterglue_add_block_category( $categories, $post ) {

	return array_merge(
		$categories,
		array(
			array(
				'slug' 		=> 'newsletterglue-blocks',
				'title' 	=> __( 'Newsletter Glue', 'newsletter-glue' ),
			),
		)
	);

}
add_filter( 'block_categories', 'newsletterglue_add_block_category', 10, 2 );

/**
 * Enqueues the required frontend scripts.
 */
function newsletterglue_load_frontend_scripts( $hook ) {
	global $wp_scripts;

	$js_dir  = NGL_PLUGIN_URL . 'assets/js/';
	$css_dir = NGL_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register scripts.
	wp_register_script( 'newsletterglue_gutenberg', $js_dir . 'frontend/gutenberg' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_enqueue_script( 'newsletterglue_gutenberg' );

	wp_localize_script( 'newsletterglue_gutenberg', 'newsletterglue_gutenberg', apply_filters( 'newsletterglue_gutenberg_js_params',
		array(
			'ajaxurl'    		=> newsletterglue_get_ajax_url(),
			'ajaxnonce'			=> wp_create_nonce( 'newsletterglue-ajax-nonce' ),
			'please_wait'		=> __( 'Please wait...', 'newsletter-glue' ),
		)
	) );

	wp_register_style( 'newsletterglue_gutenberg', $css_dir . 'gutenberg' . $suffix . '.css', array(), NGL_VERSION );
	wp_enqueue_style( 'newsletterglue_gutenberg' );

}
add_action( 'wp_enqueue_scripts', 'newsletterglue_load_frontend_scripts', 100 );
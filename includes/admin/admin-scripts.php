<?php
/**
 * Admin Scripts.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueues the required admin scripts.
 */
function newsletterglue_load_admin_scripts( $hook ) {
	global $wp_scripts;

	$screen    = get_current_screen();
	$screen_id = $screen ? $screen->id : '';

	$js_dir  = NGL_PLUGIN_URL . 'assets/js/';
	$css_dir = NGL_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	// Register scripts.
	wp_register_script( 'newsletterglue_semantic', $js_dir . 'semantic/semantic' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_flatpickr', $js_dir . 'flatpickr/flatpickr' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_admin', $js_dir . 'admin/admin' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_global', $js_dir . 'admin/global' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_onboarding', $js_dir . 'admin/onboarding' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );

	wp_enqueue_script( 'newsletterglue_global' );

	// Register styles.
	wp_register_style( 'newsletterglue_admin_menu_styles', $css_dir . 'menu' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_material_icons', '//fonts.googleapis.com/icon?family=Material+Icons', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_semantic', $css_dir . 'semantic' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_admin_styles', $css_dir . 'admin' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_onboarding_styles', $css_dir . 'onboarding' . $suffix . '.css', array(), NGL_VERSION );

	// Sitewide menu CSS.
	wp_enqueue_style( 'newsletterglue_admin_menu_styles' );

	// Admin assets for plugin pages only.
	if ( in_array( $screen_id, newsletterglue_get_screen_ids() ) ) {
		wp_enqueue_script( 'newsletterglue_semantic' );
		wp_enqueue_script( 'newsletterglue_flatpickr' );
		wp_enqueue_script( 'newsletterglue_admin' );
		
		wp_localize_script( 'newsletterglue_admin', 'newsletterglue_params', apply_filters( 'newsletterglue_admin_js_params', array(
			'ajaxurl'    	=> newsletterglue_get_ajax_url(),
			'ajaxnonce'		=> wp_create_nonce( 'newsletterglue-ajax-nonce' ),
			'publish_error'	=> __( 'We can&rsquo;t publish yet. Your email newsletter is missing some important details.<br /><a href="#">Let&rsquo;s fix that.</a>', 'newsletter-glue' ),
			'saving'		=> '<i class="sync alternate icon"></i>' . __( 'Saving...', 'newsletter-glue' ),
			'saved'			=> '<i class="check circle outline icon"></i>' . __( 'Saved', 'newsletter-glue' ),
			'save'			=> __( 'Save', 'newsletter-glue' ),
		) ) );

		wp_enqueue_style( 'newsletterglue_material_icons' );
		wp_enqueue_style( 'newsletterglue_semantic' );
		wp_enqueue_style( 'newsletterglue_admin_styles' );
	}

	// Add onboarding assets.
	if ( newsletterglue_is_onboarding_page() ) {
		wp_enqueue_script( 'newsletterglue_onboarding' );
		wp_enqueue_style( 'newsletterglue_onboarding_styles' );
	}

}
add_action( 'admin_enqueue_scripts', 'newsletterglue_load_admin_scripts', 100 );
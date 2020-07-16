<?php
/**
 * Onboarding page.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Detect if the onboarding was not skipped.
 */
function newsletterglue_redirect_to_onboarding() {

	// Skip.
	if ( isset( $_GET[ 'ngl_skip_onboarding' ] ) ) {
		newsletterglue_skip_onboarding();
	}

	// Complete.
	if ( isset( $_GET[ 'ngl_onboarding' ] ) && $_GET[ 'ngl_onboarding' ] == 'complete' ) {
		newsletterglue_skip_onboarding();
	}

	// Show onboarding.
	$post_id 	= isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : '';
	$on_screen 	= isset( $_GET[ 'ngl_screen' ] ) && $_GET[ 'ngl_screen' ] == 'onboarding' ? true : false;

	if ( $post_id && $post_id == get_option( 'newsletterglue_demo_post' ) && ! $on_screen ) {
		if ( ! get_option( 'newsletterglue_onboarding_done' ) ) {
			exit( wp_redirect( add_query_arg( 'ngl_screen', 'onboarding' ) ) );
		}
	}

}
add_action( 'admin_init', 'newsletterglue_redirect_to_onboarding', 100 );

/**
 * Skips the onboarding and redirect to normal URL.
 */
function newsletterglue_skip_onboarding() {

	$url = remove_query_arg( 'ngl_onboarding' );
	$url = remove_query_arg( 'ngl_screen', $url );
	$url = remove_query_arg( 'ngl_skip_onboarding', $url );

	update_option( 'newsletterglue_onboarding_done', 'yes' );

	exit( wp_redirect( $url ) );
}

/**
 * Skip onboarding URL.
 */
function newsletterglue_skip_onboarding_url() {
	return add_query_arg( 'ngl_skip_onboarding', 'true' );
}

/**
 * Add onboarding html.
 */
function newsletterglue_add_onboarding_html() {

	if ( newsletterglue_is_onboarding_page() ) {
		require_once NGL_PLUGIN_DIR . 'includes/admin/onboarding/views/onboarding.php';
	}

}
add_action( 'admin_footer', 'newsletterglue_add_onboarding_html' );

/**
 * Returns true if onboarding assets should be loaded.
 */
function newsletterglue_is_onboarding_page() {

	if ( isset( $_GET[ 'ngl_screen' ] ) && $_GET[ 'ngl_screen' ] == 'onboarding' && isset( $_GET[ 'post' ] ) ) {
		if ( $_GET[ 'post' ] == get_option( 'newsletterglue_demo_post' ) && ! get_option( 'newsletterglue_onboarding_done' ) ) {
			return true;
		}		
	}

	return false;
}
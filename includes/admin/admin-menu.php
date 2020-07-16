<?php
/**
 * Admin Menu.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin menu links.
 */
function newsletterglue_add_admin_menu() {
	global $menu;

	if ( current_user_can( 'manage_newsletterglue' ) ) {
		$menu[] = array( '', 'read', 'separator-newsletterglue', '', 'wp-menu-separator newsletterglue' );
	}

	$admin_page			= add_menu_page( __( 'Newsletter Glue', 'newsletter-glue' ), __( 'Newsletter Glue', 'newsletter-glue' ), 'manage_newsletterglue', 'newsletter-glue', null, null, '25.5471' );

	$connect_page 		= add_submenu_page( 'newsletter-glue', __( 'Connect', 'newsletter-glue' ), __( 'Connect', 'newsletter-glue' ), 'manage_newsletterglue', 'ngl-connect', 'newsletterglue_connect_page' );
	$settings_page 		= add_submenu_page( 'newsletter-glue', __( 'Settings', 'newsletter-glue' ), __( 'Settings', 'newsletter-glue' ), 'manage_newsletterglue', 'ngl-settings', 'newsletterglue_settings_page' );
	$upgrade_page 		= add_submenu_page( 'newsletter-glue', __( 'Upgrade', 'newsletter-glue' ), __( 'Upgrade', 'newsletter-glue' ), 'manage_newsletterglue', 'ngl-upgrade', 'newsletterglue_upgrade_page' );

	//add_action( 'load-' . $page, 'hook' );

}
add_action( 'admin_menu', 'newsletterglue_add_admin_menu', 10 );

/**
 * Custom menu order.
 */
function newsletterglue_custom_menu_order( $enabled ) {
	return $enabled || current_user_can( 'manage_newsletterglue' );
}
add_filter( 'custom_menu_order', 'newsletterglue_custom_menu_order' );

/**
 * Reorder the menu items in admin.
 */
function newsletterglue_menu_order( $menu_order ) {
	// Initialize our custom order array.
	$newsletterglue_menu_order = array();

	// Get the index of our custom separator.
	$newsletterglue_separator = array_search( 'separator-newsletterglue', $menu_order, true );

	// Loop through menu order and do some rearranging.
	foreach ( $menu_order as $index => $item ) {

		if ( 'newsletter-glue' === $item ) {
			$newsletterglue_menu_order[] = 'separator-newsletterglue';
			$newsletterglue_menu_order[] = $item;
			unset( $menu_order[ $newsletterglue_separator ] );
		} elseif ( ! in_array( $item, array( 'separator-newsletterglue' ), true ) ) {
			$newsletterglue_menu_order[] = $item;
		}
	}

	// Return order.
	return $newsletterglue_menu_order;
}
add_filter( 'menu_order', 'newsletterglue_menu_order' );

/**
 * Removes the parent menu item.
 */
function newsletterglue_menu_order_fix() {
	global $submenu;

	if ( isset( $submenu['newsletter-glue'] ) ) {
		// Remove 'newsletter-glue' sub menu item.
		unset( $submenu['newsletter-glue'][0] );
	}
}
add_action( 'admin_head', 'newsletterglue_menu_order_fix' );
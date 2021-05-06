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

	$ngicon = base64_encode(
		'<svg id="Group_76" data-name="Group 76" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 196.005 124.099">
			<g id="Group_47" data-name="Group 47" transform="translate(0 0)">
				<path id="Path_29" data-name="Path 29" d="M71.7,79.352l2.14-1.03a78.958,78.958,0,0,1-8.774-36.987q0-15.659,7.762-26.593C78,7.452,82.26,2.787,93.206.622s21.368,1.726,23.4,5.46S109,10.179,102.9,18.744s-6.309,9.32-9.7,18.068c-2.223,7.105-3.172,9.842-4.083,19.248-.428,9.314.821,14.809,2.941,24.421,2.335,9.378,4.273,13.454,7.153,17.684a42.2,42.2,0,0,1-10.934,5.4,46.312,46.312,0,0,1-12.891,1.485A30.365,30.365,0,0,1,50.553,92.7,51.629,51.629,0,0,1,43.6,78.861q-4.995-14.444-6.074-36.717h-2.16q-6.209,28.078-6.209,39.282t4.05,17.684q-8.1,6.074-16.874,6.074T3.78,100.122Q0,95.06,0,85c0-6.7,7.9-52.117,8.5-57.033S1.35,13.527,1.35,13.527q15.119-9.719,27-9.719T46.3,7.925a21.635,21.635,0,0,1,8.5,12.081,102.88,102.88,0,0,1,3.577,16.806q1.147,8.842,4.05,21.261C64.367,66.352,68.189,72.692,71.7,79.352Z" transform="translate(0 0)" fill="#9ca2a7"/>
			</g>
			<g id="Group_46" data-name="Group 46" transform="matrix(0.999, -0.035, 0.035, 0.999, 101.15, 3.897)">
				<path id="Path_29-2" data-name="Path 29" d="M61.825,71.949,54.94,57.775q14.444-9.584,26.053-9.584,9.719,0,9.719,10.934,0,3.1-3.712,18.966t-3.712,24.231q0,8.369,3.645,12.419a26.362,26.362,0,0,1-15.794,5.535q-8.234,0-11.677-5.4T56.02,99.082a52.938,52.938,0,0,1-15.119,2.16q-18.359,0-29.63-11.339T0,56.425A50.071,50.071,0,0,1,4.05,37.189,64.331,64.331,0,0,1,15.524,19.033a55.835,55.835,0,0,1,19.371-13.7A59.994,59.994,0,0,1,59.6,0Q72.354,0,79.576,4.725A14.839,14.839,0,0,1,86.8,17.819q0,8.369-5.467,13.026A18.713,18.713,0,0,1,68.844,35.5a26.049,26.049,0,0,1-12.621-3.037,23.158,23.158,0,0,1-8.977-8.707q-7.289,3.51-12.554,13.769A46.41,46.41,0,0,0,29.428,58.99q0,11.2,4.387,16.806a13.44,13.44,0,0,0,11,5.6Q54.4,81.4,61.825,71.949Z" transform="translate(0 0)" fill="#9ca2a7"/>
			</g>
		</svg>'
	);

	$admin_page			= add_menu_page( __( 'Newsletters', 'newsletter-glue' ), __( 'Newsletters', 'newsletter-glue' ), 'manage_newsletterglue', 'newsletter-glue', null, 'data:image/svg+xml;base64,' . $ngicon, '25.5471' );

	//$issues 			= add_submenu_page( 'newsletter-glue', __( 'All Newsletters', 'newsletter-glue' ), __( 'All Newsletters', 'newsletter-glue' ), 'manage_newsletterglue', 'edit.php?post_type=newsletterglue' );
	//$new_issue_page 	= add_submenu_page( 'newsletter-glue', __( 'Add New Newsletter', 'newsletter-glue' ), __( 'Add New Newsletter', 'newsletter-glue' ), 'manage_newsletterglue', 'post-new.php?post_type=newsletterglue' );
	//$new_template_page 	= add_submenu_page( 'newsletter-glue', __( 'Templates & Styles', 'newsletter-glue' ), __( 'Templates & Styles', 'newsletter-glue' ), 'manage_newsletterglue', 'edit.php?post_type=ngl_pattern' );
	$settings_page 		= add_submenu_page( 'newsletter-glue', __( 'Settings', 'newsletter-glue' ), __( 'Settings', 'newsletter-glue' ), 'manage_newsletterglue', 'ngl-settings', 'newsletterglue_settings_page' );

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
 * Removes the parent menu item.
 */
function newsletterglue_menu_order_fix() {

	global $submenu;

	if ( isset( $submenu ) && is_array( $submenu ) ) {
		foreach( $submenu as $key => $array ) {
			if ( $key === 'newsletter-glue' ) {
				foreach( $array as $index => $value ) {
					if ( isset( $value[2] ) && $value[2] === 'newsletter-glue' ) {
						unset( $submenu[ 'newsletter-glue' ][ $index ] );
					}
				}
			}
		}
	}

}
add_action( 'admin_menu', 'newsletterglue_menu_order_fix', 1000 );
add_action( 'admin_menu_editor-menu_replaced', 'newsletterglue_menu_order_fix', 1000 );
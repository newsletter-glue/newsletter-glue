<?php
/**
 * Install Functions.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 */
function newsletterglue_install( $network_wide = false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {

		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {

			switch_to_blog( $blog_id );
			newsletterglue_run_install();
			restore_current_blog();

		}

	} else {

		newsletterglue_run_install();

	}

}
register_activation_hook( NGL_PLUGIN_FILE, 'newsletterglue_install' );

/**
 * Un-Install
 */
function newsletterglue_uninstall( $network_wide = false ) {

	global $wpdb;

	wp_delete_post( get_option( 'newsletterglue_demo_post' ), true );

	// Delete options.
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'newsletterglue\_%';" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%\_newsletterglue\_%';" );

	delete_transient( '_ngl_onboarding' );

}
register_deactivation_hook( NGL_PLUGIN_FILE, 'newsletterglue_uninstall' );

/**
 * Run the Install process.
 */
function newsletterglue_run_install() {

	// Setup user roles.
	newsletterglue_install_roles();

	// Add custom css.
	newsletterglue_add_default_css();

	// Set transient.
	set_transient( '_ngl_onboarding', 1, 30 );

	// Set version.
	update_option( 'newsletterglue_version', NGL_VERSION );

}

/**
 * Add default css.
 */
function newsletterglue_add_default_css() {

	if ( ! get_option( 'newsletterglue_version' ) ) {

$css = get_option( 'newsletterglue_css' );

$css .= '
.NGpurplebox {
	padding: 30px;
	background: #f1ecff;
}

.NGbluebox {
	padding: 30px;
	background: #d6f3ff;
}

.NGyellowbox {
	padding: 30px;
	background: #fffbdb;
}
';

		update_option( 'newsletterglue_css', $css );
	}

}

/**
 * When a new Blog is created in multisite run the installer
 */
function newsletterglue_new_blog_created( $blog ) {
	if ( ! is_plugin_active_for_network( plugin_basename( NGL_PLUGIN_FILE ) ) ) {
		return;
	}

	if ( ! is_int( $blog ) ) {
		$blog = $blog->id;
	}

	switch_to_blog( $blog );
	newsletterglue_install();
	restore_current_blog();

}
if ( version_compare( get_bloginfo( 'version' ), '5.1', '>=' ) ) {
	add_action( 'wp_initialize_site', 'newsletterglue_new_blog_created' );
} else {
	add_action( 'wpmu_new_blog', 'newsletterglue_new_blog_created' );
}

/**
 * Drop our custom tables when a mu site is deleted
 */
function newsletterglue_wpmu_drop_tables( $tables, $blog_id ) {

	switch_to_blog( $blog_id );

	restore_current_blog();

	return $tables;

}
add_filter( 'wpmu_drop_tables', 'newsletterglue_wpmu_drop_tables', 10, 2 );

/**
 * Install user roles on sub-sites of a network.
 */
function newsletterglue_install_roles_on_network() {
	global $wp_roles;

	if ( ! is_object( $wp_roles ) ) {
		return;
	}

	if ( empty( $wp_roles->roles ) || ! array_key_exists( 'newsletterglue_manager', $wp_roles->roles ) ) {
		newsletterglue_install_roles();
	}

}
add_action( 'admin_init', 'newsletterglue_install_roles_on_network' );

/**
 * Onboarding screen.
 */
function newsletterglue_onboarding_screen() {

	// Setup wizard redirect.
	if ( get_transient( '_ngl_onboarding' ) ) {
		$do_redirect  = true;
		$current_page = isset( $_GET['ngl_screen'] ) ? sanitize_text_field( wp_unslash( $_GET['ngl_screen'] ) ) : false;

		// On these pages, or during these events, postpone the redirect.
		if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_newsletterglue' ) ) {
			$do_redirect = false;
		}

		// On these pages, or during these events, disable the redirect.
		if ( 'onboarding' === $current_page || isset( $_GET['activate-multi'] ) ) {
			delete_transient( '_ngl_onboarding' );
			$do_redirect = false;
		}

		// If onboarding was done. Either complete or skipped.
		if ( get_option( 'newsletterglue_onboarding_done' ) || ! newsletterglue_has_no_active_api() ) {
			delete_transient( '_ngl_onboarding' );
			$do_redirect = false;
		}

		if ( $do_redirect ) {
			delete_transient( '_ngl_onboarding' );
			$demo_post = newsletterglue_create_demo_post();
			if ( $demo_post ) {
				wp_safe_redirect( admin_url( 'post.php?post=' . $demo_post . '&action=edit&ngl_screen=onboarding' ) );
				exit;
			}
		}

	}

}
add_action( 'admin_init', 'newsletterglue_onboarding_screen' );

/**
 * Create demo post.
 */
function newsletterglue_create_demo_post() {

	$demo_post = get_option( 'newsletterglue_demo_post' );

	if ( $demo_post ) {
		$post = get_post( $demo_post );
		if ( isset( $post->ID ) ) {
			return $demo_post;
		}
	}

	$args = array(
		'post_status'	=> 'draft',
		'post_type'		=> 'post',
		'post_title'	=> __( "Newsletter Glue: Let's get you started", 'newsletter-glue' ),
		'post_content'  => newsletterglue_get_onboarding_post(),
		'post_author'	=> 1,
	);

	$demo_post = wp_insert_post( $args );

	update_option( 'newsletterglue_demo_post', $demo_post );

	return $demo_post;
}

/**
 * Install user roles.
 */
function newsletterglue_install_roles() {
	global $wp_roles;

	if ( ! is_object( $wp_roles ) ) {
		return;
	}

	// Add roles.
	add_role(
		'newsletterglue_manager',
		'Newsletter Glue Manager',
		array(
			'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true,
			'edit_theme_options'     => true,
		)
	);

	$admin_roles = array( 'administrator', 'newsletterglue_manager' );

	foreach( $admin_roles as $role ) {
		$wp_roles->add_cap( $role, 'manage_newsletterglue' );
	}

}
<?php
/**
 * Compatibility Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP offload media filter.
 */
function newsletterglue_filter_post_local_to_provider( $the_content, $post, $subject, $app ) {
	return apply_filters( 'as3cf_filter_post_local_to_provider', $the_content );
}
add_filter( 'newsletterglue_email_content','newsletterglue_filter_post_local_to_provider', 50, 4 );

/**
 * Fix Yoast duplicate post conflict.
 */
function newsletterglue_duplicate_post_excludelist_filter( $meta_excludelist ) {

	return array_merge( $meta_excludelist, [ '_ngl_results' ] );

}
add_filter( 'duplicate_post_excludelist_filter', 'newsletterglue_duplicate_post_excludelist_filter' );

/**
 * Performs some actions after the WordPress standard fields of a post, or a non-hierarchical custom type item, have been copied.
 */
function newsletterglue_custom_dp_duplicate_post( $new_post_id, $post, $status ) {

	$meta = get_post_meta( $new_post_id, '_newsletterglue', true );
	if ( ! empty( $meta ) && isset( $meta[ 'sent' ] ) ) {
		unset( $meta[ 'sent' ] );
		update_post_meta( $new_post_id, '_newsletterglue', $meta );
	}

}
add_action( 'dp_duplicate_post', 'newsletterglue_custom_dp_duplicate_post', 999, 3 );

/**
 * Fix conflict with MailPoet css.
 */
function newsletterglue_mailpoet_css_conflict( $styles ) {

	$styles[] = 'newsletter-glue';

	return $styles;

}
add_filter( 'mailpoet_conflict_resolver_whitelist_style', 'newsletterglue_mailpoet_css_conflict' );

/**
 * Fix conflict with MailPoet js.
 */
function newsletterglue_mailpoet_js_conflict( $scripts ) {

	$scripts[] = 'newsletter-glue';

	return $scripts;

}
add_filter( 'mailpoet_conflict_resolver_whitelist_script', 'newsletterglue_mailpoet_js_conflict' );

/**
 * Gets the current post type in the WordPress Admin.
 */
function newsletterglue_get_current_post_type() {
	global $post, $typenow, $current_screen;

	// we have a post so we can just get the post type from that
	if ( $post && $post->post_type )
		return $post->post_type;

	// lastly check the post_type querystring
	elseif( isset( $_GET['post_type'] ) )
		return sanitize_key( $_GET['post_type'] );

	// check the global $typenow - set in admin.php
	elseif( $typenow )
		return $typenow;
    
	// check the global $current_screen object - set in sceen.php
	elseif( $current_screen && $current_screen->post_type )
		return $current_screen->post_type;

	// we do not know the post type!
	return null;
}

/**
 * Remove action hooks.
 */
function newsletterglue_remove_action_hooks() {

	if ( in_array( newsletterglue_get_current_post_type(), array( 'newsletterglue', 'ngl_pattern' ) ) ) {
		remove_action( 'enqueue_block_editor_assets', 'stackable_block_editor_assets', 20 );
	}

}
add_action( 'load-post.php', 'newsletterglue_remove_action_hooks', 1 );
add_action( 'load-post-new.php', 'newsletterglue_remove_action_hooks', 1 );
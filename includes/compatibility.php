<?php
/**
 * Compatibility Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

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
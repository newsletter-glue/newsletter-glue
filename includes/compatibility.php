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

	return array_merge( $meta_excludelist, [ '_ngl_results', '_newsletterglue' ] );

}
add_filter( 'duplicate_post_excludelist_filter', 'newsletterglue_duplicate_post_excludelist_filter' );

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
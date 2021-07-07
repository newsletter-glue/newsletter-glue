<?php
/**
 * Rewrite Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allow pagination in newsletter category archive.
 */
function newsletterglue_generate_taxonomy_rewrite_rules( $wp_rewrite ) {

	$rules = array();

	$post_type_name = 'newsletterglue';
	$terms = get_categories( array( 'type' => $post_type_name, 'taxonomy' => 'ngl_newsletter_cat', 'hide_empty' => 0 ) );

	foreach ( $terms as $term ) {
		$rules[ 'newsletter/' . $term->slug . '/page/?([0-9]{1,})/?$' ] = 'index.php?' .'type=' .$post_type_name."&" . $term->taxonomy . '=' . $term->slug . '&paged=' . $wp_rewrite->preg_index( 1 );
	}

	$wp_rewrite->rules = $rules + $wp_rewrite->rules;

}
add_action( 'generate_rewrite_rules', 'newsletterglue_generate_taxonomy_rewrite_rules', 100 );

/**
 * Generates custom link for each newsletter.
 */
function newsletterglue_generate_newsletter_post_link( $post_link, $id = 0 ){
    $post = get_post( $id );  
    if ( is_object( $post ) ) {
        $terms = wp_get_object_terms( $post->ID, 'ngl_newsletter_cat' );
        if ( $terms ) {
            return str_replace( '%newsletter%', $terms[0]->slug, $post_link );
        } else {
			wp_set_object_terms( $post->ID, array( 'archive' ), 'ngl_newsletter_cat' );
			return str_replace( '%newsletter%', 'archive', $post_link );
		}
    }
    return $post_link;  
}
add_filter( 'post_type_link', 'newsletterglue_generate_newsletter_post_link', 1, 3 );

/**
 * Rewrite archive link for a newsletter.
 */
function newsletterglue_archive_rewrite_rules() {

    add_rewrite_rule(
        '^newsletter/(.*)/(.*)/?$',
        'index.php?post_type=newsletterglue&name=$matches[2]',
        'top'
    );

	if ( ! get_option( 'newsletterglue_flushed_rewrite' ) ) {
		flush_rewrite_rules(); // use only once
		update_option( 'newsletterglue_flushed_rewrite', 'yes' );
	}

}
add_action( 'init', 'newsletterglue_archive_rewrite_rules' );
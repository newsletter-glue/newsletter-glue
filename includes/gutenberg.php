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

	$use_blocks = get_option( 'newsletterglue_use_blocks' );

	$blocks = array(
		'newsletterglue_block_show_hide_content' => array(
			'title'			=> __( 'Show/hide content', 'newsletter-glue' ),
			'description'	=> __( 'Hide selected content from your blog/newsletter.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_show_hide_content' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_show_hide_content' ] ) : 'yes',
			'callback'		=> 'newsletterglue_block_show_hide_content',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_show_hide_content/icon/icon.png',
		),
		'newsletterglue_block_author' => array(
			'title'			=> __( 'Author byline', 'newsletter-glue' ),
			'description'	=> __( 'Add that face, name, title/short bio and CTA to each newsletter', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_author' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_author' ] ) : 'no',
			'callback'		=> 'newsletterglue_block_author_byline',
		),
		'newsletterglue_block_callout' => array(
			'title'			=> __( 'Callout cards', 'newsletter-glue' ),
			'description'	=> __( 'Add a callout cards to your blog and newsletter.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_callout' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_callout' ] ) : 'no',
			'callback'		=> 'newsletterglue_block_callout',
		),
	);

	return apply_filters( 'newsletterglue_get_blocks', $blocks );

}

/**
 * Register blocks.
 */
function newsletterglue_register_blocks() {

	$blocks = newsletterglue_get_blocks();

	foreach( $blocks as $block_id => $params ) {
		if ( $params[ 'use_block' ] === 'yes' ) {
			if ( file_exists( NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/' . $block_id . '.php' ) ) {
				include_once NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/' . $block_id . '.php';
			}
			if ( isset( $params[ 'callback' ] ) && function_exists( $params[ 'callback' ] ) ) {
				call_user_func( $params[ 'callback' ] );
			}
		}
	}

}
add_action( 'init', 'newsletterglue_register_blocks' );

/**
 * Add our block category.
 */
function newsletterglue_add_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'newsletterglue-blocks',
				'title' => __( 'Newsletter Glue', 'newsletter-glue' ),
			),
		)
	);
}
add_filter( 'block_categories', 'newsletterglue_add_block_category', 10, 2 );
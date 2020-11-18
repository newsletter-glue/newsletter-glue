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
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_show_hide_content/icon/icon.svg',
		),
		'newsletterglue_block_form' => array(
			'title'			=> __( 'Subscriber form', 'newsletter-glue' ),
			'description'	=> __( 'New subscribers can sign up to your mailing list with this form.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_form' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_form' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_form/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
		'newsletterglue_block_social' => array(
			'title'			=> __( 'Social embed', 'newsletter-glue' ),
			'description'	=> __( 'Embed posts from social media by pasting a link.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_social' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_social' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_social/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
		'newsletterglue_block_article' => array(
			'title'			=> __( 'Article embeds', 'newsletter-glue' ),
			'description'	=> __( 'Bulk embed articles and customise their layout.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_article' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_article' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_article/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
		'newsletterglue_block_author' => array(
			'title'			=> __( 'Author byline', 'newsletter-glue' ),
			'description'	=> __( 'Add an author byline and follow button to your newsletter.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_author' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_author' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
		'newsletterglue_block_callout' => array(
			'title'			=> __( 'Callout card', 'newsletter-glue' ),
			'description'	=> __( 'Customise the background and border of this card to help its content stand out.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_callout' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_callout' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_callout/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
		'newsletterglue_block_metadata' => array(
			'title'			=> __( 'Newsletter meta data', 'newsletter-glue' ),
			'description'	=> __( 'Add standard meta data to each post.', 'newsletter-glue' ),
			'use_block'		=> isset( $use_blocks[ 'newsletterglue_block_metadata' ] ) ? sanitize_text_field( $use_blocks[ 'newsletterglue_block_metadata' ] ) : 'no',
			'icon'			=> NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/icon/icon.svg',
			'is_pro'		=> 'yes',
		),
	);

	return apply_filters( 'newsletterglue_get_blocks', $blocks );

}

/**
 * Add our block category.
 */
function newsletterglue_add_block_category( $categories, $post ) {

	return array_merge(
		$categories,
		array(
			array(
				'slug' 		=> 'newsletterglue-blocks',
				'title' 	=> __( 'Newsletter Glue', 'newsletter-glue' ),
			),
		)
	);

}
add_filter( 'block_categories', 'newsletterglue_add_block_category', 10, 2 );

/**
 * Enqueues the required frontend scripts.
 */
function newsletterglue_load_frontend_scripts( $hook ) {
	global $wp_scripts;

	$js_dir  = NGL_PLUGIN_URL . 'assets/js/';
	$css_dir = NGL_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register scripts.
	wp_register_script( 'newsletterglue_gutenberg', $js_dir . 'frontend/gutenberg' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_enqueue_script( 'newsletterglue_gutenberg' );

	wp_localize_script( 'newsletterglue_gutenberg', 'newsletterglue_gutenberg', apply_filters( 'newsletterglue_gutenberg_js_params',
		array(
			'ajaxurl'    		=> newsletterglue_get_ajax_url(),
			'ajaxnonce'			=> wp_create_nonce( 'newsletterglue-ajax-nonce' ),
			'please_wait'		=> __( 'Please wait...', 'newsletter-glue' ),
		)
	) );

	wp_register_style( 'newsletterglue_gutenberg', $css_dir . 'gutenberg' . $suffix . '.css', array(), NGL_VERSION );
	wp_enqueue_style( 'newsletterglue_gutenberg' );

}
add_action( 'wp_enqueue_scripts', 'newsletterglue_load_frontend_scripts', 100 );
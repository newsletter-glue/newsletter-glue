<?php
/**
 * Gutenberg.
 */

/**
 * Author byline block.
 */
function newsletterglue_block_author_byline() {

	$js_dir    	= NGL_PLUGIN_URL . 'assets/gutenberg/';
	$js_path   	= NGL_PLUGIN_DIR . 'assets/gutenberg/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	wp_register_script( 'newsletterglue-author-block', $js_dir . 'ngl-author' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );

	wp_register_style( 'newsletterglue-author-block-style', $js_dir . 'ngl-author-style' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/author', array(
		'attributes'      => array(
			'foo' => array(
				'type' 		=> 'string',
				'default'	=> 100,
			),
		),
		'editor_script'   => 'newsletterglue-author-block',
		'style'           => 'newsletterglue-author-block-style',
		'render_callback' => 'newsletterglue_author_block_render',
	) );

	add_shortcode( 'newsletterglue_author_block_render', 'newsletterglue_author_block_render' );

}

/**
 * Render the author block.
 */
function newsletterglue_author_block_render( $attributes ) {

	ob_start();

	include_once NGL_PLUGIN_DIR . 'templates/author-byline.php';

	return ob_get_clean();

}
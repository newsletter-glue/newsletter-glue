<?php
/**
 * Gutenberg.
 */

/**
 * Callout card.
 */
function newsletterglue_block_callout() {

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_callout/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_callout/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	wp_register_script( 'newsletterglue-callout-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );

	wp_register_style( 'newsletterglue-callout-block', $css_dir . 'block' . $suffix . '.css', array(), time() );
	wp_register_style( 'newsletterglue-callout-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/callout', array(
		'attributes'      => array(

		),
		'editor_script'   => 'newsletterglue-callout-block',
		'editor_style'    => 'newsletterglue-callout-block',
		'style'           => 'newsletterglue-callout-block-style',
		'render_callback' => 'newsletterglue_callout_block_render',
	) );

}

/**
 * Render the author block.
 */
function newsletterglue_callout_block_render( $attributes, $content ) {

	return $content;

}
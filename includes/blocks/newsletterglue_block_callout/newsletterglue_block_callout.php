<?php
/**
 * Gutenberg.
 */

/**
 * Update the show/hide block.
 */
function newsletterglue_block_callout_save() {

	delete_option( 'newsletterglue_block_callout' );

	$defaults = get_option( 'newsletterglue_block_callout' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'newsletterglue_block_callout_show_in_email' ] ) ) {
		$defaults[ 'show_in_email' ] = true;
	} else {
		$defaults[ 'show_in_email' ] = false;
	}

	if ( isset( $_POST[ 'newsletterglue_block_callout_show_in_blog' ] ) ) {
		$defaults[ 'show_in_blog' ] = true;
	} else {
		$defaults[ 'show_in_blog' ] = false;
	}

	update_option( 'newsletterglue_block_callout', $defaults );

	return $defaults;
}

/**
 * Callout card.
 */
function newsletterglue_block_callout() {

	$defaults = get_option( 'newsletterglue_block_callout' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_callout/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_callout/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	wp_register_script( 'newsletterglue-callout-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-callout-block', 'newsletterglue_block_callout', $defaults );

	wp_register_style( 'newsletterglue-callout-block', $css_dir . 'block' . $suffix . '.css', array(), time() );
	wp_register_style( 'newsletterglue-callout-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/callout', array(
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

	$defaults = get_option( 'newsletterglue_block_callout' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$show_in_blog  = isset( $attributes[ 'show_in_blog' ] ) ? $attributes[ 'show_in_blog' ] : $defaults[ 'show_in_blog' ];
	$show_in_email = isset( $attributes[ 'show_in_email' ] ) ? $attributes[ 'show_in_email' ] : $defaults[ 'show_in_email' ];

	// Hidden from blog.
	if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
		$content = '';
	}

	// Hidden from email.
	if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
		$content = '';
	}

	return $content;

}

/**
 * Add cutom css.
 */
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_callout_block_css' );
function newsletterglue_add_callout_block_css() { ?>

.wp-block-newsletterglue-callout p {
	margin: 0 !important;
}

<?php
}
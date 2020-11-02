<?php
/**
 * Gutenberg.
 */

/**
 * Update the block.
 */
function newsletterglue_block_metadata_save() {

	delete_option( 'newsletterglue_block_metadata' );

	$defaults = get_option( 'newsletterglue_block_metadata' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'newsletterglue_block_metadata_show_in_email' ] ) ) {
		$defaults[ 'show_in_email' ] = true;
	} else {
		$defaults[ 'show_in_email' ] = false;
	}

	if ( isset( $_POST[ 'newsletterglue_block_metadata_show_in_blog' ] ) ) {
		$defaults[ 'show_in_blog' ] = true;
	} else {
		$defaults[ 'show_in_blog' ] = false;
	}

	update_option( 'newsletterglue_block_metadata', $defaults );

	return $defaults;
}

/**
 * Meta data block.
 */
function newsletterglue_block_metadata() {

	$defaults = get_option( 'newsletterglue_block_metadata' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/css/';

	$defaults[ 'assets_uri' ] =  NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_metadata/img/';

	$suffix  = '';

	wp_register_script( 'newsletterglue-metadata-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-metadata-block', 'newsletterglue_block_metadata', $defaults );

	wp_register_style( 'newsletterglue-metadata-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/metadata', array(
		'editor_script'   => 'newsletterglue-metadata-block',
		'style'           => 'newsletterglue-metadata-block-style',
		'render_callback' => 'newsletterglue_metadata_block_render',
	) );

}

/**
 * Render the author block.
 */
function newsletterglue_metadata_block_render( $attributes, $content ) {

	$defaults = get_option( 'newsletterglue_block_metadata' );
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
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_metadata_css' );
function newsletterglue_add_metadata_css() { ?>

<?php
}
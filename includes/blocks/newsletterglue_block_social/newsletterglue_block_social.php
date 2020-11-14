<?php
/**
 * Gutenberg.
 */

/**
 * Update the block.
 */
function newsletterglue_block_social_save() {

	delete_option( 'newsletterglue_block_social' );

	$defaults = get_option( 'newsletterglue_block_social' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'newsletterglue_block_social_show_in_email' ] ) ) {
		$defaults[ 'show_in_email' ] = true;
	} else {
		$defaults[ 'show_in_email' ] = false;
	}

	if ( isset( $_POST[ 'newsletterglue_block_social_show_in_blog' ] ) ) {
		$defaults[ 'show_in_blog' ] = true;
	} else {
		$defaults[ 'show_in_blog' ] = false;
	}

	update_option( 'newsletterglue_block_social', $defaults );

	return $defaults;
}

/**
 * Meta data block.
 */
function newsletterglue_block_social() {

	$defaults = get_option( 'newsletterglue_block_social' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_social/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_social/css/';

	$suffix  = '';

	wp_register_script( 'newsletterglue-social-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-social-block', 'newsletterglue_block_social', $defaults );

	wp_register_style( 'newsletterglue-social-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/social', array(
		'editor_script'   => 'newsletterglue-social-block',
		'style'           => 'newsletterglue-social-block-style',
		'render_callback' => 'newsletterglue_social_block_render',
		'attributes'	  => array(
			'show_in_blog' => array(
				'type' 		=> 'boolean',
				'default' 	=> $defaults[ 'show_in_blog' ],
			),
			'show_in_email' => array(
				'type' 		=> 'boolean',
				'default' 	=> $defaults[ 'show_in_email' ],
			),
		),
	) );

}

/**
 * Render the author block.
 */
function newsletterglue_social_block_render( $attributes, $content ) {

	ob_start();

	$supported_embeds = array(
		'twitter' => array(
			'icon'	=> '<svg width="100" height="100" viewBox="0 0 32 32"><path d="M2 4c4 4 8 8 13 7a6 6 0 0 1 7-7 6 6 0 0 1 4 2 8 8 0 0 0 5-2 8 8 0 0 1-3 4 8 8 0 0 0 4-1 8 8 0 0 1-4 4 18 18 0 0 1-18 19 18 18 0 0 1-10-3 12 12 0 0 0 8-3 8 8 0 0 1-5-4 8 8 0 0 0 3-.5A8 8 0 0 1 0 12a8 8 0 0 0 3 1 8 8 0 0 1-1-9"/></svg>'
		),
	);

	include_once NGL_PLUGIN_DIR . 'includes/blocks/newsletterglue_block_social/templates/embed.php';

	return ob_get_clean();

}

/**
 * Add cutom css.
 */
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_social_embed_css' );
function newsletterglue_add_social_embed_css() { ?>

<?php
}
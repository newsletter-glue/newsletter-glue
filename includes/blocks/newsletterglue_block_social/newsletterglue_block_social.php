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
			'block_id'		=> array(
				'type'		=> 'string',
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

	$block_id 	= isset( $attributes[ 'block_id' ] ) ? $attributes[ 'block_id' ] : '';
	$url		= newsletterglue_get_embed_url( $block_id );
	$content    = newsletterg_get_platform_embed( $url );
	$html		= null;

	if ( ! empty( $content[ 'html' ] ) ) {
		$html = $content[ 'html' ];
	}

	// On frontend. no html.
	if ( ! is_admin() && ! defined( 'REST_REQUEST' ) && ! $html ) {
		return ob_get_clean();
	}

	if ( ! empty( $content[ 'error' ] ) ) {
		$html = $content[ 'error' ];
	}

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

/**
 * Get embed by block ID.
 */
function newsletterglue_get_embed( $block_id ) {

	$html = get_option( 'ngl_embed_' . str_replace( '-', '_', $block_id ) );

	return $html;

}

/**
 * Get embed URL by block ID.
 */
function newsletterglue_get_embed_url( $block_id ) {

	$url = get_option( 'ngl_embed_url_' . str_replace( '-', '_', $block_id ) );

	return $url;

}

/**
 * Get embed with AJAX.
 */
function newsletterglue_ajax_get_embed() {

	$error = '';

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	$url 		= isset( $_REQUEST[ 'url' ] ) ? sanitize_text_field( $_REQUEST[ 'url' ] ) : '';
	$block_id 	= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
	$block_id	= str_replace( '-', '_', $block_id );

	if ( empty( $block_id ) ) {
		wp_die( -1 );
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE || esc_url_raw( $url ) !== $url ) {
		$error = __( 'Please enter a valid URL.', 'newsletter-glue' );
	}

	if ( ! empty( $error ) ) {
		delete_option( 'ngl_embed_' . $block_id );
		delete_option( 'ngl_embed_url_' . $block_id );

		wp_send_json( array( 'error' => $error ) );
	}

	$result = newsletterg_get_platform_embed( $url );

	if ( ! empty( $result[ 'html' ] ) ) {
		update_option( 'ngl_embed_' . $block_id, $result[ 'html' ] );
		update_option( 'ngl_embed_url_' . $block_id, $url );
	}

	if ( ! $result ) {
		$result[ 'error' ] = __( 'An error has occured.', 'newsletter-glue' );
	}

	wp_send_json( $result );

}
add_action( 'wp_ajax_newsletterglue_ajax_get_embed', 'newsletterglue_ajax_get_embed' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_embed', 'newsletterglue_ajax_get_embed' );

/**
 * Get platform embed by URL.
 */
function newsletterg_get_platform_embed( $url ) {

	$result = false;

	// Twitter embed.
	if ( strstr( $url, 'twitter' ) ) {
		$result = newsletterglue_get_twitter_embed( $url );
	}

	return $result;

}

/**
 * Twitter.
 */
function newsletterglue_get_twitter_embed( $url ) {

	$response 	= array();

	$request 	= wp_remote_get( 'https://publish.twitter.com/oembed?url=' . urlencode( $url ) . '&omit_script=false' );

	if ( is_wp_error( $request ) ) {
		$response[ 'error' ] = __( 'An error has occured.', 'newsletter-glue' );
		return $response;
	}

	$result 	= json_decode( $request[ 'body' ] );
	$html   	= isset( $result->html ) ? $result->html : '';

	$response[ 'html' ] = $html;

	return $response;

}
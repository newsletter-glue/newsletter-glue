<?php
/**
 * Gutenberg.
 */

/**
 * Update the show/hide block.
 */
function newsletterglue_block_show_hide_content_save() {

	delete_option( 'newsletterglue_block_show_hide_content' );

	$defaults = get_option( 'newsletterglue_block_show_hide_content' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'showemail' ] ) ) {
		$defaults[ 'showemail' ] = true;
	} else {
		$defaults[ 'showemail' ] = false;
	}

	if ( isset( $_POST[ 'showblog' ] ) ) {
		$defaults[ 'showblog' ] = true;
	} else {
		$defaults[ 'showblog' ] = false;
	}

	update_option( 'newsletterglue_block_show_hide_content', $defaults );

	return $defaults;
}

/**
 * Show/hide content block.
 */
function newsletterglue_block_show_hide_content() {

	$defaults = get_option( 'newsletterglue_block_show_hide_content' );
	if ( ! $defaults ) {
		$defaults = array(
			'showemail'	=> true,
			'showblog'	=> false,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'assets/gutenberg/';
	$js_path   	= NGL_PLUGIN_DIR . 'assets/gutenberg/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	wp_register_script( 'newsletterglue-group-block', $js_dir . 'ngl-group' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-group-block', 'newsletterglue_block_show_hide_content', $defaults );

	wp_register_style( 'newsletterglue-group-block', $js_dir . 'ngl-group' . $suffix . '.css', array(), time() );
	wp_register_style( 'newsletterglue-group-block-style', $js_dir . 'ngl-group-style' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/group', array(
		'editor_script' 	=> 'newsletterglue-group-block',
		'editor_style'  	=> 'newsletterglue-group-block',
        'style'         	=> 'newsletterglue-group-block-style',
		'attributes'		=> array(
			'showblog'	=> array(
				'type'	=> 'boolean',
			),
			'showemail' => array(
				'type'	=> 'boolean'
			),
		),
		'render_callback'	=> function( $attributes, $content ) {

			$defaults = get_option( 'newsletterglue_block_show_hide_content' );
			if ( ! $defaults ) {
				$defaults = array(
					'showemail'	=> true,
					'showblog'	=> false,
				);
			}

			$show_in_blog  = isset( $attributes[ 'showblog' ] ) ? $attributes[ 'showblog' ] : $defaults[ 'showblog' ];
			$show_in_email = isset( $attributes[ 'showemail' ] ) ? $attributes[ 'showemail' ] : $defaults[ 'showemail' ];

			// Hidden from blog.
			if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
				$content = preg_replace('#<section class="wp-block-newsletterglue-group">(.*?)</section>#s', '', $content );
			}

			// Hidden from email.
			if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
				$content = str_replace( 'wp-block-newsletterglue-group', 'wp-block-newsletterglue-group ngl-hide-in-email', $content );
				$content = preg_replace('#<section class="wp-block-newsletterglue-group ngl-hide-in-email">(.*?)</section>#s', '', $content );
			}

			return $content;

		}
	) );

}
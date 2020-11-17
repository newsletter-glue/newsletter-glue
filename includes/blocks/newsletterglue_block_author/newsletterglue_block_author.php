<?php
/**
 * Gutenberg.
 */

/**
 * Update the block.
 */
function newsletterglue_block_author_save() {

	delete_option( 'newsletterglue_block_author' );

	$defaults = get_option( 'newsletterglue_block_author' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'newsletterglue_block_author_show_in_email' ] ) ) {
		$defaults[ 'show_in_email' ] = true;
	} else {
		$defaults[ 'show_in_email' ] = false;
	}

	if ( isset( $_POST[ 'newsletterglue_block_author_show_in_blog' ] ) ) {
		$defaults[ 'show_in_blog' ] = true;
	} else {
		$defaults[ 'show_in_blog' ] = false;
	}

	update_option( 'newsletterglue_block_author', $defaults );

	return $defaults;
}

/**
 * Author byline block.
 */
function newsletterglue_block_author() {

	$defaults = get_option( 'newsletterglue_block_author' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/css/';

	$suffix  = '';

	$defaults[ 'assets_uri' ] =  NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/';

	wp_register_script( 'newsletterglue-author-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-author-block', 'newsletterglue_block_author', $defaults );

	wp_register_style( 'newsletterglue-author-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/author', array(
		'editor_script'   => 'newsletterglue-author-block',
		'style'           => 'newsletterglue-author-block-style',
		'render_callback' => 'newsletterglue_author_block_render',
	) );

}

/**
 * Render the author block.
 */
function newsletterglue_author_block_render( $attributes, $content ) {

	$defaults = get_option( 'newsletterglue_block_author' );
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
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_author_byline_css' );
function newsletterglue_add_author_byline_css() { ?>

.ngl-author {
	display: flex;
	padding: 20px 0;
	line-height: 1;
}

.ngl-author-pic {
	width: 50px;
	min-width: 50px;
	margin: 0 12px 0 0;
}

.ngl-author-pic img {
	margin: 0 !important;
	display: block;
	overflow: hidden;
	border-radius: 999px;
}

.ngl-author-name {
	font-weight: bold;
	min-width: 20px;
	padding: 0 0 4px;
	font-size: 14px;
	line-height: 14px;
}

.ngl-author-bio {
	margin: 0;
	padding: 0 0 4px;
	font-size: 14px;
	line-height: 18px;
}

.ngl-author-btn {
	color: #fff !important;
	text-decoration: none !important;
	padding: 4px 8px;
	display: inline-flex;
	align-items: center;
	border: 2px solid transparent;
	font-size: 12px;
	line-height: 16px;
}

.ngl-author-btn:hover {
	color: #fff !important;
}

.ngl-author-btn-text {
	min-width: 20px;
}

.ngl-author-cta img {
	display: inline-block;
    width: 16px;
    height: 16px;
	margin: 0 4px 0 0 !important;
}

.ngl-author-cta .ngl-author-btn-outlined {
	background-color: transparent !important;
	border: 2px solid transparent;
	color: #444 !important;
}

.ngl-author-twitter { background-color: #1DA1F2; }
.ngl-author-instagram { background-color: #ed4956; }
.ngl-author-facebook { background-color: #1877F2; }
.ngl-author-twitch { background-color: #9047FF; }
.ngl-author-tiktok { background-color: #fe2c55; }
.ngl-author-youtube { background-color: #FF0000; }

.ngl-author-btn-outlined.ngl-author-twitter { border-color: #1DA1F2 !important; }
.ngl-author-btn-outlined.ngl-author-instagram { border-color: #ed4956 !important; }
.ngl-author-btn-outlined.ngl-author-facebook { border-color: #1877F2 !important; }
.ngl-author-btn-outlined.ngl-author-twitch { border-color: #9047FF !important; }
.ngl-author-btn-outlined.ngl-author-tiktok { border-color: #fe2c55 !important; }
.ngl-author-btn-outlined.ngl-author-youtube { border-color: #FF0000 !important; }

<?php
}
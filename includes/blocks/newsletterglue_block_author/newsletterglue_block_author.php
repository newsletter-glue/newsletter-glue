<?php
/**
 * Gutenberg.
 */

/**
 * Update the show/hide block.
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
function newsletterglue_block_author_byline() {

	$defaults = get_option( 'newsletterglue_block_author' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$suffix  = '';

	wp_register_script( 'newsletterglue-author-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-author-block', 'newsletterglue_block_author', $defaults );

	wp_register_style( 'newsletterglue-author-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/author', array(
		'attributes'      => array(
			'social' 		=> array(
				'type' 		=> 'string',
			),
			'social_user' 	=> array(
				'type' 		=> 'string',
			),
			'author_name' 	=> array(
				'type' 		=> 'string',
			),
			'author_bio'	=> array(
				'type'		=> 'string',
			),
			'show_in_blog'	=> array(
				'type'		=> 'boolean',
			),
			'show_in_email'	=> array(
				'type'		=> 'boolean',
			),
			'profile_pic'	=> array(
				'type'		=> 'string',
			),
			'button_text'	=> array(
				'type'		=> 'string',
			),
			'border_radius' => array(
				'type'		=> 'number',
			),
			'button_style'  => array(
				'type'		=> 'string',
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
	global $post, $ng_post;

	// Hide in blog.
	$is_backend = defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_STRING );

	$defaults = get_option( 'newsletterglue_block_author' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> true,
		);
	}

	$show_in_blog  = isset( $attributes[ 'show_in_blog' ] ) ? $attributes[ 'show_in_blog' ] : $defaults[ 'show_in_blog' ];
	$show_in_email = isset( $attributes[ 'show_in_email' ] ) ? $attributes[ 'show_in_email' ] : $defaults[ 'show_in_email' ];

	// Hide in blog.
	if ( ! $is_backend ) {
		if ( empty( $show_in_blog ) && ! defined( 'NGL_IN_EMAIL' ) ) {
			return;
		}
	}

	// Hide in email.
	if ( empty( $show_in_email ) && defined( 'NGL_IN_EMAIL' ) ) {
		return;
	}

	ob_start();

	if ( defined( 'NGL_IN_EMAIL' ) ) {
		$post = $ng_post;
	}

	$user_id		= '';
	$name 			= isset( $attributes[ 'author_name' ] ) ? $attributes[ 'author_name' ] : '';
	$bio			= isset( $attributes[ 'author_bio' ] ) ? $attributes[ 'author_bio' ] : '';
	$social 		= isset( $attributes[ 'social' ] ) ? $attributes[ 'social' ] : '';
	$social_user 	= isset( $attributes[ 'social_user' ] ) ? $attributes[ 'social_user' ] : '';
	$profile_pic 	= isset( $attributes[ 'profile_pic' ] ) ? $attributes[ 'profile_pic' ] : '';
	$button_text 	= isset( $attributes[ 'button_text' ] ) ? $attributes[ 'button_text' ] : __( 'Follow', 'newsletter-glue' );
	$border_radius 	= isset( $attributes[ 'border_radius' ] ) ? $attributes[ 'border_radius' ] : 5;
	$button_style 	= isset( $attributes[ 'button_style' ] ) ? $attributes[ 'button_style' ] : 'solid';
	$social_url		= '';
	$social_icon	= '';

	$outline = '';
	if ( $button_style === 'outlined' ) {
		$outline = '-fill';
	}

	// Set social platform data.
	if ( $social_user ) {
		$social_user = esc_attr( $social_user );
		if ( ! $social ) {
			$social 		= 'twitter';
			$social_icon	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/twitter' . $outline . '.png" />';
		}
		if ( $social == 'twitter' ) {
			$social_url 	= 'https://twitter.com/' . $social_user;
			$social_icon	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/twitter' . $outline . '.png" />';
		}
		if ( $social == 'instagram' ) {
			$social_url 	= 'https://instagram.com/' . $social_user;
			$social_icon 	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/instagram' . $outline . '.png" />';
		}
		if ( $social == 'facebook' ) {
			$social_url 	= 'https://facebook.com/' . $social_user;
			$social_icon 	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/facebook' . $outline . '.png" />';
		}
		if ( $social == 'twitch' ) {
			$social_url 	= 'https://twitch.tv/' . $social_user;
			$social_icon 	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/twitch' . $outline . '.png" />';
		}
		if ( $social == 'tiktok' ) {
			$social_url		= 'https://www.tiktok.com/@' . $social_user;
			$social_icon 	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/tiktok' . $outline . '.png" />';
		}
		if ( $social == 'youtube' ) {
			$social_url		= 'https://www.youtube.com/user/' . $social_user;
			$social_icon 	= '<img src="' . NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_author/img/youtube' . $outline . '.png" />';
		}
	}

	// Auto generated data.
	if ( isset( $post->ID ) ) {
		$author = $post->post_author;
		if ( ! $name ) {
			$name = get_the_author_meta( 'display_name', $author );
		}
		if ( ! $bio ) {
			$bio = get_the_author_meta( 'description', $author );
		}
		$user_id = $author;
	}

	// Author byline template.
	include_once NGL_PLUGIN_DIR . 'includes/blocks/newsletterglue_block_author/templates/author-byline.php';

	return ob_get_clean();

}

/**
 * Add cutom css.
 */
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_author_byline_css' );
function newsletterglue_add_author_byline_css() { ?>

.ngl-author {
	display: flex;
	padding: 20px 0;
	align-items: center;
	line-height: 1;
}

.ngl-author-pic {
	width: 70px;
	margin: 0 15px 0 0;
}

.ngl-author-pic img {
	margin: 0 !important;
	display: block;
	overflow: hidden;
	border-radius: 999px;
}

.ngl-author-name {
	display: flex;
	align-items: center;
	line-height: 32px;
}

.ngl-author-name-1 {
	font-weight: bold;
	margin: 0 15px 0 0;
	font-size: 16px;
}

.ngl-author-cta a {
	color: #fff !important;
	text-decoration: none !important;
	padding: 6px 12px;
	display: inline-flex;
	line-height: 18px;
	align-items: center;
	font-size: 14px;
}

.ngl-author-cta a:hover {
	color: #fff !important;
}

.ngl-author-cta img {
	display: inline-block;
    width: 16px;
    height: 16px;
	margin: 0 4px 0 0 !important;
}

.ngl-author-bio {
	margin: 12px 0 0;
	font-size: 14px;
	line-height: 22px;
}

.ngl-author-cta a.ngl-author-btn-outlined {
	background-color: transparent !important;
	border: 2px solid transparent;
	color: #444 !important;
}

.ngl-author-twitter { background-color: #1da1f2; }
.ngl-author-instagram { background-color: #DD2A7B; }
.ngl-author-facebook { background-color: #3b5998; }
.ngl-author-twitch { background-color: #6441a5; }
.ngl-author-tiktok { background-color: #EE1D52; }
.ngl-author-youtube { background-color: #c4302b; }

.ngl-author-btn-outlined.ngl-author-twitter { border-color: #1da1f2 !important; }
.ngl-author-btn-outlined.ngl-author-instagram { border-color: #DD2A7B !important; }
.ngl-author-btn-outlined.ngl-author-facebook { border-color: #3b5998 !important; }
.ngl-author-btn-outlined.ngl-author-twitch { border-color: #6441a5 !important; }
.ngl-author-btn-outlined.ngl-author-tiktok { border-color: #EE1D52 !important; }
.ngl-author-btn-outlined.ngl-author-youtube { border-color: #c4302b !important; }

<?php
}
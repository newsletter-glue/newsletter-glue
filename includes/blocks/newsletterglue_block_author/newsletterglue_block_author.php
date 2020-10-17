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
			'button_border'	=> array(
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
	$button_border 	= isset( $attributes[ 'button_border' ] ) ? $attributes[ 'button_border' ] : '#3400FF';
	$social_url		= '';
	$social_icon	= '<svg fill="' . $button_border . '" width="100" height="100"  viewBox="0 0 32 32"><title>twitter icon</title><path d="M2 4 C6 8 10 12 15 11 A6 6 0 0 1 22 4 A6 6 0 0 1 26 6 A8 8 0 0 0 31 4 A8 8 0 0 1 28 8 A8 8 0 0 0 32 7 A8 8 0 0 1 28 11 A18 18 0 0 1 10 30 A18 18 0 0 1 0 27 A12 12 0 0 0 8 24 A8 8 0 0 1 3 20 A8 8 0 0 0 6 19.5 A8 8 0 0 1 0 12 A8 8 0 0 0 3 13 A8 8 0 0 1 2 4"></path></svg>';

	// Set social platform data.
	if ( $social_user ) {
		$social_user = esc_attr( $social_user );
		if ( $social == 'twitter' ) {
			$social_url 	= 'https://twitter.com/' . $social_user;
		}
		if ( $social == 'instagram' ) {
			$social_url 	= 'https://instagram.com/' . $social_user;
			$social_icon 	= '<svg fill="' . $button_border . '" id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="100" height="100"
	 viewBox="0 0 551.034 551.034" style="enable-background:new 0 0 551.034 551.034;" xml:space="preserve">
		<path class="logo" id="XMLID_17_" d="M386.878,0H164.156C73.64,0,0,73.64,0,164.156v222.722 c0,90.516,73.64,164.156,164.156,164.156h222.722c90.516,0,164.156-73.64,164.156-164.156V164.156 C551.033,73.64,477.393,0,386.878,0z M495.6,386.878c0,60.045-48.677,108.722-108.722,108.722H164.156 c-60.045,0-108.722-48.677-108.722-108.722V164.156c0-60.046,48.677-108.722,108.722-108.722h222.722 c60.045,0,108.722,48.676,108.722,108.722L495.6,386.878L495.6,386.878z"/>
		<path id="XMLID_81_" d="M275.517,133C196.933,133,133,196.933,133,275.516 s63.933,142.517,142.517,142.517S418.034,354.1,418.034,275.516S354.101,133,275.517,133z M275.517,362.6 c-48.095,0-87.083-38.988-87.083-87.083s38.989-87.083,87.083-87.083c48.095,0,87.083,38.988,87.083,87.083 C362.6,323.611,323.611,362.6,275.517,362.6z"/>
		<circle id="XMLID_83_" cx="418.306" cy="134.072" r="34.149"/></svg>';
		}
		if ( $social == 'facebook' ) {
			$social_url 	= 'https://facebook.com/' . $social_user;
			$social_icon 	= '<svg style="background: ' . $button_border . ';" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin meet" viewBox="0 0 262 266.895"><path id="path3087" fill="#FFF" d="M182.409,262.307v-99.803h33.499l5.016-38.895h-38.515V98.777c0-11.261,3.127-18.935,19.275-18.935 l20.596-0.009V45.045c-3.562-0.474-15.788-1.533-30.012-1.533c-29.695,0-50.025,18.126-50.025,51.413v28.684h-33.585v38.895h33.585 v99.803H182.409z"></path></svg>';
		}
		if ( $social == 'twitch' ) {
			$social_url 	= 'https://twitch.tv/' . $social_user;
			$social_icon 	= '<svg fill="' . $button_border . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M2.149 0l-1.612 4.119v16.836h5.731v3.045h3.224l3.045-3.045h4.657l6.269-6.269v-14.686h-21.314zm19.164 13.612l-3.582 3.582h-5.731l-3.045 3.045v-3.045h-4.836v-15.045h17.194v11.463zm-3.582-7.343v6.262h-2.149v-6.262h2.149zm-5.731 0v6.262h-2.149v-6.262h2.149z" fill-rule="evenodd" clip-rule="evenodd"/></svg>';
		}
		if ( $social == 'tiktok' ) {
			$social_url		= 'https://www.tiktok.com/@' . $social_user;
			$social_icon 	= '<svg fill="' . $button_border . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2859 3333" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd"><path d="M2081 0c55 473 319 755 778 785v532c-266 26-499-61-770-225v995c0 1264-1378 1659-1932 753-356-583-138-1606 1004-1647v561c-87 14-180 36-265 65-254 86-398 247-358 531 77 544 1075 705 992-358V1h551z"></path></svg>';

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
	max-width: 70px;
	margin: 0 15px 0 0;
}

.ngl-author-pic img {
	display: block;
	overflow: hidden;
	border-radius: 999px;
	margin: 0 !important;
}

.ngl-author-name {
	display: flex;
	align-items: center;
	line-height: 32px;
}

.ngl-author-name-1 {
	color: rgba(0, 0, 0, 0.6);
	font-weight: bold;
	margin: 0 15px 0 0;
	font-size: 16px;
}

.ngl-author-cta a {
	border: 1px solid #3400FF;
	border-radius: 5px;
	color: rgba(0, 0, 0, 0.6) !important;
	text-decoration: none !important;
	padding: 6px 12px;
	display: inline-flex;
	line-height: 1;
	align-items: center;
	font-size: 14px;
}

.ngl-author-cta a:hover {
	border: 1px solid #3400FF;
	color: rgba(0, 0, 0, 0.6) !important;
}

.ngl-author-cta svg {
	display: inline-block;
    width: 16px;
    height: 16px;
    fill: #3400FF;
	margin: 0 8px 0 0;
}

.ngl-author-bio {
	margin: 12px 0 0;
	color: rgba(0, 0, 0, 0.6);
	font-size: 14px;
}

<?php
}
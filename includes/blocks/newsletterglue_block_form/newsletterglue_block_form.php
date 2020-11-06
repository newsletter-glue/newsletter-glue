<?php
/**
 * Gutenberg.
 */

/**
 * Update the block.
 */
function newsletterglue_block_form_save() {

	delete_option( 'newsletterglue_block_form' );

	$defaults = get_option( 'newsletterglue_block_form' );

	if ( ! $defaults ) {
		$defaults = array();
	}

	if ( isset( $_POST[ 'newsletterglue_block_form_show_in_email' ] ) ) {
		$defaults[ 'show_in_email' ] = true;
	} else {
		$defaults[ 'show_in_email' ] = false;
	}

	if ( isset( $_POST[ 'newsletterglue_block_form_show_in_blog' ] ) ) {
		$defaults[ 'show_in_blog' ] = true;
	} else {
		$defaults[ 'show_in_blog' ] = false;
	}

	update_option( 'newsletterglue_block_form', $defaults );

	return $defaults;
}

/**
 * Meta data block.
 */
function newsletterglue_block_form() {

	$defaults = get_option( 'newsletterglue_block_form' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> false,
		);
	}

	$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_form/js/';
	$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/newsletterglue_block_form/css/';

	$suffix  = '';

	wp_register_script( 'newsletterglue-form-block', $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
	wp_localize_script( 'newsletterglue-form-block', 'newsletterglue_block_form', $defaults );

	wp_register_style( 'newsletterglue-form-block-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

	register_block_type( 'newsletterglue/form', array(
		'editor_script'   => 'newsletterglue-form-block',
		'style'           => 'newsletterglue-form-block-style',
		'render_callback' => 'newsletterglue_form_block_render',
	) );

}

/**
 * Render the author block.
 */
function newsletterglue_form_block_render( $attributes, $content ) {

	$defaults = get_option( 'newsletterglue_block_form' );
	if ( ! $defaults ) {
		$defaults = array(
			'show_in_blog'	=> true,
			'show_in_email'	=> false,
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

	if ( defined( 'NGL_IN_EMAIL' ) ) {
		$content = str_replace( '<button', '<a href="{post_permalink}"', $content );
	}

	$content = str_replace( 'class="wp-block-newsletterglue-form', 'data-app="' . newsletterglue_default_connection() . '" class="wp-block-newsletterglue-form', $content );

	return $content;

}

/**
 * Add cutom css.
 */
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_form_css' );
function newsletterglue_add_form_css() { ?>

.ngl-form {
	max-width: 100% !important;
	margin-top: 25px !important;
	margin-bottom: 25px !important;
	position: relative;
}

.ngl-form h2 {
	font-size: 24px !important;
}

.ngl-form-input-text {
	border: 1px solid #aaa;
	padding: 5px 14px;
	border-radius: 0;
	background: #fff;
	height: 40px;
	width: 100%;
	box-sizing: border-box;
}

.ngl-form-field {
	margin: 0 0 25px;
	text-align: left !important;
	display: none !important;
}

.ngl-form-label {
	user-select: none;
}

.ngl-form-button {
    background: #3400FF;
    color: #fff;
    border-radius: 0;
    cursor: pointer;
    outline: 0;
    box-shadow: none;
    border: none;
    padding: 4px 25px;
	font-size: 16px;
	text-decoration: none !important;
	text-transform: none;
    width: auto !important;
    min-width: 120px !important;
    display: inline-block !important;
	height: 30px !important;
	line-height: 30px !important;
	text-align: center !important;
}

.ngl-form.ngl-portrait {

}

.ngl-form.ngl-portrait .ngl-form-button {
	width: 100%;
	display: block;
}

.ngl-form.ngl-landscape {

}

.ngl-form.ngl-landscape .ngl-form-container {
	display: flex;
	align-items: flex-end;
}

.ngl-form.ngl-landscape .ngl-form-field {
	margin-bottom: 0;
	flex: auto;
}

.ngl-form.ngl-landscape .ngl-form-button {
	text-align: center;
	height: 40px;
	min-width: 180px;
}

.ngl-message-overlay {
	text-align: center;
	width: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
    flex-direction: column;
	opacity: 0;
	transition: opacity 0.25s ease-in-out;
	pointer-events: none;
	visibility: hidden;
	height: 0;
}

.ngl-message-overlay.ngl-show {
	opacity: 1;
	pointer-events: auto;
	visibility: visible;
	min-height: 200px;
	height: auto;
}

.ngl-message-svg-wrap {
	background: #5bca64;
	width: 40px;
	line-height: 40px;
	height: 40px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.ngl-message-overlay-text {
	font-size: 18px;
	margin: 14px 0 0;
}

<?php
}

/**
 * Subscribe a user via a form.
 */
function newsletterglue_block_form_subscribe() {

	$result = 0;

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	// Get app.
	$app 	= isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

	// App Instance.
	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();

	// Prepare data to send to the ESP endpoint.
	foreach( $_POST as $key => $value ) {
		if ( strstr( $key, 'ngl_' ) ) {
			$key 	= str_replace( 'ngl_', '', $key );
			$value 	= sanitize_text_field( $_POST[ $key ] );
			$data[ $key ] = $value;
		}
	}

	if ( method_exists( $api, 'add_user' ) ) {
		$result = $api->add_user( $data );
	}

	// 3rd party hooks.
	do_action( 'newsletterglue_form_block_signup', $app, $api, $data );

	if ( $result > 0 ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}

}
add_action( 'wp_ajax_newsletterglue_block_form_subscribe', 'newsletterglue_block_form_subscribe' );
add_action( 'wp_ajax_nopriv_newsletterglue_block_form_subscribe', 'newsletterglue_block_form_subscribe' );
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
			'show_in_email'	=> true,
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

	$content = str_replace( 'class="wp-block-newsletterglue-form', 'data-app="' . newsletterglue_default_connection() . '" class="wp-block-newsletterglue-form', $content );

	return $content;

}

/**
 * Add cutom css.
 */
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_form_css' );
function newsletterglue_add_form_css() { ?>

<?php
}

/**
 * Subscribe a user via a form.
 */
function newsletterglue_block_form_subscribe() {

	$result = array();

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	// Get app.
	$app 	= isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';
	$email 	= isset( $_POST[ 'ngl_email' ] ) ? sanitize_email( $_POST[ 'ngl_email' ] ) : '';
	$name 	= isset( $_POST[ 'ngl_name' ] ) ? sanitize_email( $_POST[ 'ngl_name' ] ) : '';

	// App Instance.
	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();

	$userdata = array(
		'name'	=> $name,
		'email'	=> $email,
	);

	if ( method_exists( $api, 'add_user' ) ) {
		$result = $api->add_user( $userdata );
		if ( $result ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

}
add_action( 'wp_ajax_newsletterglue_block_form_subscribe', 'newsletterglue_block_form_subscribe' );
add_action( 'wp_ajax_nopriv_newsletterglue_block_form_subscribe', 'newsletterglue_block_form_subscribe' );
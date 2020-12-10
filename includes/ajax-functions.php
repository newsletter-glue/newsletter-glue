<?php
/**
 * AJAX Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get AJAX URL
*/
function newsletterglue_get_ajax_url() {
	$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

	$current_url = newsletterglue_get_current_page_url();
	$ajax_url    = admin_url( 'admin-ajax.php', $scheme );

	if ( preg_match( '/^https/', $current_url ) && ! preg_match( '/^https/', $ajax_url ) ) {
		$ajax_url = preg_replace( '/^http/', 'https', $ajax_url );
	}

	return apply_filters( 'newsletterglue_get_ajax_url', $ajax_url );
}

/**
 * Save a block.
 */
function newsletterglue_ajax_save_block() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$block_id 	= isset( $_POST[ 'id' ] ) ? sanitize_text_field( $_POST[ 'id' ] ) : '';
	$block		= str_replace( 'newsletterglue_block_', '', $block_id );
	$classname 	= 'NGL_Block_' . ucfirst( $block );

	if ( class_exists( $classname ) ) {
		$instance = new $classname;
		wp_send_json( call_user_func( array( $instance, 'save_settings' ) ) );
	}

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_save_block', 'newsletterglue_ajax_save_block' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_block', 'newsletterglue_ajax_save_block' );

/**
 * Use all blocks.
 */
function newsletterglue_ajax_use_all_blocks() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	$blocks 	= newsletterglue_get_blocks();

	foreach( $blocks as $block_id => $params ) {
		$use_blocks[ $block_id ] = 'yes';
	}

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_use_all_blocks', 'newsletterglue_ajax_use_all_blocks' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_use_all_blocks', 'newsletterglue_ajax_use_all_blocks' );

/**
 * Disable all blocks.
 */
function newsletterglue_ajax_disable_all_blocks() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	$blocks 	= newsletterglue_get_blocks();

	foreach( $blocks as $block_id => $params ) {
		$use_blocks[ $block_id ] = 'no';
	}

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_disable_all_blocks', 'newsletterglue_ajax_disable_all_blocks' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_disable_all_blocks', 'newsletterglue_ajax_disable_all_blocks' );

/**
 * Switch block use.
 */
function newsletterglue_ajax_use_block() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	
	if ( ! $use_blocks ) {
		$use_blocks = array();
	}

	$id = sanitize_text_field( $_POST[ 'id' ] );
	$value = sanitize_text_field( $_POST[ 'value' ] );

	$use_blocks[ $id ] = $value;

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_use_block', 'newsletterglue_ajax_use_block' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_use_block', 'newsletterglue_ajax_use_block' );

/**
 * API connection.
 */
function newsletterglue_ajax_connect_api() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	// Get app.
	$app = isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->add_integration();

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_connect_api', 'newsletterglue_ajax_connect_api' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_connect_api', 'newsletterglue_ajax_connect_api' );

/**
 * Remove API Integration.
 */
function newsletterglue_ajax_remove_api() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	// Get app.
	$app = isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->remove_integration();

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_remove_api', 'newsletterglue_ajax_remove_api' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_api', 'newsletterglue_ajax_remove_api' );

/**
 * Reset a sent newsletter.
 */
function newsletterglue_ajax_reset_newsletter() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	if ( $post_id ) {
		newsletterglue_reset_newsletter( $post_id );
	}

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_reset_newsletter', 'newsletterglue_ajax_reset_newsletter' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_reset_newsletter', 'newsletterglue_ajax_reset_newsletter' );

/**
 * Removes a notice.
 */
function newsletterglue_ajax_remove_notice() {
	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$key = isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : '';

	newsletterglue_remove_notice( $key, sanitize_text_field( $_REQUEST[ 'key' ]) );

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_remove_notice', 'newsletterglue_ajax_remove_notice' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_notice', 'newsletterglue_ajax_remove_notice' );

/**
 * Test a newsletter.
 */
function newsletterglue_ajax_test_email() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	// Save newsletter data.
	newsletterglue_save_data( $post_id, newsletterglue_sanitize( $_REQUEST ) );

	// Send it.
	$response = newsletterglue_send( $post_id, true );

	wp_send_json( $response );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_test_email', 'newsletterglue_ajax_test_email' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_test_email', 'newsletterglue_ajax_test_email' );

/**
 * Verify email address.
 */
function newsletterglue_ajax_verify_email() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$email 		= isset( $_REQUEST[ 'email' ] ) ? sanitize_email( $_REQUEST[ 'email' ] ) : '';
	$app 		= isset( $_REQUEST[ 'app' ] ) ? sanitize_text_field( $_REQUEST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->verify_email( $email );

	wp_send_json( $result );

}
add_action( 'wp_ajax_newsletterglue_ajax_verify_email', 'newsletterglue_ajax_verify_email' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_verify_email', 'newsletterglue_ajax_verify_email' );

/**
 * Save Theme Setting.
 */
function newsletterglue_ajax_save_theme_setting() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$id 	= isset( $_REQUEST[ 'id' ] ) ? sanitize_text_field( $_REQUEST[ 'id' ] ) : '';
	$value 	= isset( $_REQUEST['value'] ) ? sanitize_text_field( trim( $_REQUEST['value'] ) ) : '';

	if ( $id == 'ngl_add_title' ) {
		if ( $value ) {
			update_option( 'newsletterglue_add_title', 'yes' );
		} else {
			update_option( 'newsletterglue_add_title', 'no' );
		}
		die();
	}

	if ( strstr( $id, 'ngl_' ) ) {
		$key = str_replace( 'ngl_', '', $id );
		update_option( 'newsletterglue_' . $key, $value );
		die();
	}

	// Misc theme options.
	$theme  = get_option( 'newsletterglue_theme' );

	if ( $id ) {
		$theme[ $id ] = $value;
	}

	update_option( 'newsletterglue_theme', $theme );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_save_theme_setting', 'newsletterglue_ajax_save_theme_setting' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_theme_setting', 'newsletterglue_ajax_save_theme_setting' );

/**
 * Reset Theme.
 */
function newsletterglue_ajax_reset_theme() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	delete_option( 'newsletterglue_theme' );
	delete_option( 'newsletterglue_logo' );
	delete_option( 'newsletterglue_logo_id' );
	delete_option( 'newsletterglue_credits' );
	delete_option( 'newsletterglue_position_logo' );
	delete_option( 'newsletterglue_position_featured' );
	delete_option( 'newsletterglue_add_title' );
	update_option( 'newsletterglue_add_featured', 1 );

	?>
	<div class="ngl-theme">

		<div class="ngl-theme-reset">
			<div class="ngl-theme-reset-status">
				<span class="ngl-process is-hidden is-waiting">
					<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
					<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
				</span>
				<span class="ngl-process is-hidden is-valid">
					<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
					<span class="ngl-process-text"><strong><?php _e( 'Saved', 'newsletter-glue' ); ?></strong></span>
				</span>
			</div>
			<div class="ngl-theme-reset-link"><?php _e( 'Reset to default style', 'newsletter-glue' ); ?></div>
			<div class="ngl-theme-reset-confirm is-hidden"><?php _e( 'Confirm reset (you can&rsquo;t undo after this)', 'newsletter-glue' ); ?></div>
			<div class="ngl-theme-reset-btns is-hidden"><a href="#" class="ngl-theme-reset-do"><?php _e( 'Reset', 'newsletter-glue' ); ?></a><span>|</span><a href="#" class="ngl-theme-reset-back"><?php _e( 'Go back', 'newsletter-glue' ); ?></a></div>
		</div>

		<div class="ngl-theme-preview">

			<?php include_once( NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-theme-preview.php' ); ?>

		</div>

		<div class="ngl-theme-panel">

			<?php include_once( NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-theme-panel.php' ); ?>

		</div>

	</div>
	<?php

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_reset_theme', 'newsletterglue_ajax_reset_theme' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_reset_theme', 'newsletterglue_ajax_reset_theme' );

/**
 * Save Field.
 */
function newsletterglue_ajax_save_field() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$id 	= isset( $_REQUEST[ 'id' ] ) ? str_replace( 'ngl_', '', sanitize_text_field( $_REQUEST[ 'id' ] ) ) : '';

	if ( $id == 'custom_css' ) {
		$value = isset( $_REQUEST['value'] ) ? nl2br( $_REQUEST['value'] ) : '';
	} else {
		$value 	= isset( $_REQUEST['value'] ) ? sanitize_text_field( trim( $_REQUEST['value'] ) ) : '';
	}

	$value  = urldecode( $value );

	$app 	= newsletterglue_default_connection();

	$options = get_option( 'newsletterglue_options' );
	$result  = array();

	$single_options = apply_filters( 'newsletterglue_single_options', array( 'header_image', 'header_image_pos', 'credits', 'post_types', 'disable_plugin_css' ) );

	if ( in_array( $id, $single_options ) ) {

		update_option( 'newsletterglue_' . $id, $value );

	} else if ( $id == 'custom_css' ) {

		update_option( 'newsletterglue_css', $value );

	} else if ( $id == 'from_email' ) {

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $app );
		$api		= new $classname();
		$result 	= $api->verify_email( $value );

		if ( isset( $result['success'] ) || $result === true ) {
			$options[ $app ][ $id ] = $value;
		}

	} else {

		if ( trim( $value ) ) {

			$options[ $app ][ $id ] = $value;

		} else {

			$result[ 'failed' ] = __( 'This cannot be empty', 'newsletter-glue' );

		}

	}

	update_option( 'newsletterglue_options', $options );

	// We did an action here.
	if ( ! get_option( 'newsletterglue_did_action' ) ) {
		update_option( 'newsletterglue_did_action', 'yes' );
	}

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_save_field', 'newsletterglue_ajax_save_field' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_field', 'newsletterglue_ajax_save_field' );

/**
 * Save image selection.
 */
function newsletterglue_ajax_save_image() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$option_id 		= isset( $_REQUEST[ 'elem_id' ] ) ? sanitize_text_field( $_REQUEST[ 'elem_id' ] ) : '';
	$attachment_id 	= isset( $_REQUEST[ 'id' ] ) ? absint( $_REQUEST[ 'id' ] ) : '';

	if ( $attachment_id ) {

		$url = wp_get_attachment_url( $attachment_id );

		// No URL.
		if ( ! $url ) {

			delete_option( $option_id );
			delete_option( $option_id . '_id' );

			wp_send_json_error();

		}

		$data = array(
			'id'		=> $attachment_id,
			'url'		=> $url,
			'filename'	=> basename( $url ),
			'html'		=> '<a href="#" target="_blank" class="ngl-image-trigger">' . basename( $url ) . '</a><a href="' . esc_url( $url ) . '" target="_blank" class="ngl-image-icon"><i class="external alternate icon"></i></a><a href="#" class="ngl-image-remove">' . __( 'remove', 'newsletter-glue' ) . '</a>',
		);

		update_option( $option_id, $url );
		update_option( $option_id . '_id' , $attachment_id );

		wp_send_json_success( $data );

	}

	delete_option( $option_id );
	delete_option( $option_id . '_id' );

	wp_send_json_error();

}
add_action( 'wp_ajax_newsletterglue_ajax_save_image', 'newsletterglue_ajax_save_image' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_image', 'newsletterglue_ajax_save_image' );

/**
 * Get onboarding settings.
 */
function newsletterglue_ajax_get_onboarding_settings() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$app 	= isset( $_REQUEST['api'] ) ? sanitize_text_field( $_REQUEST['api'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	if ( method_exists( $api, 'connect' ) ) {
		$api->connect();
	}

	include_once newsletterglue_get_path( $app ) . '/onboarding.php';

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_get_onboarding_settings', 'newsletterglue_ajax_get_onboarding_settings' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_onboarding_settings', 'newsletterglue_ajax_get_onboarding_settings' );

/**
 * When user clicks the review button.
 */
function newsletterglue_clicked_review_button() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	update_option( 'newsletterglue_review_button_expired', 'yes' );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_clicked_review_button', 'newsletterglue_clicked_review_button' );
add_action( 'wp_ajax_nopriv_newsletterglue_clicked_review_button', 'newsletterglue_clicked_review_button' );

/**
 * Get tags.
 */
function newsletterglue_ajax_get_tags() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$app 		= isset( $_REQUEST['app'] ) ? sanitize_text_field( $_REQUEST['app'] ) : '';
	$audience 	= isset( $_REQUEST['audience'] ) ? sanitize_text_field( $_REQUEST['audience'] ) : '';

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	if ( method_exists( $api, 'connect' ) ) {
		$api->connect();
	}

	$api->get_segments_html( $audience );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_ajax_get_tags', 'newsletterglue_ajax_get_tags' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_tags', 'newsletterglue_ajax_get_tags' );

/**
 * Send deactivation feedback.
 */
function newsletterglue_deactivate() {

	check_ajax_referer( 'newsletterglue-deactivate-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( 'No cheating, huh!' );
	}

	$email         = get_option( 'admin_email' );
	$_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
	$reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
	$feedback	   = sanitize_text_field( wp_unslash( $_POST['feedback'] ) );
	$reason        = '';

	if ( $_reason == '1' ) {
		$reason = 'I only needed the plugin for a short period';
	} elseif ( $_reason == '2' ) {
		$reason = 'I found a better plugin';
	} elseif ( $_reason == '3' ) {
		$reason = 'The plugin broke my site';
	} elseif ( $_reason == '4' ) {
		$reason = 'The plugin suddenly stopped working';
	} elseif ( $_reason == '5' ) {
		$reason = 'I no longer need the plugin';
	} elseif ( $_reason == '6' ) {
		$reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
	} elseif ( $_reason == '7' ) {
		$reason = 'Other';
	}

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Deactivate',
        'reason'  			=> $reason,
        'reason_detail'		=> $reason_detail,
		'feedback'			=> $feedback,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_deactivate', 'newsletterglue_deactivate' );
add_action( 'wp_ajax_nopriv_newsletterglue_deactivate', 'newsletterglue_deactivate' );

/**
 * Send connection feedback.
 */
function newsletterglue_send_feedback() {

	check_ajax_referer( 'newsletterglue-feedback-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( 'No cheating, huh!' );
	}

	$software		= sanitize_text_field( wp_unslash( $_POST['_software'] ) );
	$details       	= sanitize_text_field( wp_unslash( $_POST['_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Feedback',
        'name'  			=> $name,
        'details'			=> $details,
		'software'			=> $software,
		'plugin_name' 		=> 'Newsletter Glue',
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_send_feedback', 'newsletterglue_send_feedback' );
add_action( 'wp_ajax_nopriv_newsletterglue_send_feedback', 'newsletterglue_send_feedback' );

/**
 * Send a bug report.
 */
function newsletterglue_bug_report() {

	check_ajax_referer( 'newsletterglue-bug-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( __( 'No cheating, huh!', 'newsletter-glue' ) );
	}

	$details       	= sanitize_text_field( wp_unslash( $_POST['_bug_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_bug_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_bug_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Bug',
        'name'  			=> $name,
        'details'			=> $details,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_bug_report', 'newsletterglue_bug_report' );
add_action( 'wp_ajax_nopriv_newsletterglue_bug_report', 'newsletterglue_bug_report' );

/**
 * Send a feature request.
 */
function newsletterglue_feature_request() {

	check_ajax_referer( 'newsletterglue-feature-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( __( 'No cheating, huh!', 'newsletter-glue' ) );
	}

	$details       	= sanitize_text_field( wp_unslash( $_POST['_feature_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_feature_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_feature_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Feature',
        'name'  			=> $name,
        'details'			=> $details,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_feature_request', 'newsletterglue_feature_request' );
add_action( 'wp_ajax_nopriv_newsletterglue_feature_request', 'newsletterglue_feature_request' );
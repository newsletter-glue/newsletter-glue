<?php
/**
 * Metaboxes.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add meta box support.
 */
function newsletterglue_add_meta_box() {

	add_meta_box( 'newsletter_glue_metabox', __( 'Newsletter Glue: Send as newsletter', 'newsletter-glue' ), 'newsletterglue_meta_box', 'post' );

}
add_action( 'add_meta_boxes', 'newsletterglue_add_meta_box' );

/**
 * Save meta box.
 */
function newsletterglue_save_meta_box( $post_id, $post ) {

	// $post_id and $post are required
	$saved_meta_boxes = false;

	if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
		return;
	}

	// Dont' save meta boxes for revisions or autosaves
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
		return;
	}

	// Check the nonce
	if ( empty( $_POST['newsletterglue_meta_nonce'] ) || ! wp_verify_nonce( $_POST['newsletterglue_meta_nonce'], 'newsletterglue_save_data' ) ) {
		return;
	}

	// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
	if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
		return;
	}

	// Check user has permission to edit
	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		return;
	}

	// We need this save event to run once to avoid potential endless loops. This would have been perfect:
	$saved_meta_boxes = true;

	$old_settings = newsletterglue_get_data( $post_id );

	// Sent already? do not do anything.
	if ( isset( $old_settings->sent ) ) {
		return;
	}

	// The "Send" checkbox is not checked.
	if ( ! isset( $_POST[ 'ngl_send_newsletter' ] ) ) {
		return;
	}

	// Save newsletter data.
	newsletterglue_save_data( $post_id, $_POST );

	// Send it.
	newsletterglue_send( $post_id );

}
add_action( 'save_post', 'newsletterglue_save_meta_box', 1, 2 );

/**
 * Shows the metabox content.
 */
function newsletterglue_meta_box() {
	global $post;

	if ( ! $connection = newsletterglue_default_connection() ) {

		include( 'views/metabox-connect.php' );

	} else {

		include_once NGL_PLUGIN_DIR . 'includes/integrations/' . $connection . '/init.php';

		$class 		= 'NGL_' . ucfirst( $connection );
		$api   		= new $class();
		$defaults 	= newsletterglue_get_form_defaults( $post, $api );
		$settings   = newsletterglue_get_data( $post->ID );

		include( 'views/metabox-status.php' );
		include( 'views/metabox-reset.php' );
		include( 'views/metabox-' . $connection . '.php' );

		wp_nonce_field( 'newsletterglue_save_data', 'newsletterglue_meta_nonce' );

	}

}

/**
 * Get form defaults.
 */
function newsletterglue_get_form_defaults( $post = 0, $api = '' ) {
	$d = new stdclass;

	// Subject.
	if ( $post->post_status === 'auto-draft' ) {
		$subject = '';
	} else {
		$subject = get_the_title( $post->ID );
	}

	$app = newsletterglue_default_connection();

	$d->from_name	= newsletterglue_get_option( 'from_name', $app );
	$d->from_email 	= newsletterglue_get_option( 'from_email', $app );
	$d->test_email	= newsletterglue_get_option( 'from_email', $app );
	$d->subject     = $subject;

	// Get options from API.
	$api_options = $api->get_form_defaults();

	foreach( $api_options as $key => $value ) {
		$d->{$key} = $value;
	}

	return $d;
}

/**
 * Save newsletter options as meta data.
 */
function newsletterglue_save_data( $post_id, $data ) {
	$meta = array();

	foreach( $data as $key => $value ) {
		if ( strstr( $key, 'ngl_' ) ) {
			$key = str_replace( 'ngl_', '', $key );
			$meta[ $key ] = $value;

			if ( $key === 'when' ) {
				$timestamp = strtotime( $value );
				$meta[ 'timestamp' ] = $timestamp;
			}
		}
	}

	if ( isset( $meta ) && ! empty( $meta ) ) {
		update_post_meta( $post_id, '_newsletterglue', $meta );
	}
}

/**
 * Get newsletter options as meta data.
 */
function newsletterglue_get_data( $post_id ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	$s = new stdclass;

	if ( is_array( $data ) ) {
		foreach( $data as $key => $value ) {
			$s->{$key} = $value;
		}
	}

	return $s;

}

/**
 * Send the newsletter and mark as sent.
 */
function newsletterglue_send( $post_id = 0, $test = false ) {

	$post = get_post( $post_id );
	$data = get_post_meta( $post_id, '_newsletterglue', true );

	if ( ! $test ) {
		$data[ 'sent' ] = true;
	}

	update_post_meta( $post_id, '_newsletterglue', $data );

	$provider = $data[ 'provider' ];

	include_once NGL_PLUGIN_DIR . 'includes/integrations/' . $provider . '/init.php';

	$classname = 'NGL_' . ucfirst( $provider );

	$api = new $classname();

	// Send the newsletter.
	$response = $api->send_newsletter( $post_id, $data, $test );

	return $response;
}

/**
 * Mark a newsletter as unsent.
 */
function newsletterglue_reset_newsletter( $post_id = 0 ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	// Allow campaign to be resent.
	if ( isset( $data[ 'sent' ] ) ) {
		unset( $data[ 'sent' ] );
	}

	// Cancel draft status.
	$data[ 'schedule' ] = 'immediately';

	update_post_meta( $post_id, '_newsletterglue', $data );
}
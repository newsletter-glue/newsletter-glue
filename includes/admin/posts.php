<?php
/**
 * Admin Posts.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add newsletter status column.
 */
function newsletterglue_manage_posts_columns( $columns ) {

	foreach( $columns as $key => $value ) {
		$ngl_columns[ $key ] = $value;
		if ( $key == 'title' ) {
			$ngl_columns[ 'ngl_status' ] = __( 'Newsletter status', 'newsletter-glue' );
		}
	}

	return $ngl_columns;

}
add_filter( 'manage_post_posts_columns', 'newsletterglue_manage_posts_columns', 99 );

/**
 * Display custom post columns.
 */
function newsletterglue_manage_posts_custom_column( $column, $post_id ) {

	switch ( $column ) {
		case 'ngl_status' :

			$text = '';
			$results = newsletterglue_get_past_campaigns( $post_id );

			if ( $results ) {
				if ( count( $results ) == 1 ) {
					foreach( $results as $time => $data ) {
						if ( $data['type'] == 'error' ) {
							$text .= '<span class="ngl-state ngl-error">' . $data[ 'message' ] . '</span>';
						}
						if ( $data['type'] == 'success' ) {
							$text .= '<span class="ngl-state ngl-success">' . $data[ 'message' ] . '</span>';
						}
						if ( $data['type'] == 'neutral' ) {
							$text .= '<span class="ngl-state ngl-neutral">' . $data[ 'message' ] . '</span>';
						}
						$text .= '<span><a href="#" class="ngl-modal-log">' . __( 'View log', 'newsletter-glue' ) . '</a>';
						if ( isset( $data['help'] ) ) {
							$text .= ' | <span class="ngl-error"><a href="' . esc_url( $data[ 'help' ] ) . '">' . __( 'Get help', 'newsletter-glue' ) . '</a></span>';
						}
						$text .= '</span>';
					}
				} else {
					$text .= '<span class="ngl-state">' . __( 'Multiple newsletters', 'newsletter-glue' ) . '</span>';
					$text .= '<span><a href="#" class="ngl-modal-log">' . __( 'View log', 'newsletter-glue' ) . '</a></span>';
				}

				// Add modal content.
				echo newsletterglue_generate_status_table( $post_id, $results );
			}

			if ( ! empty( $text ) ) {
				echo $text;
			}

		break;
	}

}
add_action( 'manage_post_posts_custom_column', 'newsletterglue_manage_posts_custom_column', 99, 2 );

/**
 * Generate newsletter status table.
 */
function newsletterglue_generate_status_table( $post_id = 0, $results = array() ) {

	$post = get_post( $post_id );

	include NGL_PLUGIN_DIR . 'includes/admin/views/posts-status.php';

}

/**
 * Get past campaigns based on post ID.
 */
function newsletterglue_get_past_campaigns( $post_id ) {

	$results = get_post_meta( $post_id, '_ngl_results', true );

	if ( $results ) {
		foreach( $results as $key => $value ) {
			if ( $key == 0 ) {
				unset( $results[ $key ] );
			}
		}
	}

	// Sort by latest time first.
	if ( is_array( $results ) ) {
		krsort( $results );
	}

	return $results;
}

/**
 * Add modal to display newsletter status.
 */
function newsletterglue_status_log_modal() {
	global $pagenow;

	if ( $pagenow !== 'edit.php' ) {
		return;
	}

	include_once NGL_PLUGIN_DIR . 'includes/admin/views/posts-modal.php';

}
add_action( 'admin_footer', 'newsletterglue_status_log_modal' );
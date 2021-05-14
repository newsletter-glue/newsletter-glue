<?php
/**
 * Admin Posts.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add newsletter category column.
 */
function newsletterglue_add_category_column( $columns ) {

	foreach( $columns as $key => $value ) {
		$ngl_columns[ $key ] = $value;
		if ( $key == 'title' ) {
			$ngl_columns[ 'ngl_category' ] = __( 'Newsletter category', 'newsletter-glue' );
		}
	}

	return $ngl_columns;

}
add_filter( "manage_newsletterglue_posts_columns", 'newsletterglue_add_category_column', 100 );

/**
 * Display custom post columns.
 */
function newsletterglue_display_category_column( $column, $post_id ) {

	switch ( $column ) {
		case 'ngl_category' :

			$terms = wp_get_post_terms( $post_id, 'ngl_newsletter_cat' );
			if ( ! empty( $terms ) ) {
				$output = '';
				foreach( $terms as $term ) {
					$output .= '<a href="' . admin_url( 'edit.php?post_type=newsletterglue&ngl_newsletter_cat=newsletters' ) . '">' . $term->name . '</a> (<a href="' . admin_url( 'term.php?taxonomy=ngl_newsletter_cat&tag_ID=' . $term->term_id . '&post_type=post' ) . '">' . __( 'Edit', 'newsletter-glue' ) . '</a>)<span style="display:inline-block;width:20px;"></span>';
				}
				echo $output;
			} else {
				echo '&mdash;';
			}

		break;
	}

}
add_action( "manage_newsletterglue_posts_custom_column", 'newsletterglue_display_category_column', 99, 2 );

/**
 * Adds support to display newsletter status in posts list.
 */
add_action( 'init', 'newsletterglue_add_newsletter_status' );
function newsletterglue_add_newsletter_status() {

	$post_types = get_option( 'newsletterglue_post_types' );

	if ( ! empty( $post_types ) ) {
		$post_types = explode( ',', $post_types );
	} else {
		$post_types = apply_filters( 'newsletterglue_supported_core_types', array( 'post', 'page' ) );
	}

	$post_types = array_merge( $post_types, array( 'newsletterglue' ) );

	if ( is_array( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", 'newsletterglue_manage_posts_columns', 99 );
			add_action( "manage_{$post_type}_posts_custom_column", 'newsletterglue_manage_posts_custom_column', 99, 2 );
		}
	}

}

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
						if ( ! isset( $data['type'] ) ) {
							continue;
						}
						if ( $data['type'] == 'error' ) {
							$text .= '<span class="ngl-state ngl-error">' . $data[ 'message' ] . '</span>';
						}
						if ( $data['type'] == 'success' ) {
							$text .= '<span class="ngl-state ngl-success">' . $data[ 'message' ] . '</span>';
						}
						if ( $data['type'] == 'neutral' ) {
							$text .= '<span class="ngl-state ngl-neutral">' . $data[ 'message' ] . '</span>';
						}
						if ( $data['type'] == 'schedule' ) {
							$text .= '<span class="ngl-state ngl-schedule">' . $data[ 'message' ] . '</span>';
						}
						$text .= '<span><a href="#ngl-status-log" data-post-id="'. absint( $post_id ) . '" class="ngl-modal-log">' . __( 'View log', 'newsletter-glue' ) . '</a>';
						if ( isset( $data['help'] ) && ! empty( $data['help'] ) ) {
							$text .= ' | <span class="ngl-error"><a href="' . esc_url( $data[ 'help' ] ) . '">' . __( 'Get help', 'newsletter-glue' ) . '</a></span>';
						}
						$text .= '</span>';
					}
				} else {
					$text .= '<span class="ngl-state">' . __( 'Multiple newsletters', 'newsletter-glue' ) . '</span>';
					$text .= '<span><a href="#ngl-status-log" data-post-id="'. absint( $post_id ) . '" class="ngl-modal-log">' . __( 'View log', 'newsletter-glue' ) . '</a></span>';
				}

			}

			if ( ! empty( $text ) ) {
				echo $text;
			}

		break;
	}

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

	if ( $pagenow !== 'edit.php' && $pagenow !== 'post.php' ) {
		return;
	}

	include_once NGL_PLUGIN_DIR . 'includes/admin/views/posts-modal.php';

}
add_action( 'admin_footer', 'newsletterglue_status_log_modal' );
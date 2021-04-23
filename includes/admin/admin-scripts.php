<?php
/**
 * Admin Scripts.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueues the required admin scripts.
 */
function newsletterglue_load_admin_scripts( $hook ) {
	global $wp_scripts;

	$screen    = get_current_screen();
	$screen_id = $screen ? $screen->id : '';

	$js_dir  = NGL_PLUGIN_URL . 'assets/js/';
	$css_dir = NGL_PLUGIN_URL . 'assets/css/';

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Register scripts.
	wp_register_script( 'newsletterglue_semantic', $js_dir . 'semantic/semantic' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_flatpickr', $js_dir . 'flatpickr/flatpickr' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_admin', $js_dir . 'admin/admin' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_cpt', $js_dir . 'admin/cpt' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_global', $js_dir . 'admin/global' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_onboarding', $js_dir . 'admin/onboarding' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'jquery-spectrum', $js_dir . 'spectrum/spectrum' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_theme', $js_dir . 'admin/theme' . $suffix . '.js', array( 'jquery', 'jquery-spectrum' ), NGL_VERSION, true );
	wp_register_script( 'newsletterglue_meta', $js_dir . 'admin/gutenberg' . $suffix . '.js', array( 'jquery' ), NGL_VERSION, true );

	// Sitewide JS.
	wp_enqueue_script( 'newsletterglue_global' );

	// Register styles.
	wp_register_style( 'newsletterglue_admin_menu_styles', $css_dir . 'menu' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_material_icons', '//fonts.googleapis.com/icon?family=Material+Icons', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_semantic', $css_dir . 'semantic' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_admin_styles', $css_dir . 'admin' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_onboarding_styles', $css_dir . 'onboarding' . $suffix . '.css', array(), NGL_VERSION );
	wp_register_style( 'newsletterglue_cpt', $css_dir . 'cpt' . $suffix . '.css', array(), NGL_VERSION );

	// Sitewide menu CSS.
	wp_enqueue_style( 'newsletterglue_admin_menu_styles' );

	// Admin assets for plugin pages only.
	if ( in_array( $screen_id, newsletterglue_get_screen_ids() ) ) {
		wp_enqueue_script( 'newsletterglue_semantic' );
		wp_enqueue_script( 'newsletterglue_flatpickr' );
		wp_enqueue_script( 'newsletterglue_admin' );
		wp_enqueue_script( 'newsletterglue_meta' );

		wp_localize_script( 'newsletterglue_admin', 'newsletterglue_params', apply_filters( 'newsletterglue_admin_js_params', array(
			'ajaxurl'    		=> newsletterglue_get_ajax_url(),
			'ajaxnonce'			=> wp_create_nonce( 'newsletterglue-ajax-nonce' ),
			'publish_error'		=> __( 'Your newsletter is missing important details. <a href="#">Let&rsquo;s fix that.</a>', 'newsletter-glue' ),
			'saving'			=> '<i class="sync alternate icon"></i>' . __( 'Saving...', 'newsletter-glue' ),
			'saved'				=> '<i class="check circle icon"></i>' . __( 'Saved', 'newsletter-glue' ),
			'save'				=> __( 'Save', 'newsletter-glue' ),
			'image_size'		=> __( 'Ideal image width: 1200px', 'newsletter-glue' ),
			'no_featured_image'	=> __( 'No featured image was selected.', 'newsletter-glue' ),
			'select_image'		=> __( 'Select Image', 'newsletter-glue' ),
			'no_image_set'		=> __( 'No image selected', 'newsletter-glue' ),
			'send_newsletter'	=> sprintf( __( '<strong>Send as newsletter</strong> (%s)', 'newsletter-glue' ), '<a href="#newsletter_glue_metabox">' . __( 'check settings', 'newsletter-glue' ) . '</a>' ),
			'no_posts_found'	=> __( 'There&rsquo;s nothing here yet. Add your first post above.', 'newsletter-glue' ),
			'write_labels'		=> __( 'Add label', 'newsletter-glue' ),
			'refreshing_html'	=> __( 'Refreshing...', 'newsletter-glue' ),
			'refreshed_html'	=> __( 'Refreshed!', 'newsletter-glue' ),
			'unknown_error'		=> __( 'Unknown error occured.', 'newsletter-glue' ),
			'loader'			=> '<span class="ngl-state-loader"><img src="' . NGL_PLUGIN_URL . 'assets/images/loading.gif" /><i>' . __( 'Working on your newsletter...', 'newsletter-glue' ) . '</span>',
		) ) );

		wp_enqueue_style( 'newsletterglue_material_icons' );
		wp_enqueue_style( 'newsletterglue_semantic' );
		wp_enqueue_style( 'newsletterglue_admin_styles' );
	}

	// Add onboarding assets.
	if ( newsletterglue_is_onboarding_page() ) {
		wp_enqueue_script( 'newsletterglue_onboarding' );
		wp_enqueue_style( 'newsletterglue_onboarding_styles' );
	}

	// Add media scripts to settings page.
	if ( 'newsletters_page_ngl-settings' === $screen_id ) {
		wp_enqueue_media();
		wp_enqueue_script( 'newsletterglue_theme' );
		wp_enqueue_style( 'wp-edit-blocks' );
	}

	// Add CPT stuff.
	if ( in_array( $screen_id, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
		wp_enqueue_script( 'newsletterglue_cpt' );
		wp_enqueue_style( 'newsletterglue_cpt' );
	}

}
add_action( 'admin_enqueue_scripts', 'newsletterglue_load_admin_scripts', 100 );

/**
 * Add custom meta as JS.
 */
function newsletterglue_js_data() {

	global $post;

	if ( isset( $post->ID ) ) {

		$app = newsletterglue_default_connection();

		$data = array(
			'post_id'		=> $post->ID,
			'post_perma'	=> get_permalink( $post->ID ),
			'profile_pic'	=> get_avatar_url( $post->post_author, 80 ),
			'author_name'	=> get_the_author_meta( 'display_name', $post->post_author ),
			'author_bio'	=> get_the_author_meta( 'description', $post->post_author ),
			'post_date'		=> date( 'l, j M Y', strtotime( $post->post_date ) ),
			'app'			=> $app,
			'app_name'		=> newsletterglue_get_name( $app ) ? sprintf( __( '%s integration', 'newsletter-glue' ), newsletterglue_get_name( $app ) ) : __( 'Email integration', 'newsletter-glue' ),
			'readtime'		=> newsletterglue_content_estimated_reading_time( $post->post_content ),
		);

		// Add lists.
		$the_lists = newsletterglue()::$the_lists;
		if ( ! empty( $the_lists ) ) {
			$lists = array();
			if ( $app == 'mailerlite' ) {
				$lists[] = array( 'label' => __( '― No group', 'newsletter-glue' ), 'value' => '' );
			}
			if ( $app == 'sendinblue' ) {
				$lists[] = array( 'label' => __( '― No list', 'newsletter-glue' ), 'value' => '' );
			}
			foreach( $the_lists as $key => $value ) {
				$lists[] = array( 'value' => $key, 'label' => $value );
			}
			$data[ 'the_lists' ] = $lists;

			$extra_lists[] = array( 'value' => '', 'label' => '' );
			foreach( $the_lists as $key => $value ) {
				$extra_lists[] = array( 'value' => $key, 'label' => $value );
			}
			$data[ 'extra_lists' ] = $extra_lists;
		}

		// Post dates.
		$dates = array(
			date( 'l, j M Y', strtotime( $post->post_date ) ),
			date( 'F j, Y', strtotime( $post->post_date ) ),
			date( 'd M Y', strtotime( $post->post_date ) ),
			date( 'Y-m-d', strtotime( $post->post_date ) ),
			date( 'm/d/Y', strtotime( $post->post_date ) ),
			date( 'd/m/Y', strtotime( $post->post_date ) ),
		);

		$date_formats = array();
		foreach( $dates as $date ) {
			$date_formats[] = array( 'value' => $date, 'label' => $date );
		}
		$data[ 'date_formats' ] = $date_formats;

		wp_localize_script( 'newsletterglue_meta', 'newsletterglue_meta', $data );

	}

}
add_action( 'admin_footer', 'newsletterglue_js_data' );
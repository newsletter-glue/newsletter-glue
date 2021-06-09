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
	if ( in_array( $screen_id, newsletterglue_get_screen_ids() ) || strstr( $screen_id, 'ngl-' ) ) {
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
	if ( strstr( $screen_id, 'ngl-settings' ) ) {
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
			'locale'		=> str_replace( '_', '-', get_locale() ),
		);

		// Add lists.
		$the_lists = newsletterglue()::$the_lists;
		if ( $app && empty( $the_lists ) ) {

			include_once newsletterglue_get_path( $app ) . '/init.php';
			$classname 	= 'NGL_' . ucfirst( $app );
			$api		= new $classname();

			newsletterglue()::$the_lists = $api->_get_lists_compat();

			$the_lists = newsletterglue()::$the_lists;
		}
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

	// Newsletter and patterns.
	if ( isset( $post->post_type ) && in_array( $post->post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
		$app = newsletterglue_default_connection();
		if ( ! $app ) {
			return;
		}
		include_once newsletterglue_get_path( $app ) . '/functions.php';
		$function = 'newsletterglue_get_' . strtolower( $app ) . '_tags';
		if ( ! function_exists( $function ) ) {
			return;
		}
		?>
		<div class="ngl-gutenberg-pop">
			<?php
				$tags = call_user_func( $function );
				foreach( ( array ) $tags as $group_id => $group ) {
					echo '<div class="components-menu-group"><div role="group">';
					echo '<button type="button" role="menuitem" class="components-button components-menu-item__button ngl-submenu-trigger">
								<span class="components-menu-item__item"><strong>' . esc_html( $group[ 'title' ] ) . '</strong></span>
								<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="components-menu-items__item-icon" role="img" aria-hidden="true" focusable="false">
									<path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
								</svg>
							</button>';
					foreach( $group[ 'tags' ] as $id => $tag ) {
						$default_link_text 	= ! empty( $tag[ 'default_link_text' ] ) ? $tag[ 'default_link_text' ] : '';
						$fb_text			= isset( $tag[ 'require_fallback' ] ) ? __( 'Fallback required', 'newsletter-glue' ) . '<span style="color:#cc3000;">*</span>' : __( 'Fallback', 'newsletter-glue' );
						$fallback_label     = ! empty( $default_link_text ) ? __( 'Link text', 'newsletter-glue' ) : $fb_text;
						$show_helper		= ! empty( $default_link_text ) ? '' : sprintf( __( 'Show this if %s doesn&rsquo;t exist.', 'newsletter-glue' ), esc_html( strtolower( $tag[ 'title' ] ) ) );
						$helper				= isset( $tag[ 'helper' ] ) ? $tag[ 'helper' ] : $show_helper;
						echo '<button type="button" role="menuitem" class="components-button components-menu-item__button ngl-submenu-item" data-default-link-text="' . esc_attr( $default_link_text ) . '" data-tag-id="' . esc_attr( $id ) . '" data-ngl-tag="{{ ' . esc_attr( $id ) . ' }}" data-require-fb="' . isset( $tag[ 'require_fallback' ] ) . '">
								<span class="components-menu-item__item">' . esc_html( $tag[ 'title' ] ) . '</span>';
						
						if ( ! isset( $tag[ 'uneditable' ] ) ) {
							echo '<span class="ngl-gutenberg-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 27.004 27.004" class="components-menu-items__item-icon" role="img" aria-hidden="true" focusable="false"><path d="M4.5,25.875V31.5h5.625l16.59-16.59L21.09,9.285ZM31.065,10.56a1.494,1.494,0,0,0,0-2.115l-3.51-3.51a1.494,1.494,0,0,0-2.115,0L22.695,7.68l5.625,5.625,2.745-2.745Z" transform="translate(-4.5 -4.496)"/></svg>
								</span>';
						}
						echo '</button>';
						if ( isset( $tag[ 'uneditable' ] ) && $tag[ 'helper' ] ) {
							echo '<div class="ngl-outside-helper" >' . $tag[ 'helper' ] . '</div>';
						}
						if ( ! isset( $tag[ 'uneditable' ] ) ) {
							echo '<div class="ngl-fallback" data-tag="' . esc_attr( $id ) . '">
									<div class="ngl-fallback-title"><label for="__fallback_' . esc_attr( $id ) . '">' . $fallback_label . '</label></div>
									<div class="ngl-fallback-input"><input type="text" value="' . newsletterglue_get_merge_tag_fallback( $id ) . '" id="__fallback_' . esc_attr( $id ) . '" data-tag-input-id="' . esc_attr( $id ) . '" placeholder="' . $default_link_text . '" /></div>
									<div class="ngl-fallback-helper">' . $helper . '</div>
								</div>
							';
						}
					}
					echo '</div></div>';
				}
			?>
		</div>
		<?php
	}

}
add_action( 'admin_footer', 'newsletterglue_js_data' );
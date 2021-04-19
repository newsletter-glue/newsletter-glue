<?php
/**
 * CPT.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_CPT class.
 */
class NGL_CPT {

	/**
	 * Constructor.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		add_filter( 'allowed_block_types', array( __CLASS__, 'allowed_block_types' ), 99, 2 );

		add_action( 'admin_head', array( __CLASS__, 'admin_head' ), 992 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {

		if ( ! is_blog_installed() || post_type_exists( 'newsletterglue' ) ) {
			return;
		}

		do_action( 'newsletterglue_register_post_types' );

		register_post_type(
			'newsletterglue',
			apply_filters(
				'newsletterglue_register_post_type_template',
				array(
					'labels'             => array(
						'name'                  => __( 'Newsletters', 'newsletter-glue' ),
						'singular_name'         => __( 'Newsletter', 'newsletter-glue' ),
						'menu_name'             => esc_html_x( 'All Newsletters', 'Admin menu name', 'newsletter-glue' ),
						'add_new'               => __( 'Add Newsletter', 'newsletter-glue' ),
						'add_new_item'          => __( 'Add New Newsletter', 'newsletter-glue' ),
						'edit'                  => __( 'Edit', 'newsletter-glue' ),
						'edit_item'             => __( 'Edit Newsletter', 'newsletter-glue' ),
						'new_item'              => __( 'New Newsletter', 'newsletter-glue' ),
						'view_item'             => __( 'View Newsletter', 'newsletter-glue' ),
						'search_items'          => __( 'Search Newsletters', 'newsletter-glue' ),
						'not_found'             => __( 'No Newsletters found', 'newsletter-glue' ),
						'not_found_in_trash'    => __( 'No Newsletters found in trash', 'newsletter-glue' ),
						'parent'                => __( 'Parent Newsletter', 'newsletter-glue' ),
						'filter_items_list'     => __( 'Filter Newsletters', 'newsletter-glue' ),
						'items_list_navigation' => __( 'Newsletters navigation', 'newsletter-glue' ),
						'items_list'            => __( 'Newsletters list', 'newsletter-glue' ),
					),
					'description'         => __( 'This is where you can add new Newsletters to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_newsletterglue' ) ? 'newsletter-glue' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'editor' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
					'show_in_rest'		  => true,
				)
			)
		);

		do_action( 'newsletterglue_after_register_post_type' );

	}

	/**
	 * Add scripts.
	 */
	public static function admin_enqueue_scripts() {
		global $post_type;

		// Only in our CPT.
		if ( is_admin() && ! empty( $post_type ) && $post_type == 'newsletterglue' ) {
			wp_add_inline_script(
				'wp-edit-post',
				'
				wp.data.select( "core/edit-post" ).isFeatureActive( "welcomeGuide" ) && wp.data.dispatch( "core/edit-post" ).toggleFeature( "welcomeGuide" );
				var isFullScreenMode = wp.data.select( "core/edit-post" ).isFeatureActive( "fullscreenMode" );
				if ( !isFullScreenMode ) {
					wp.data.dispatch( "core/edit-post" ).toggleFeature( "fullscreenMode" );
				}
				'
			);
		}

	}

	/**
	 * Allowed block types.
	 */
	public static function allowed_block_types( $blocks, $post ) {
		if ( ! empty( $post->post_type ) ) {
			if ( $post->post_type == 'newsletterglue' ) {
				$blocks = array(
					'newsletterglue/group',
					'newsletterglue/form',
					'newsletterglue/article',
					'newsletterglue/author',
					'newsletterglue/callout',
					'newsletterglue/metadata',
					'core/paragraph',
					'core/image',
					'core/heading',
					'core/list',
					'core/quote',
					'core/buttons',
					'core/separator',
					'core/spacer',
					'core/table',
					'core/columns',
				);
				return $blocks;
			}
		}
		return $blocks;
	}

	/**
	 * Register core post types.
	 */
	public static function admin_head() {
		global $post_type;

		if ( ! empty( $post_type ) && $post_type == 'newsletterglue' ) {

			echo '<style>';
			echo '.edit-post-visual-editor { background: ' . newsletterglue_get_theme_option( 'email_bg' ) . '; }';
			echo '.editor-styles-wrapper { background: ' . newsletterglue_get_theme_option( 'container_bg' ) . '; }';

			if ( newsletterglue_get_theme_option( 'font' ) ) {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, .editor-styles-wrapper p, .editor-styles-wrapper ol, .editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt, div.editor-styles-wrapper .wp-block h2 {
						font-family: ' . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . '!important; }';
			} else {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, .editor-styles-wrapper p, .editor-styles-wrapper ol, .editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt {
						font-family: Arial, Helvetica, sans-serif; !important; }';
			}

			echo 'div.editor-styles-wrapper, div.editor-styles-wrapper p { color: ' . newsletterglue_get_theme_option( 'p_colour' ) . '; }';
			echo 'div.editor-styles-wrapper p { font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; }';
			echo 'div.editor-styles-wrapper textarea.editor-post-title__input, div.editor-styles-wrapper h1 { font-weight: bold !important; font-size: ' . newsletterglue_get_theme_option( 'h1_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h1_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h2 { font-size: ' . newsletterglue_get_theme_option( 'h2_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h2_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h3 { font-size: ' . newsletterglue_get_theme_option( 'h3_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h3_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h4 { font-size: ' . newsletterglue_get_theme_option( 'h4_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h4_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h5 { font-size: ' . newsletterglue_get_theme_option( 'h5_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h5_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h6 { font-size: ' . newsletterglue_get_theme_option( 'h6_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h6_colour' ) . '; }';
			echo 'div.editor-styles-wrapper .wp-block-button__link { font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; text-transform: none; padding: 14px 20px; font-weight: inherit; min-width: ' . newsletterglue_get_theme_option( 'btn_width' ) . 'px; background-color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '; color: ' . newsletterglue_get_theme_option( 'btn_colour' ) . '; }';

			echo '</style>';

		}
	}

}

NGL_CPT::init();
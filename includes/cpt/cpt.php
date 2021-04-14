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
						'menu_name'             => esc_html_x( 'Newsletters', 'Admin menu name', 'newsletter-glue' ),
						'add_new'               => __( 'Add Newsletter', 'newsletter-glue' ),
						'add_new_item'          => __( 'Add new Newsletter', 'newsletter-glue' ),
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
		if ( is_admin() && ! empty( $post_type ) && $post_type == 'newsletterglue' ) {
			wp_add_inline_script(
				'wp-edit-post',
				'
				wp.data.select( "core/edit-post" ).isFeatureActive( "welcomeGuide" ) && wp.data.dispatch( "core/edit-post" ).toggleFeature( "welcomeGuide" );
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
					'core/paragraph',
				);
				return $blocks;
			}
		}
		return $blocks;
	}

}

NGL_CPT::init();
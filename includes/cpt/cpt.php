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
		add_filter( 'display_post_states', array( __CLASS__, 'display_post_states' ), 50, 2 );

		// Register post types.
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

		// Load block patterns.
		add_action( 'init', array( __CLASS__, 'load_block_patterns' ), 7 );

		// Default patterns.
		add_action( 'init', array( __CLASS__, 'create_default_patterns' ), 50 );

		// Register block category.
		add_action( 'init', array( __CLASS__, 'register_block_category' ), 999999999 );

		// Enqueue scripts in admin.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		// Allowed block types in CPT.
		add_filter( 'allowed_block_types', array( __CLASS__, 'allowed_block_types' ), 999, 2 );

		// CSS for Gutenberg.
		add_action( 'admin_head', array( __CLASS__, 'admin_head' ), 999 );

		// Removes date filter.
		add_filter( 'months_dropdown_results', array( __CLASS__, 'months_dropdown_results' ) );

		// Add category dropdown.
		add_action( 'restrict_manage_posts', array( __CLASS__, 'restrict_manage_posts' ), 100 );

		// Filter post views.
		add_filter( 'views_edit-ngl_pattern', array( __CLASS__, 'views_edit' ) );

		// When a newsletter is saved.
		add_action( 'save_post', array( __CLASS__, 'save_newsletter' ), 10, 2 );

		// When a pattern is saved.
		add_action( 'save_post', array( __CLASS__, 'save_pattern' ), 10, 2 );

		// Row actions.
		add_action( 'post_row_actions', array( __CLASS__, 'post_row_actions' ), 50, 2 );

		// Duplicate.
		add_action( 'admin_action_ngl_duplicate_as_pattern', array( __CLASS__, 'duplicate_pattern' ) );
		add_action( 'admin_action_ngl_duplicate_as_newsletter', array( __CLASS__, 'duplicate_newsletter' ) );

		// Add Gutenberg JS.
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );

		// Default titles.
		// add_filter( 'save_post', array( __CLASS__, 'add_newsletter_title' ), 99, 2 );

		// Add top bar.
		add_action( 'all_admin_notices', array( __CLASS__, 'add_topbar' ), 999 );

		// Filter for Gutenberg use.
		add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'use_block_editor_for_post_type' ), 99999, 2 );

		// Hook for web view.
		add_action( 'wp', array( __CLASS__, 'show_webview' ), 99 );
	}

	/**
	 * Mark pattern as default.
	 */
	public static function display_post_states( $post_states, $post ) {
		if ( 'ngl_pattern' === $post->post_type ) {
			if ( get_post_meta( $post->ID, '_ngl_core_pattern', true ) ) {
				$post_states[ 'ngl_default' ] = '<span class="ngl-pattern-state" style="color: #999;font-weight:normal;font-size:13px;">' . __( 'Default', 'newsletter-glue' ) . '</span>';
			}
		}
		return $post_states;
	}

	/**
	 * Create default patterns.
	 */
	public static function create_default_patterns() {

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		if ( get_option( 'newsletterglue_default_patterns' ) && ! isset( $_REQUEST[ 'recreate-patterns' ] ) ) {
			return;
		}

		require_once( NGL_PLUGIN_DIR . 'includes/cpt/default-patterns.php' );

		$patterns = new NGL_Default_Patterns();
		$patterns->create();

		update_option( 'newsletterglue_default_patterns', 'yes' );

		if ( isset( $_REQUEST[ 'recreate-patterns' ] ) ) {
			exit( wp_redirect( remove_query_arg( 'recreate-patterns' ) ) );
		}
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {

		if ( ! is_blog_installed() || post_type_exists( 'newsletterglue' ) ) {
			return;
		}

		do_action( 'newsletterglue_register_post_types' );

		// Create newsletter post type.
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
					'description'         	=> __( 'This is where you can add new Newsletters to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              	=> true,
					'show_ui'             	=> true,
					'capability_type'     	=> 'post',
					'map_meta_cap'        	=> true,
					'publicly_queryable'  	=> true,
					'exclude_from_search' 	=> false,
					'show_in_menu'        	=> false,
					'hierarchical'        	=> false,
					'rewrite'             	=> array( 'slug' => 'newsletter/%newsletter%', 'with_front' => true ),
					'query_var'           	=> true,
					'supports'           	=> array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
					'taxonomies'        	=> array( 'ngl_newsletter_cat' ),
					'show_in_nav_menus'		=> true,
					'show_in_admin_bar'   	=> true,
					'show_in_rest'		  	=> true,
				)
			)
		);

		// Create newsletter category taxonomy.
		$args = array(
			'labels' => array(
				'name' 			=> __( 'Newsletter category', 'newsletter-glue' ),
				'singular_name' => __( 'Newsletter category', 'newsletter-glue' ),
			),
			'label'        			=> __( 'Newsletter category', 'newsletter-glue' ),
			'hierarchical' 			=> true,
			'rewrite'      			=> array( 'slug' => 'newsletter' ),
			'show_in_rest' 			=> true,
			'show_admin_column'		=> false,
		);

		register_taxonomy( 'ngl_newsletter_cat', array( 'newsletterglue' ), $args );

		// Add default terms (pattern categories)
		$default_categories = array(
			'archive' 		=> __( 'Archive', 'newsletter-glue' ),
		);

		foreach( $default_categories as $cat_id => $cat_name ) {
			$term = term_exists( $cat_id, 'ngl_newsletter_cat' );
			if ( ! $term ) {
				wp_insert_term( $cat_name, 'ngl_newsletter_cat', array( 'slug' => $cat_id ) );
			}
		}

		// Create pattern post type.
		$args = array(
			'labels'             => array(
				'name'                  => __( 'Patterns', 'newsletter-glue' ),
				'singular_name'         => __( 'Pattern', 'newsletter-glue' ),
				'menu_name'             => esc_html_x( 'All Patterns', 'Admin menu name', 'newsletter-glue' ),
				'add_new'               => __( 'Add New', 'newsletter-glue' ),
				'add_new_item'          => __( 'Add New Pattern', 'newsletter-glue' ),
				'edit'                  => __( 'Edit', 'newsletter-glue' ),
				'edit_item'             => __( 'Edit Pattern', 'newsletter-glue' ),
				'new_item'              => __( 'New Pattern', 'newsletter-glue' ),
				'view_item'             => __( 'View Pattern', 'newsletter-glue' ),
				'search_items'          => __( 'Search Patterns', 'newsletter-glue' ),
				'not_found'             => __( 'No Patterns found', 'newsletter-glue' ),
				'not_found_in_trash'    => __( 'No Patterns found in trash', 'newsletter-glue' ),
				'parent'                => __( 'Parent Pattern', 'newsletter-glue' ),
				'filter_items_list'     => __( 'Filter Patterns', 'newsletter-glue' ),
				'items_list_navigation' => __( 'Patterns navigation', 'newsletter-glue' ),
				'items_list'            => __( 'Patterns list', 'newsletter-glue' ),
			),
			'description'       	=> __( 'Description', 'newsletter-glue' ),
			'query_var'         	=> false,
			'supports'          	=> array( 'title', 'editor', 'custom-fields' ),
			'taxonomies'        	=> array( 'ngl_pattern_category' ),
			'publicly_queryable'  	=> true,
			'exclude_from_search' 	=> true,
			'show_ui'           	=> true,
			'rewrite'           	=> false,
			'show_in_rest'      	=> true,
			'show_in_menu'      	=> false,
			'show_in_admin_bar' 	=> false,
		);

		register_post_type( 'ngl_pattern', $args );

		// Create pattern category taxonomy.
		$args = array(
			'label'        			=> __( 'Pattern category', 'newsletter-glue' ),
			'hierarchical' 			=> true,
			'rewrite'      			=> false,
			'show_in_rest' 			=> true,
			'show_admin_column'		=> true,
		);

		register_taxonomy( 'ngl_pattern_category', array( 'ngl_pattern' ), $args );

		// Add default terms (pattern categories)
		$default_categories = array(
			'ngl_headers' 		=> __( 'Headers', 'newsletter-glue' ),
			'ngl_body' 			=> __( 'Body', 'newsletter-glue' ),
			'ngl_signoffs' 		=> __( 'Sign-offs', 'newsletter-glue' ),
			'ngl_footers' 		=> __( 'Footers', 'newsletter-glue' ),
			'ngl_uncategorized' => __( 'Uncategorized', 'newsletter-glue' ),
		);

		foreach( $default_categories as $cat_id => $cat_name ) {
			$term = term_exists( $cat_id, 'ngl_pattern_category' );
			if ( ! $term ) {
				wp_insert_term( $cat_name, 'ngl_pattern_category', array( 'slug' => $cat_id ) );
			}
		}

		// Register post meta for rest use.
		register_post_meta(
			'newsletterglue',
			'_webview',
			array(
				'show_in_rest' 	=> true,
				'single'       	=> true,
				'type'         	=> 'string',
				'default'       => 'blog',
				'auth_callback' => function () { return current_user_can( 'manage_newsletterglue' ); }
			)
		);

		register_post_meta(
			'ngl_pattern',
			'_webview',
			array(
				'show_in_rest' 	=> true,
				'single'       	=> true,
				'type'         	=> 'string',
				'default'       => 'blog',
				'auth_callback' => function () { return current_user_can( 'manage_newsletterglue' ); }
			)
		);

		register_post_meta(
			'ngl_pattern',
			'_ngl_core_pattern',
			array(
				'show_in_rest' 	=> true,
				'single'       	=> true,
				'type'         	=> 'string',
				'default'       => '',
				'auth_callback' => function () { return current_user_can( 'manage_newsletterglue' ); }
			)
		);

		register_taxonomy_for_object_type( 'post_tag', 'newsletterglue' );

		do_action( 'newsletterglue_after_register_post_type' );

	}

	/**
	 * Add scripts.
	 */
	public static function admin_enqueue_scripts() {
		global $post_type;

		if ( ! is_admin() || empty( $post_type ) ) {
			return;
		}

		// Only in our CPT.
		if ( in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
			wp_add_inline_script(
				'wp-edit-post',
				'
				wp.data.select( "core/edit-post" ).isFeatureActive( "welcomeGuide" ) && wp.data.dispatch( "core/edit-post" ).toggleFeature( "welcomeGuide" );
				var isFullScreenMode = wp.data.select( "core/edit-post" ).isFeatureActive( "fullscreenMode" );
				if ( ! isFullScreenMode ) {
					wp.data.dispatch( "core/edit-post" ).toggleFeature( "fullscreenMode" );
				}
				wp.domReady(function () {
				  const allowedEmbedBlocks = [
					"twitter",
					"youtube",
					"spotify",
					"reddit",
					"soundcloud"
				  ];
				  wp.blocks.getBlockVariations( "core/embed" ).forEach(function (blockVariation) {
					if (-1 === allowedEmbedBlocks.indexOf(blockVariation.name)) {
					  wp.blocks.unregisterBlockVariation( "core/embed", blockVariation.name);
					}
				  });
				});
				'
			);
		}

	}

	/**
	 * Allowed block types.
	 */
	public static function allowed_block_types( $blocks, $post ) {
		if ( ! empty( $post->post_type ) ) {
			if ( in_array( $post->post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
				$blocks = array(
					'newsletterglue/group',
					'newsletterglue/form',
					'newsletterglue/article',
					'newsletterglue/author',
					'newsletterglue/callout',
					'newsletterglue/metadata',
					'newsletterglue/share',
					'newsletterglue/share-link',
					'core/block',
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
					'core/embed',
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

		if ( empty( $post_type ) ) {
			return;
		}

		// Add What are patterns?
		if ( $post_type == 'ngl_pattern' ) {
			?>
			<style type="text/css">
			.editor-post-taxonomies__hierarchical-terms-input + div {
				opacity: 0;
				visibility: hidden !important;
				height: 0px !important;
			}
			</style>
			<script type="text/javascript">
				//jQuery( document ).ready( function ( $ ) {
					//var text = "<?php echo __( 'What are Patterns?', 'newsletter-glue' ); ?>";
					//$( '.page-title-action' ).after( '<a href="#" style="font-weight: 600; text-decoration: none !important; font-size: 14px; margin-left: 20px; position: relative; top: -3px;">' + text + '</a>' );
				//} );
			</script>
			<?php
		}

		if ( in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {

			$spacer_bg = 'transparent';

			echo '<style>';

			echo '.edit-post-visual-editor { background: ' . newsletterglue_get_theme_option( 'email_bg' ) . '; }';
			echo 'div.editor-styles-wrapper { background-color: ' . newsletterglue_get_theme_option( 'container_bg' ) . ' !important; }';

			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block { padding-bottom: 0; margin: 0; max-width: 100%; border: 0; }';

			if ( newsletterglue_get_theme_option( 'font' ) ) {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, div.editor-styles-wrapper p, div.editor-styles-wrapper ol, div.editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt,div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper .wp-block h6, div.editor-styles-wrapper h1, div.editor-styles-wrapper h2, div.editor-styles-wrapper h3, div.editor-styles-wrapper h4,
				div.editor-styles-wrapper h5, div.editor-styles-wrapper h6 {
						font-family: ' . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . '!important; }';
			} else {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, div.editor-styles-wrapper p, div.editor-styles-wrapper ol, div.editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt,div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper .wp-block h6, div.editor-styles-wrapper h1, div.editor-styles-wrapper h2, div.editor-styles-wrapper h3, div.editor-styles-wrapper h4,
				div.editor-styles-wrapper h5, div.editor-styles-wrapper h6 {
						font-family: Arial, Helvetica, sans-serif; !important; }';
			}

			echo 'div.editor-styles-wrapper a, div.editor-styles-wrapper .wp-block a { color: ' . newsletterglue_get_theme_option( 'a_colour' ) . '; text-decoration: none !important; }';

			echo 'div.editor-styles-wrapper a, div.editor-styles-wrapper .wp-block.has-text-color a { color: inherit; text-decoration: underline !important; }';

			echo 'div.editor-styles-wrapper, div.editor-styles-wrapper p { color: ' . newsletterglue_get_theme_option( 'p_colour' ) . '; }';
			echo 'div.editor-styles-wrapper p, div.editor-styles-wrapper li, div.editor-styles-wrapper blockquote.wp-block-quote p, div.editor-styles-wrapper blockquote p { font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; }';
			echo '.editor-styles-wrapper blockquote.wp-block-quote p { font-weight: normal; }';

			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block textarea.editor-post-title__input, div.editor-styles-wrapper h1, div.editor-styles-wrapper .wp-block h1 { font-weight: bold !important; font-size: ' . newsletterglue_get_theme_option( 'h1_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h1_colour' ) . '; font-weight: bold; }';

			echo 'div.editor-styles-wrapper h2, div.editor-styles-wrapper .wp-block h2 { font-size: ' . newsletterglue_get_theme_option( 'h2_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h2_colour' ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h3, div.editor-styles-wrapper .wp-block h3 { font-size: ' . newsletterglue_get_theme_option( 'h3_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h3_colour' ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h4, div.editor-styles-wrapper .wp-block h4 { font-size: ' . newsletterglue_get_theme_option( 'h4_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h4_colour' ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h5, div.editor-styles-wrapper .wp-block h5 { font-size: ' . newsletterglue_get_theme_option( 'h5_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h5_colour' ) . '; font-weight: bold; }';
			echo 'div.editor-styles-wrapper h6, div.editor-styles-wrapper .wp-block h6 { font-size: ' . newsletterglue_get_theme_option( 'h6_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h6_colour' ) . '; font-weight: bold; }';

			echo 'div.editor-styles-wrapper .wp-block-button__link,
				div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover,
				div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:active
				{ font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; text-transform: none; padding: 14px 20px; font-weight: inherit; min-width: ' . newsletterglue_get_theme_option( 'btn_width' ) . 'px; background-color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '; color: ' . newsletterglue_get_theme_option( 'btn_colour' ) . '; border: 0 !important; line-height: 1.3 !important; }';

			echo 'div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link,
				div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:hover,
				div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link:active
			{ padding: 12px 20px; color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '!important; background-color: transparent !important; border: 2px solid ' . newsletterglue_get_theme_option( 'btn_bg' ) . '!important; }';

			echo 'div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover, div.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:active {
					background-color: 0 !important;
			}';

			echo 'div.editor-styles-wrapper .wp-block .wp-block-newsletterglue-callout.is-color-set .block-editor-block-list__layout > * { color: inherit; }';

			echo '.wp-block-spacer, div.block-library-spacer__resize-container.has-show-handle { background-color: ' . $spacer_bg . '; }';

			echo '</style>';

		}

	}

	/**
	 * Register custom post type posts (with the 'pattern' type) as block patterns.
	 */
	public static function load_block_patterns() {

		$query_args = array(
			'post_type'              => 'ngl_pattern',
			'post_status'			 => 'publish',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$block_patterns_query = new \WP_Query( $query_args );

		wp_reset_postdata();

		if ( empty( $block_patterns_query->posts ) ) {
			return;
		}

		$pattern_categories = '';

		foreach ( $block_patterns_query->posts as $block_pattern ) {
			$pattern_categories = null;

			$categories = get_the_terms( $block_pattern->ID, 'ngl_pattern_category' );

			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$pattern_categories = wp_list_pluck( $categories, 'slug' );
			}

			if ( empty( $pattern_categories ) ) {
				$pattern_categories = array( 'ngl_uncategorized' );
			} else {

				foreach( $pattern_categories as $key => $value ) {
					if ( substr( $value, 0, 4 ) !== 'ngl_' ) {
						$pattern_categories[] = 'ngl_' . str_replace( '-', '_', $value );
					}
				}

			}

			register_block_pattern(
				'ngl_pattern/' . $block_pattern->post_name,
				array(
					'title'       => $block_pattern->post_title,
					'content'     => $block_pattern->post_content,
					'categories'  => $pattern_categories,
				)
			);
		}

	}

	/**
	 * Register custom post type posts (with the 'pattern' type) as block patterns.
	 */
	public static function register_block_category() {

		$unregister_default_patterns = false;

		$post_id 	= isset( $_GET[ 'post' ] ) ? absint( $_GET[ 'post' ] ) : 0;
		$edit	 	= isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'edit' ? true : false;

		if ( $post_id && $edit ) {
			$thepost = get_post( $post_id );
			if ( in_array( $thepost->post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
				$unregister_default_patterns = true;
			}
		}

		if ( isset( $_GET[ 'post_type' ] ) ) {
			if ( in_array( $_GET[ 'post_type' ], array( 'newsletterglue', 'ngl_pattern' ) ) ) {
				$unregister_default_patterns = true;
			}
		}

		if ( class_exists( 'WP_Block_Patterns_Registry' ) ) {

			register_block_pattern_category(
				'ngl_headers',
				array( 'label' => _x( 'Headers', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_body',
				array( 'label' => _x( 'Body', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_signoffs',
				array( 'label' => _x( 'Sign-offs', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_footers',
				array( 'label' => _x( 'Footers', 'Block pattern category', 'newsletter-glue' ) )
			);

			register_block_pattern_category(
				'ngl_uncategorized',
				array( 'label' => _x( 'Uncategorized', 'Block pattern category', 'newsletter-glue' ) )
			);

			// Get all terms.
			$terms = get_terms( array(
				'taxonomy'		=> 'ngl_pattern_category',
				'hide_false' 	=> false,
				'orderby'		=> 'term_id',
				'order'			=> 'asc'
			) );

			if ( $terms ) {
				foreach( $terms as $term ) {
					if ( substr( $term->slug, 0, 4 ) !== 'ngl_' ) {
						register_block_pattern_category(
							'ngl_' . str_replace( '-', '_', $term->slug ),
							array( 'label' => _x( $term->name, 'Block pattern category', 'newsletter-glue' ) )
						);
					}
				}
			}

			// Unregister everything else.
			if ( $unregister_default_patterns ) {
				$categories = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();
				foreach( $categories as $key => $value ) {
					if ( ! strstr( $value[ 'name' ], 'ngl_' ) ) {
						unregister_block_pattern_category( $value[ 'name' ] );
					}
				}
				$patterns = WP_Block_Patterns_Registry::get_instance()->get_all_registered();
				foreach( $patterns as $key => $value ) {
					if ( ! strstr( $value[ 'name' ], 'ngl_pattern/' ) ) {
						unregister_block_pattern( $value[ 'name' ] );
					}
				}
			}

		}

	}

	/**
	 * Remove date filter.
	 */
	public static function months_dropdown_results( $months ) {
		global $typenow;

		if ( $typenow == 'ngl_pattern' ) {
			return array();
		}

		return $months;
	}

	/**
	 * Add category dropdown filter.
	 */
	public static function restrict_manage_posts() {
		global $typenow, $post, $post_id;

		if ( $typenow == 'ngl_pattern' ) {

			$post_type 	= get_query_var( 'post_type' ); 
			$taxonomies = get_object_taxonomies( $post_type );

			if ( $taxonomies ) {
				foreach( $taxonomies as $tax_slug ) {
					$tax_obj = get_taxonomy( $tax_slug );
					$tax_name = $tax_obj->labels->name;
					$terms = get_terms( array( 'taxonomy' => $tax_slug, 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'asc' ) );
					echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
					echo "<option value=''>" . __( 'All Categories', 'newsletter-glue' ) . "</option>";
					foreach ( $terms as $term ) { 
						$label = ( isset( $_GET[ $tax_slug ] ) ) ? $_GET[ $tax_slug ] : '';
						echo '<option value=' . $term->slug, $label == $term->slug ? ' selected="selected"' : '','>' . $term->name . '</option>';
					}
					echo "</select>";
				}
			}
		}
	}

	/**
	 * Views edit.
	 */
	public static function views_edit( $views ) {

		$terms = get_terms( array( 'taxonomy' => 'ngl_pattern_category', 'hide_empty' => false, 'orderby' => 'term_id', 'order' => 'asc' ) );

		unset( $views[ 'publish' ] );

		$current = '';

		foreach( $terms as $term ) {
			if ( strstr( $term->slug, 'ngl_' ) ) {
				if ( isset( $_GET[ 'ngl_pattern_category' ] ) ) {
					if ( $_GET[ 'ngl_pattern_category' ] == $term->slug ) {
						$current = 'current';
					} else {
						$current = '';
					}
				}
				$views[ $term->slug ] = '<a href="' . admin_url( 'edit.php?post_type=ngl_pattern&ngl_pattern_category=' . $term->slug ) . '" class="' . $current . '">' . $term->name . ' <span class="count">(' . $term->count . ')</span></a>';
			}
		}

		return $views;

	}

	/**
	 * Save a newsletter.
	 */
	public static function save_newsletter( $post_id, $post ) {
		// $post_id and $post are required
		$saved_meta_boxes = false;

		// only for patterns.
		if ( $post->post_type !== 'newsletterglue' ) {
			return;
		}

		// Require post ID and post object.
		if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		// Only allow published and scheduled posts.
		if ( ! in_array( $post->post_status, array( 'publish' ) ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		$saved_meta_boxes = true;

		$terms = wp_get_object_terms( $post_id, 'ngl_newsletter_cat' );
		if ( empty( $terms ) ) {
			wp_set_object_terms( $post_id, array( 'archive' ), 'ngl_newsletter_cat' );
		}
	}

	/**
	 * Save a pattern.
	 */
	public static function save_pattern( $post_id, $post ) {
		// $post_id and $post are required
		$saved_meta_boxes = false;

		// only for patterns.
		if ( $post->post_type !== 'ngl_pattern' ) {
			return;
		}

		// Require post ID and post object.
		if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		// Only allow published and scheduled posts.
		if ( ! in_array( $post->post_status, array( 'publish' ) ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		$saved_meta_boxes = true;

		$terms = wp_get_object_terms( $post_id, 'ngl_pattern_category' );
		if ( empty( $terms ) ) {
			wp_set_object_terms( $post_id, array( 'ngl_uncategorized' ), 'ngl_pattern_category' );
		}
	}

	/**
	 * Row actions.
	 */
	public static function post_row_actions( $actions, $post ) {

		if ( $post->post_type == 'ngl_pattern' ) {
			$actions[ 'ngl_duplicate' ] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=ngl_duplicate_as_pattern&post=' . $post->ID ), basename(__FILE__), 'ngl_duplicate_nonce' ) . '" title="' . __( 'Duplicate this pattern', 'newsletter-glue' ) . '" rel="permalink">' . __( 'Duplicate', 'newsletter-glue' ) . '</a>';
		}

		if ( $post->post_type == 'newsletterglue' ) {
			$actions[ 'ngl_duplicate' ] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=ngl_duplicate_as_newsletter&post=' . $post->ID ), basename(__FILE__), 'ngl_duplicate_nonce' ) . '" title="' . __( 'Duplicate this newsletter', 'newsletter-glue' ) . '" rel="permalink">' . __( 'Duplicate', 'newsletter-glue' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Duplicate pattern.
	 */
	public static function duplicate_pattern() {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_REQUEST['action']) && 'ngl_duplicate_as_pattern' == $_REQUEST['action'] ) ) ) {
			wp_die( __( 'Nothing to duplicate was found.', 'newsletter-glue' ) );
		}

		if ( !isset( $_GET['ngl_duplicate_nonce'] ) || ! wp_verify_nonce( $_GET[ 'ngl_duplicate_nonce' ], basename( __FILE__ ) ) )
			return;

		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && $post != null ) {

			$new_post = newsletterglue_duplicate_item( $post, $post_id );

			wp_redirect( admin_url( 'edit.php?post_type=ngl_pattern' ) );

			exit;

		} else {
			wp_die( __( 'Duplicate pattern has failed.', 'newsletter-glue' ) );
		}

	}

	/**
	 * Duplicate newsletter.
	 */
	public static function duplicate_newsletter() {
		global $wpdb;

		if ( ! ( isset( $_GET['post']) || isset( $_POST['post'] )  || ( isset( $_REQUEST['action']) && 'ngl_duplicate_as_newsletter' == $_REQUEST['action'] ) ) ) {
			wp_die( __( 'Nothing to duplicate was found.', 'newsletter-glue' ) );
		}

		if ( !isset( $_GET['ngl_duplicate_nonce'] ) || ! wp_verify_nonce( $_GET[ 'ngl_duplicate_nonce' ], basename( __FILE__ ) ) )
			return;

		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if ( isset( $post ) && $post != null ) {

			$new_post = newsletterglue_duplicate_item( $post, $post_id );

			wp_redirect( admin_url( 'edit.php?post_type=newsletterglue' ) );

			exit;

		} else {
			wp_die( __( 'Duplicate newsletter has failed.', 'newsletter-glue' ) );
		}

	}

	/**
	 * Enqueue block editor js.
	 */
	public static function enqueue_block_editor_assets() {
		global $post_type;

		if ( ! in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
			return;
		}

		$js_dir = NGL_PLUGIN_URL . 'assets/js/gutenberg/';

		// Enqueue block editor JS
		wp_enqueue_script(
			'ngl-editor-js',
			$js_dir . 'editor.js',
			array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
			time()
		);

		$app = newsletterglue_default_connection();
		if ( $app ) {
			include_once newsletterglue_get_path( $app ) . '/functions.php';
			$function = 'newsletterglue_get_' . strtolower( $app ) . '_tags';
			if ( function_exists( $function ) ) {
				wp_enqueue_script(
					'ngl-editor-bw-js',
					$js_dir . 'editor-bw.js',
					array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
					time()
				);
			}
		}
	}

	/**
	 * Set a default newsletter title.
	 */
	public static function add_newsletter_title( $post_id, $post ) {

		$saved_meta_boxes = false;

		if ( empty( $post_id ) || empty( $post ) || $saved_meta_boxes ) {
			return;
		}

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		if ( ! isset( $_POST['post_type'] ) || $_POST['post_type'] != 'newsletterglue' ) {
			return;
		}

		$saved_meta_boxes = true;

		global $wpdb;

        $meta = get_post_meta( $post_id, '_newsletterglue', true );

        $title = ! empty( $meta[ 'subject' ] ) ? esc_html( $meta[ 'subject' ] ) : __( 'Untitled newsletter', 'newsletter-glue' );

        $where = array( 'ID' => $post_id );

        $wpdb->update( $wpdb->posts, array( 'post_title' => $title, 'post_name' => sanitize_title( $title, $post_id ) ), $where );

	}

	/**
	 * Pattern tabs.
	 */
	public static function add_topbar() {
		global $post_type, $pagenow;
		return;
		if ( $pagenow == 'edit.php' && $post_type == 'ngl_pattern' ) {

		?>
		<nav class="nav-tab-wrapper" style="padding-top: 30px;">
			<?php
				$tabs = array(
					'ngl_template'		=> __( 'Templates', 'newsletter-glue' ),
					'ngl_pattern'		=> __( 'Patterns', 'newsletter-glue' ),
					'ngl_style'			=> __( 'Styles', 'newsletter-glue' ),
				);

				foreach( $tabs as $key => $name ) {
					$current = $key === $post_type ? 'nav-tab-active' : '';
					echo '<a href="' . admin_url( 'edit.php?post_type=' . $key ) . '" class="nav-tab ' . $current . '">' . $name . '</a>';
				}
			?>
		</nav>
		<?php
		}
	}

	/**
	 * Force Gutenberg use - compatibility issues.
	 */
	public static function use_block_editor_for_post_type( $is_enabled, $post_type ) {

		if ( in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
			return true;
		}

		return $is_enabled;
	}

	/**
	 * Webview.
	 */
	public static function show_webview() {
		global $post;

		if ( is_single() && isset( $post ) && ! empty( $post->post_type ) && $post->post_type == 'newsletterglue' ) {
			$webview = get_post_meta( $post->ID, '_webview', true );
			if ( $webview === 'email' ) {
				ob_start();

				$post_id 	= $post->ID;
				$data 		= get_post_meta( $post_id, '_newsletterglue', true );
				$app 		= isset( $data[ 'app' ] ) ? $data[ 'app' ] : '';

				if ( $app ) {
					include_once newsletterglue_get_path( $app ) . '/init.php';
					$classname = 'NGL_' . ucfirst( $app );
					$api = new $classname();
				}

				echo newsletterglue_generate_content( $post_id, ! empty( $data[ 'subject' ] ) ? $data[ 'subject' ] : '', $app );

				$message = ob_get_clean();

				echo $message;

				exit;
			}
		}
	}

}

NGL_CPT::init();
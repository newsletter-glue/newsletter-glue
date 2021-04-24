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
		// Register post types.
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );

		// Load block patterns.
		add_action( 'init', array( __CLASS__, 'load_block_patterns' ), 7 );

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
					'description'         => __( 'This is where you can add new Newsletters to Newsletter Glue plugin.', 'newsletter-glue' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'map_meta_cap'        => true,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_newsletterglue' ) ? 'newsletter-glue' : true,
					'hierarchical'        => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'editor', 'thumbnail' ),
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
					'show_in_rest'		  => true,
				)
			)
		);

		// Create pattern post type.
		$args = array(
			'labels'             => array(
				'name'                  => __( 'Patterns', 'newsletter-glue' ),
				'singular_name'         => __( 'Pattern', 'newsletter-glue' ),
				'menu_name'             => esc_html_x( 'All Patterns', 'Admin menu name', 'newsletter-glue' ),
				'add_new'               => __( 'Add New', 'newsletter-glue' ),
				'add_new_item'          => __( 'Add New Newsletter', 'newsletter-glue' ),
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
			'description'       => __( 'Description', 'newsletter-glue' ),
			'supports'          => array( 'title', 'editor', 'excerpt' ),
			'taxonomies'        => array( 'ngl_pattern_category' ),
			'show_ui'           => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
			'show_in_menu'      => false,
			'show_in_admin_bar' => false,
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

		do_action( 'newsletterglue_after_register_post_type' );

	}

	/**
	 * Add scripts.
	 */
	public static function admin_enqueue_scripts() {
		global $post_type;

		// Only in our CPT.
		if ( is_admin() && ! empty( $post_type ) && in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
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
			if ( in_array( $post->post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {
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

		if ( empty( $post_type ) ) {
			return;
		}

		// Add What are patterns?
		if ( $post_type == 'ngl_pattern' ) {
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					var text = "<?php echo __( 'What are Patterns?', 'newsletter-glue' ); ?>";
					$( '.page-title-action' ).after( '<a href="#" style="font-weight: 600; text-decoration: none !important; font-size: 14px; margin-left: 20px; position: relative; top: -3px;">' + text + '</a>' );
				} );
			</script>
			<?php
		}

		if ( in_array( $post_type, array( 'newsletterglue', 'ngl_pattern' ) ) ) {

			echo '<style>';

			echo '.edit-post-visual-editor { background: ' . newsletterglue_get_theme_option( 'email_bg' ) . '; }';
			echo 'div.editor-styles-wrapper { background-color: ' . newsletterglue_get_theme_option( 'container_bg' ) . '; }';

			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block { padding-bottom: 0; margin: 0; max-width: 100%; border: 0; }';

			if ( newsletterglue_get_theme_option( 'font' ) ) {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, .editor-styles-wrapper p, .editor-styles-wrapper ol, .editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt, div.editor-styles-wrapper .wp-block h1, div.editor-styles-wrapper .wp-block h2, div.editor-styles-wrapper .wp-block h3, div.editor-styles-wrapper .wp-block h4, div.editor-styles-wrapper .wp-block h5, div.editor-styles-wrapper .wp-block h6 {
						font-family: ' . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . '!important; }';
			} else {
				echo '.editor-styles-wrapper > *, div.editor-styles-wrapper textarea.editor-post-title__input, .editor-styles-wrapper p, .editor-styles-wrapper ol, .editor-styles-wrapper ul, .editor-styles-wrapper dl, .editor-styles-wrapper dt {
						font-family: Arial, Helvetica, sans-serif; !important; }';
			}

			echo 'div.editor-styles-wrapper, div.editor-styles-wrapper p { color: ' . newsletterglue_get_theme_option( 'p_colour' ) . '; }';
			echo 'div.editor-styles-wrapper p, div.editor-styles-wrapper li { font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; }';
			echo 'div.editor-styles-wrapper .wp-block.editor-post-title__block textarea.editor-post-title__input, div.editor-styles-wrapper h1, div.editor-styles-wrapper .wp-block h1 { font-weight: bold !important; font-size: ' . newsletterglue_get_theme_option( 'h1_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h1_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h2, div.editor-styles-wrapper .wp-block h2 { font-size: ' . newsletterglue_get_theme_option( 'h2_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h2_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h3, div.editor-styles-wrapper .wp-block h3 { font-size: ' . newsletterglue_get_theme_option( 'h3_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h3_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h4, div.editor-styles-wrapper .wp-block h4 { font-size: ' . newsletterglue_get_theme_option( 'h4_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h4_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h5, div.editor-styles-wrapper .wp-block h5 { font-size: ' . newsletterglue_get_theme_option( 'h5_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h5_colour' ) . '; }';
			echo 'div.editor-styles-wrapper h6, div.editor-styles-wrapper .wp-block h6 { font-size: ' . newsletterglue_get_theme_option( 'h6_size' ) . 'px; color: ' . newsletterglue_get_theme_option( 'h6_colour' ) . '; }';
			echo 'div.editor-styles-wrapper .wp-block-button__link { font-size: ' . newsletterglue_get_theme_option( 'p_size' ) . 'px; text-transform: none; padding: 14px 20px; font-weight: inherit; min-width: ' . newsletterglue_get_theme_option( 'btn_width' ) . 'px; background-color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '; color: ' . newsletterglue_get_theme_option( 'btn_colour' ) . '; }';
			echo 'div.editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link { padding: 12px 20px; color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '; border-color: ' . newsletterglue_get_theme_option( 'btn_bg' ) . '; }';

			echo 'div.editor-styles-wrapper .wp-block .wp-block-newsletterglue-callout .block-editor-block-list__layout > * { color: inherit; }';

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
					'description' => $block_pattern->post_excerpt,
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
					$terms = get_terms( $tax_slug );
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

		$terms = get_terms( array( 'taxonomy' => 'ngl_pattern_category', 'hide_empty' => false ) );

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

}

NGL_CPT::init();
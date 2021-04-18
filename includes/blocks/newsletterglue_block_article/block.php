<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Article extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_article';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			// Ajax hooks.
			add_action( 'wp_ajax_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_labels', array( $this, 'update_labels' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_labels', array( $this, 'update_labels' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );

			add_action( 'wp_ajax_newsletterglue_save_article_image', array( $this, 'save_article_image' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_save_article_image', array( $this, 'save_article_image' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_clear_cache', array( $this, 'clear_cache' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_clear_cache', array( $this, 'clear_cache' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_url', array( $this, 'update_url' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_url', array( $this, 'update_url' ) );

			add_filter( 'newsletterglue_article_embed_content', array( $this, 'remove_div' ), 50, 2 );
		}

	}

	/**
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 40.625" class="ngl-block-svg-icon">
			<path d="M7.813,34.625H1.563A1.562,1.562,0,0,0,0,36.188v6.25A1.563,1.563,0,0,0,1.562,44h6.25a1.563,1.563,0,0,0,1.563-1.562v-6.25A1.562,1.562,0,0,0,7.813,34.625Zm0-31.25H1.563A1.563,1.563,0,0,0,0,4.938v6.25A1.562,1.562,0,0,0,1.562,12.75h6.25a1.562,1.562,0,0,0,1.563-1.562V4.938A1.563,1.563,0,0,0,7.813,3.375ZM7.813,19H1.563A1.563,1.563,0,0,0,0,20.563v6.25a1.562,1.562,0,0,0,1.562,1.563h6.25a1.562,1.562,0,0,0,1.563-1.562v-6.25A1.563,1.563,0,0,0,7.813,19ZM48.438,36.188H17.188a1.563,1.563,0,0,0-1.562,1.563v3.125a1.563,1.563,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,40.875V37.75A1.563,1.563,0,0,0,48.438,36.188Zm0-31.25H17.188A1.562,1.562,0,0,0,15.625,6.5V9.625a1.562,1.562,0,0,0,1.563,1.563h31.25A1.563,1.563,0,0,0,50,9.625V6.5A1.563,1.563,0,0,0,48.438,4.938Zm0,15.625H17.188a1.562,1.562,0,0,0-1.562,1.562V25.25a1.562,1.562,0,0,0,1.563,1.563h31.25A1.562,1.562,0,0,0,50,25.25V22.125A1.563,1.563,0,0,0,48.438,20.563Z" transform="translate(0 -3.375)"/>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Post embeds', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Bulk embed articles and customise their layout.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'show_in_blog' 	=> true,
			'show_in_email' => true,
		);

	}

	/**
	 * Register the block.
	 */
	public function register_block() {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		$defaults[ 'name' ]			= __( 'NG: Post embeds', 'newsletter-glue' );
		$defaults[ 'description' ] 	= __( 'Bulk embed articles and customise their layout.', 'newsletter-glue' );

		// Post dates.
		$formats = $this->get_date_formats();
		$date_formats = array();
		foreach( $formats as $format ) {
			$date_formats[] = array( 'value' => $format, 'label' => date( $format, current_time( 'timestamp' ) ) );
		}
		$defaults[ 'date_formats' ] = $date_formats;

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/article', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'	  => array(
				'show_in_blog' => array(
					'type' 		=> 'boolean',
					'default' 	=> $defaults[ 'show_in_blog' ],
				),
				'show_in_email' => array(
					'type' 		=> 'boolean',
					'default' 	=> $defaults[ 'show_in_email' ],
				),
				'block_id'		=> array(
					'type'		=> 'string',
				),
				'border_color'	=> array(
					'type'		=> 'string',
				),
				'background_color'	=> array(
					'type'		=> 'string',
				),
				'text_color'	=> array(
					'type'		=> 'string',
				),
				'link_color'	=> array(
					'type'		=> 'string',
				),
				'border_radius'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'border_size'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'font_size_title' => array(
					'type'		=> 'number',
					'default'	=> 18,
				),
				'font_size_text' => array(
					'type'		=> 'number',
					'default'	=> 14,
				),
				'border_style'	=> array(
					'type'		=> 'string',
					'default'	=> 'solid',
				),
				'show_image'	=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'show_date'		=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'show_labels'	=> array(
					'type'		=> 'boolean',
					'default'	=> true,
				),
				'image_radius'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'date_format'	=> array(
					'type'		=> 'string',
				),
				'new_window'	=> array(
					'type'		=> 'boolean',
					'default'	=> false,
				),
				'nofollow'		=> array(
					'type'		=> 'boolean',
					'default'	=> false,
				),
				'image_position'	=> array(
					'type'		=> 'string',
					'default'	=> 'left',
				),
				'table_ratio'	=> array(
					'type'		=> 'string',
					'default'	=> 'full',
				),
			),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		ob_start();

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'show_in_blog'	=> true,
				'show_in_email'	=> true,
			);
		}

		$show_in_blog  = isset( $attributes[ 'show_in_blog' ] ) ? $attributes[ 'show_in_blog' ] : $defaults[ 'show_in_blog' ];
		$show_in_email = isset( $attributes[ 'show_in_email' ] ) ? $attributes[ 'show_in_email' ] : $defaults[ 'show_in_email' ];

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			if ( ! defined( 'REST_REQUEST' ) ) {
				echo '';
				return ob_get_clean();
			}
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			if ( ! defined( 'REST_REQUEST' ) ) {
				echo '';
				return ob_get_clean();
			}
		}

		$block_id 			= isset( $attributes[ 'block_id' ] ) ? str_replace( '-', '', $attributes[ 'block_id' ] ) : '';
		$table_ratio 		= isset( $attributes[ 'table_ratio' ] ) ? $attributes[ 'table_ratio' ] : 'full';
		$date_format    	= isset( $attributes[ 'date_format' ] ) ? $attributes[ 'date_format' ] : $this->get_default_date_format();
		$image_position    	= isset( $attributes[ 'image_position' ] ) ? $attributes[ 'image_position' ] : 'left';
		$show_labels   		= isset( $attributes[ 'show_labels' ] ) ? $attributes[ 'show_labels' ] : '';
		$show_date   		= isset( $attributes[ 'show_date' ] ) ? $attributes[ 'show_date' ] : '';
		$show_image   		= isset( $attributes[ 'show_image' ] ) ? $attributes[ 'show_image' ] : '';
		$image_radius   	= isset( $attributes[ 'image_radius' ] ) ? $attributes[ 'image_radius' ] : 0;
		$border_radius   	= isset( $attributes[ 'border_radius' ] ) ? $attributes[ 'border_radius' ] : 0;
		$border_size   		= isset( $attributes[ 'border_size' ] ) ? $attributes[ 'border_size' ] : '';
		$border_style   	= isset( $attributes[ 'border_style' ] ) ? $attributes[ 'border_style' ] : 'solid';
		$border_color   	= isset( $attributes[ 'border_color' ] ) ? $attributes[ 'border_color' ] : 'transparent';
		$background_color   = isset( $attributes[ 'background_color' ] ) ? $attributes[ 'background_color' ] : 'transparent';
		$text_color   		= isset( $attributes[ 'text_color' ] ) ? $attributes[ 'text_color' ] : '';
		$link_color   		= isset( $attributes[ 'link_color' ] ) ? $attributes[ 'link_color' ] : newsletterglue_get_theme_option( 'a_colour' );
		$font_size_title   	= ! empty( $attributes[ 'font_size_title' ] ) ? $attributes[ 'font_size_title' ] : 18;
		$font_size_text   	= ! empty( $attributes[ 'font_size_text' ] ) ? $attributes[ 'font_size_text' ] : 14;
		$new_window   		= ! empty( $attributes[ 'new_window' ] ) ? '_blank' : '_self';
		$nofollow   		= ! empty( $attributes[ 'nofollow' ] ) ? 'nofollow' : '';

		if ( $text_color ) {
			$text_color = "color: $text_color; ";
		}

		if ( $link_color ) {
			$link_color = "color: $link_color !important; ";
		}

		if ( $border_color == 'transparent' && $border_size ) {
			$border_color = '#ddd';
		}

		if ( ! $border_size && $border_radius && ( $border_color == 'transparent' ) ) {
			if ( $background_color == 'transparent' ) {
				$border_size = 1;
				$border_color = '#ddd';
			}
		}

		if ( $border_size || $border_radius ) {
			$padding = '20px';
		} else {
			$padding = '0px';
		}

		$articles = get_option( 'ngl_articles_' . $block_id );

		include( NGL_PLUGIN_DIR . 'includes/blocks/' . $this->id . '/templates/embed.php' );

		$content = ob_get_clean();
		
		if ( defined( 'NGL_IN_EMAIL' ) && $content ) {
			$content = $this->tableize( $content, $attributes );
		}

		return $content;
	}

	/**
	 * Save settings.
	 */
	public function save_settings() {

		delete_option( $this->id );

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array();
		}

		if ( isset( $_POST[ $this->id . '_show_in_email' ] ) ) {
			$defaults[ 'show_in_email' ] = true;
		} else {
			$defaults[ 'show_in_email' ] = false;
		}

		if ( isset( $_POST[ $this->id . '_show_in_blog' ] ) ) {
			$defaults[ 'show_in_blog' ] = true;
		} else {
			$defaults[ 'show_in_blog' ] = false;
		}

		update_option( $this->id, $defaults );

		return $defaults;

	}

	/**
	 * CSS.
	 */
	public function email_css() {
		?>
.ngl-articles {
	padding: 0;
	margin: 0;
}

.ngl-article img {
	display: block;
	overflow: hidden;
}

.ngl-article {
	margin: 0 0 10px 0;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
}

.ngl-article-title {
	margin: 0 0 8px;
	line-height: 150%;
}

.ngl-article-title a {
	font-weight: bold;
	text-decoration: none;
}

.ngl-article-excerpt {
	line-height: 150%;
}

.ngl-article-featured {
	margin: 0 0 14px;
}

.ngl-article-featured a {
	display: block;
}

.ngl-article-featured img {
	margin: 0 !important;
}

.ngl-article-date {
	margin: 8px 0 0;
	font-size: 0.95em;
	opacity: 0.7;
}

.ngl-articles .components-placeholder.components-placeholder {
	min-height: 100px;
}

.ngl-articles input[type=text] {
	padding: 6px 8px;
    box-shadow: 0 0 0 transparent;
    transition: box-shadow 0.1s linear;
    border-radius: 2px;
    border: 1px solid #757575;
    margin: 0 8px 0 0;
    flex: 1 1 auto;
	font-size: 13px;
    line-height: normal;
}

.ngl-article-labels {
	display: block;
	margin: 0 0 6px;
	font-size: 0.95em;
	opacity: 0.8;
}

.ngl-articles-add {
	width: 100%;
}

#template_inner td table.ngl-articles-table {
	border: none;
}

#template_inner td table.ngl-articles-table th,
#template_inner td table.ngl-articles-table td {
	border: none;
	padding: 0;
}

.ngl-articles table {
	border: none;
}

.ngl-articles-full img {
	width: 100%;
	height: auto;
}

.ngl-article-left { display: inline-block; width: 49.5%; vertical-align: top; box-sizing: border-box !important; }
.ngl-article-right { display: inline-block; width: 49.5%; vertical-align: top; padding-left: 20px; box-sizing: border-box !important; }

.ngl-articles-30_70 .ngl-article-left { display: inline-block; width: 30%; vertical-align: top; }
.ngl-articles-30_70 .ngl-article-right { display: inline-block; width: 69%; vertical-align: top; }

.ngl-articles-70_30 .ngl-article-left { display: inline-block; width: 69%; vertical-align: top; }
.ngl-articles-70_30 .ngl-article-right { display: inline-block; width: 30%; vertical-align: top; }

.ngl-article-right .ngl-article-featured { margin: 0; }
.ngl-article-left .ngl-article-featured { margin: 0; }

.ngl-article-left-mobile {
	display: none !important;
	overflow: hidden;
	mso-hide: all;
	margin: 0;
	font-size: 0;
	max-height: 0;
}

.ngl-article-left-mobile * {
	display: none !important;
	overflow: hidden;
	mso-hide: all;
	max-height: 0;
	font-size: 0;
}

@media only screen and (max-width:642px) {

	.ngl-article-left-mobile,
	.ngl-article-left-mobile * {
		display: block !important;
		max-height: 100% !important;
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px !important;
	}

	.ngl-articles-full .ngl-article-left-mobile,
	.ngl-articles-full .ngl-article-left-mobile * {
		display: none !important;
		max-height: 0;
		font-size: 0;
	}

	.ngl-article-img-left,
	.ngl-article-img-right {
		display: none !important;
		overflow: hidden;
		mso-hide: all;
		margin: 0;
		font-size: 0;
		max-height: 0;
	}

}

	<?php
	}

	/**
	 * Get date formats.
	 */
	public function get_date_formats() {
		return array( 'j M Y', 'l, j M Y', 'F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y' );
	}

	/**
	 * Get default date format.
	 */
	public function get_default_date_format() {
		$formats = $this->get_date_formats();

		return $formats[ 0 ];
	}

	/**
	 * Remove this block div from article embeds.
	 */
	public function remove_div( $content, $post_id ) {
		$content = newsletterglue_remove_div( $content, 'ngl-articles' );

		return $content;
	}

	/**
	 * Exerpt length by words.
	 */
	public function excerpt_words() {
		return 30;
	}

	/**
	 * Update title.
	 */
	public function update_title() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_REQUEST[ 'post_id' ] ) ? sanitize_text_field( $_REQUEST[ 'post_id' ] ) : '';
		$title   = isset( $_REQUEST[ 'title' ] ) ? sanitize_text_field( $_REQUEST[ 'title' ] ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'title' ] = $title;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Update labels.
	 */
	public function update_labels() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_REQUEST[ 'post_id' ] ) ? sanitize_text_field( $_REQUEST[ 'post_id' ] ) : '';
		$labels  = isset( $_REQUEST[ 'labels' ] ) ? sanitize_text_field( $_REQUEST[ 'labels' ] ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'labels' ] = $labels;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Update excerpt.
	 */
	public function update_excerpt() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_REQUEST[ 'post_id' ] ) ? sanitize_text_field( $_REQUEST[ 'post_id' ] ) : '';
		$excerpt = isset( $_REQUEST[ 'excerpt' ] ) ? wp_kses_post( $_REQUEST[ 'excerpt' ] ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'excerpt' ] = $excerpt;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

		wp_die();

	}

	/**
	 * Display labels.
	 */
	public function get_labels( $post_id ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'labels' ] ) ) {
			$labels = stripslashes_deep( $custom_data[ $post_id ][ 'labels' ] );
			if ( ! empty( $labels ) ) {
				return $labels;
			} else {
				if ( ! defined( 'NGL_IN_EMAIL' ) && ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
					return __( 'Add label', 'newsletter-glue' );
				}
			}
		}

		if ( ! defined( 'NGL_IN_EMAIL' ) && ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return __( 'Add label', 'newsletter-glue' );
		}

		return '';

	}

	/**
	 * Set custom image.
	 */
	public function set_custom_image( $post_id, $custom_image  ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'custom_image' ] = $custom_image;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

	}

	/**
	 * Get custom image.
	 */
	public function get_custom_image( $post_id ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'custom_image' ] ) ) {
			return esc_attr( $custom_data[ $post_id ][ 'custom_image' ] );
		}

		return false;
	}

	/**
	 * Remove custom image.
	 */
	public function remove_custom_image( $post_id ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			return;
		}

		unset( $custom_data[ $post_id ][ 'custom_image' ] );

		update_option( 'newsletterglue_article_custom_data', $custom_data );

	}

	/**
	 * AJAX save article image.
	 */
	public function save_article_image() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		$key = isset( $_REQUEST[ 'key' ] ) ? sanitize_text_field( $_REQUEST[ 'key' ] ) : '';
		$ids = isset( $_REQUEST[ 'ids' ] ) ? absint( $_REQUEST[ 'ids' ] ) : '';

		if ( $ids ) {

			$url = wp_get_attachment_url( $ids );

			// No URL.
			if ( ! $url ) {
				wp_send_json_error();
			}

			$data = array(
				'id'		=> $ids,
				'url'		=> $url,
				'filename'	=> basename( $url ),
			);

			$this->set_custom_image( $key, $url );

			wp_send_json_success( $data );

		} else {	
			$this->remove_custom_image( $key );
		}

		wp_die();

	}

	/**
	 * Display title.
	 */
	public function display_title( $post_id, $post ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'title' ] ) ) {
			return stripslashes_deep( $custom_data[ $post_id ][ 'title' ] );
		} else {
			return ! empty( $post->title ) ? $post->title : get_the_title( $post );
		}

	}

	/**
	 * Display excerpt.
	 */
	public function display_excerpt( $post_id, $content ) {

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( ! empty( $custom_data ) && isset( $custom_data[ $post_id ][ 'excerpt' ] ) ) {
			return stripslashes_deep( $custom_data[ $post_id ][ 'excerpt' ] );
		} else {
			return wp_trim_words( $content, $this->excerpt_words() );
		}

	}

	/**
	 * Search articles.
	 */
	public function search_articles() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$term = isset( $_REQUEST[ 'term' ] ) ? sanitize_text_field( $_REQUEST[ 'term' ] ) : '';

		if ( ! $term || mb_strlen( $term ) < 3 ) {
			wp_die( -1 );
		}

		add_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$results = new WP_Query( array(
			'post_type'      	=> array( 'post' ),
			'post_status'    	=> 'publish',
			'nopaging'       	=> true,
			'posts_per_page' 	=> 100,
			'ngl_post_title_s'  => $term, // search post title only
		) );

		remove_filter( 'posts_where', array( $this, 'post_title_filter' ), 10, 2 );

		$html = '';

		if ( ! empty( $results->posts ) ) {
			foreach ( $results->posts as $result ) {
				$html .= '<li><a href="#" data-post-id="' . $result->ID . '" data-permalink="' . get_permalink( $result->ID ) . '">' . $result->post_title . '</a></li>';
			}
			wp_send_json( array( 'html' => $html ) );
		} else {
			wp_send_json( array( 'no_results' => true ) );
		}

		wp_die();

	}

	/**
	 * Add search to post titles only.
	 */
	public function post_title_filter( $where, $wp_query ) {
		global $wpdb;
		if ( $term = $wp_query->get( 'ngl_post_title_s' ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $wpdb->esc_like( $term ) . '%\'';
		}
		return $where;
	}

	/**
	 * Remove article.
	 */
	public function remove_article() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$key 		= isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : '';
		$block_id 	= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( ! empty( $articles ) && isset( $articles[ $key ] ) ) {
			unset( $articles[ $key ] );
			if ( ! empty( $articles ) ) {
				update_option( 'ngl_articles_' . $block_id, $articles );
			} else {
				delete_option( 'ngl_articles_' . $block_id );
			}
		}

		wp_die();

	}

	/**
	 * Order article.
	 */
	public function order_articles() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 	= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
		$keys 		= isset( $_REQUEST[ 'keys' ] ) ? sanitize_text_field( $_REQUEST[ 'keys' ] ) : '';
		$values 	= isset( $_REQUEST[ 'values' ] ) ? sanitize_text_field( $_REQUEST[ 'values' ] ) : '';

		if ( $keys && $values ) {
			$updated = array();
			$articles = get_option( 'ngl_articles_' . $block_id );
			$order = array_combine( explode( ',', $keys ), explode( ',', $values ) );
			foreach( $order as $key => $value ) {
				foreach( $articles as $index => $data ) {
					if ( $data[ 'post_id' ] == $value ) {
						$updated[ $key ] = $data;
					}
				}
			}
			update_option( 'ngl_articles_' . $block_id, $updated );
		}

	}

	/**
	 * Get a remote URL.
	 */
	public function get_remote_url( $url ) {

		$url  = untrailingslashit( $url );

		$html = get_transient( 'ngl_' . md5( $url ) );

		if ( false === $html ) {
			$html = file_get_contents( $url );
			if ( $html ) {
				set_transient( 'ngl_' . md5( $url ), $html, 2628000 );
			}
		}

		$data = new stdclass;

		$data->is_remote 	= true;
		$data->favicon 		= 'https://www.google.com/s2/favicons?sz=32&domain_url=' . $url; 
		$data->ID			= $url;

		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $html );
		libxml_clear_errors();
		$nodes = $doc->getElementsByTagName( 'title' );
		$data->title = $nodes->item(0)->nodeValue;

		$metas = $doc->getElementsByTagName( 'meta' );
		for ( $i = 0; $i < $metas->length; $i++ ) {
			$meta = $metas->item( $i );
			if ( $meta->getAttribute( 'name' ) == 'description' ) {
				$data->post_content = $meta->getAttribute( 'content' );
			}
			if ( $meta->getAttribute( 'property' ) =='og:description' ) { 
				$data->post_content = $meta->getAttribute('content');
			}
			if ( $meta->getAttribute( 'property' ) =='og:title' ) { 
				$data->title = $meta->getAttribute('content');
			}
			if ( $meta->getAttribute( 'property' ) =='og:image' ) { 
				$data->image_url = $meta->getAttribute('content');
			}
		}
		if ( empty( $data->post_content ) ) {
			$data->post_content = __( 'No description found.', 'newsletter-glue' );
		}
		if ( empty( $data->image_url ) ) {
			$data->image_url = $this->default_image_url();
		}

		return $data;

	}

	/**
	 * Get featured image URL.
	 */
	public function get_featured( $thearticle ) {
		return has_post_thumbnail( $thearticle ) ? wp_get_attachment_url( get_post_thumbnail_id( $thearticle->ID ), 'full' ) : $this->default_image_url();
	}

	/**
	 * Get default image URL.
	 */
	public function default_image_url() {
		return NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/placeholder.png';
	}

	/**
	 * Get permalink.
	 */
	public function get_permalink( $thearticle ) {
		return ! empty( $thearticle->is_remote ) ? $thearticle->ID : get_permalink( $thearticle->ID );
	}

	/**
	 * Get favicon.
	 */
	public function get_favicon( $thearticle ) {
		
		if ( ! empty( $thearticle->favicon ) ) {
			return $thearticle->favicon;
		}

		if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' ) {
			$url = 'https://newsletterglue.com';
		} else {
			$url = home_url();
		}

		$favicon = 'https://www.google.com/s2/favicons?sz=32&domain_url=' . $url;

		return $favicon;
	}

	/**
	 * AJAX update article.
	 */
	public function update_url() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 		= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
		$key 	        = isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : '';
		$url 	        = isset( $_REQUEST[ 'url' ] ) ? sanitize_text_field( $_REQUEST[ 'url' ] ) : '';
		$date_format 	= isset( $_REQUEST[ 'date_format' ] ) ? sanitize_text_field( $_REQUEST[ 'date_format' ] ) : '';

		$url = untrailingslashit( $url );

		if ( ! $key || ! $block_id ) {
			wp_die();
		}

		if ( ! preg_match( "~^(?:f|ht)tps?://~i", $url ) ) {
			$url = 'https://' . $url;
		}

		if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$error = __( 'Invalid URL.', 'newsletter-glue' );
		}

		if ( ! empty( $error ) ) {
			wp_send_json_error( array( 'error' => $error ) );
		}

		// Try to find out if this is an internal post.
		$post_id  = url_to_postid( $url );
		$post     = get_post( $post_id );

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( empty( $post ) || empty( $post->ID ) ) {

			// External.
			$thearticle = $this->get_remote_url( $url );

			if ( empty( $thearticle->title ) ) {
				wp_send_json_error( array( 'error' => __( 'Invalid URL.', 'newsletter-glue' ) ) );
			}

		} else {

			// Local.
			$thearticle = $post;
		}

		// Update current key with new data.
		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$articles[ $key ] = $embed;

		update_option( 'ngl_articles_' . $block_id, $articles );

		// Show refresh icon.
		if ( ! empty( $thearticle->is_remote ) ) {
			$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';
		} else {
			$refresh_icon	 = '';
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'labels'			=> $this->get_labels( $thearticle->ID ),
			'embed'				=> $embed,
			'date'				=> ! empty( $thearticle->post_date ) ? date_i18n( $date_format, strtotime( $thearticle->post_date ) ) : '',
		);

		wp_send_json_success( $result );

	}

	/**
	 * AJAX embedding article.
	 */
	public function embed_article() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$block_id 		= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
		$thepost 		= isset( $_REQUEST[ 'thepost' ] ) ? sanitize_text_field( $_REQUEST[ 'thepost' ] ) : '';
		$date_format 	= isset( $_REQUEST[ 'date_format' ] ) ? sanitize_text_field( $_REQUEST[ 'date_format' ] ) : '';
		$key 	        = isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : 1;

		if ( is_numeric( $thepost ) ) {
			$thearticle = get_post( $thepost );
		} else {
			$post_id 	= url_to_postid( $thepost );
			$thearticle	= get_post( $post_id );
		}

		if ( empty( $thepost ) ) {
			wp_send_json( array( 'error' => __( 'Please search for a post or type some URL.', 'newsletter-glue' ) ) );
		}

		if ( ! isset( $thearticle->ID ) || empty( $thearticle->ID ) ) {
			$thepost = strpos( $thepost, 'http' ) !== 0 ? "https://$thepost" : $thepost;
			if ( filter_var( $thepost, FILTER_VALIDATE_URL ) ) {
				$thearticle = $this->get_remote_url( $thepost );
				if ( empty( $thearticle->title ) ) {
					wp_send_json( array( 'error' => __( 'Invalid URL.', 'newsletter-glue' ) ) );
				}
			} else {
				wp_send_json( array( 'error' => __( 'Invalid post.', 'newsletter-glue' ) ) );
			}
		}

		$articles = get_option( 'ngl_articles_' . $block_id );

		if ( ! empty( $articles ) ) {
			foreach( $articles as $article => $article_data ) {
				foreach( $article_data as $index => $value ) {
					if ( $index == 'post_id' && $value == $thearticle->ID ) {
						wp_send_json( array( 'error' => __( 'This post is already embedded.', 'newsletter-glue' ) ) );
					}
				}
			}
		} else {
			$articles = array();
		}

		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$articles[ $key ] = $embed;

		update_option( 'ngl_articles_' . $block_id, $articles );

		if ( ! empty( $thearticle->is_remote ) ) {
			$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';
		} else {
			$refresh_icon	 = '';
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'thepost'			=> $thepost,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'date'				=> ! empty( $thearticle->post_date ) ? date_i18n( $date_format, strtotime( $thearticle->post_date ) ) : '',
			'labels'			=> $this->get_labels( $thearticle->ID ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'embed'				=> $embed,
			'success'			=> __( 'Add another post', 'newsletter-glue' ),
		);

		wp_send_json( $result );

	}

	/**
	 * Clear cache for external links.
	 */
	public function clear_cache() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$thepost 		= isset( $_REQUEST[ 'thepost' ] ) ? sanitize_text_field( $_REQUEST[ 'thepost' ] ) : '';
		$block_id 		= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
		$key 	        = isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : 1;

		if ( absint( $thepost ) > 0 ) {
			$thearticle = get_post( $thepost );
		} else {

			// Remove cache.
			delete_transient( 'ngl_' . md5( untrailingslashit( $thepost ) ) );

			$thearticle = $this->get_remote_url( $thepost );

		}

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		$post_id = untrailingslashit( $thepost );

		if ( ! empty( $custom_data ) && ! empty( $custom_data[ $post_id ] ) ) {
			unset( $custom_data[ $post_id ] );
			update_option( 'newsletterglue_article_custom_data', $custom_data );
		}

		$embed = array(
			'post_id' 	=> $thearticle->ID,
			'favicon'   => $this->get_favicon( $thearticle ),
		);

		if ( ! empty( $thearticle->is_remote ) ) {
			foreach( $thearticle as $remote_key => $remote_value ) {
				$embed[ $remote_key ] = $remote_value;
			}
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		// Generate html for item.
		$refresh_icon    = '<a href="#" class="ngl-article-list-refresh"><i class="sync icon"></i>' . __( 'Refresh', 'newsletter-glue' ) . '</a>';

		$result = array(
			'key'				=> $key,
			'block_id'			=> $block_id,
			'thepost'			=> $thepost,
			'post'				=> $thearticle,
			'post_id'			=> $thearticle->ID,
			'excerpt'			=> $this->display_excerpt( $thearticle->ID, $thecontent ),
			'title'				=> $this->display_title( $thearticle->ID, $thearticle ),
			'permalink'			=> $this->get_permalink( $thearticle ),
			'featured_image'	=> $this->get_image_url( $thearticle ),
			'labels'			=> $this->get_labels( $thearticle->ID ),
			'embed'				=> $embed,
		);

		wp_send_json( $result );

	}

	/**
	 * Get image URL.
	 */
	public function get_image_url( $thearticle ) {

		if ( ! empty( $thearticle->is_remote ) ) {
			$fallback = $thearticle->image_url;
		} else {
			$fallback = $this->get_featured( $thearticle );
		}

		return $this->get_custom_image( $thearticle->ID ) ? esc_url( $this->get_custom_image( $thearticle->ID ) ) : $fallback;

	}

	/**
	 * Get image default URL.
	 */
	public function get_image_default( $thearticle ) {

		if ( ! empty( $thearticle->is_remote ) ) {
			$fallback = $thearticle->image_url;
		} else {
			$fallback = $this->get_featured( $thearticle );
		}

		return $fallback;

	}

	/**
	 * Tableize.
	 */
	public function tableize( $content, $attributes = array() ) {

		$gap = false;
		if ( newsletterglue_get_theme_option( 'email_bg' ) != newsletterglue_get_theme_option( 'container_bg' ) ) {
			$gap = true;
		}

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		$table_ratio 	= isset( $attributes[ 'table_ratio' ] ) ? $attributes[ 'table_ratio' ] : 'full';
		$image_position = isset( $attributes[ 'image_position' ] ) ? $attributes[ 'image_position' ] : 'left';

		$width = 'auto';

		$img = '.ngl-article-featured img';
		foreach( $output->find( $img ) as $a => $b ) {
			$b->width = $gap ? 560 : 580;
			$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
		}

		// Left side.
		$replace = 'div.ngl-article.ngl-article-img-left > .ngl-article-left, div.ngl-article.ngl-article-img-right > .ngl-article-left';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $table_ratio == '30_70' ) {
				$width = '30%';
				$img_size = $gap ? 154 : 160;
			}
			if ( $table_ratio == '70_30' ) {
				$width = '70%';
				$img_size = $gap ? 386 : 400;
			}
			if ( $table_ratio == '50_50' ) {
				$width = '50%';
				$img_size = $gap ? 270 : 280;
			}
			$img = '.ngl-article-featured img';
			foreach( $output->find( $img ) as $a => $b ) {
				$b->width = $img_size;
				$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
			}
			$output->find( $replace, $key )->outertext = '<td style="width: ' . $width . '; vertical-align: top; font-size: inherit !important;" valign="top" class="ngl-td-clean">' . $element->innertext . '</td>';
		}

		// Right side.
		$replace = 'div.ngl-article.ngl-article-img-left > .ngl-article-right, div.ngl-article.ngl-article-img-right > .ngl-article-right';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $table_ratio == '30_70' ) {
				$width = '70%';
				$img_size = $gap ? 386 : 400;
			}
			if ( $table_ratio == '70_30' ) {
				$width = '30%';
				$img_size = $gap ? 154 : 160;
			}
			if ( $table_ratio == '50_50' ) {
				$width = '50%';
				$img_size = $gap ? 270 : 280;
			}
			$img = '.ngl-article-featured img';
			foreach( $output->find( $img ) as $a => $b ) {
				$b->width = $img_size;
				$b->style = $b->style . 'display: block; max-width: 100%; min-width: 50px; width: 100%;';
			}
			$output->find( $replace, $key )->outertext = '<td style="width: ' . $width . ';vertical-align: top;" valign="top" class="ngl-td-clean">' . $element->innertext . '</td>';
		}

		// Left and Right article wrappers.
		$replace = 'div.ngl-article.ngl-article-img-left, div.ngl-article.ngl-article-img-right';
		foreach( $output->find( $replace ) as $key => $element ) {
			$output->find( $replace, $key )->innertext = '<table class="ngl-table-clean ngl-table-article" border="0" width="100%" cellpadding="10" cellspacing="0" style="mso-table-lspace:0;mso-table-rspace:0; font-size: inherit !important;table-layout: fixed;"><tr>' . $element->innertext . '</tr></table>';
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Article;
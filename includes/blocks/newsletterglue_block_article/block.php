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
			add_action( 'init', array( $this, 'register_block' ) );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			// Ajax hooks.
			add_action( 'wp_ajax_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_add_article', array( $this, 'embed_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_excerpt', array( $this, 'update_excerpt' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_update_title', array( $this, 'update_title' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_search_articles', array( $this, 'search_articles' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_article', array( $this, 'remove_article' ) );

			add_action( 'wp_ajax_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_order_articles', array( $this, 'order_articles' ) );

			add_filter( 'newsletterglue_article_embed_content', array( $this, 'remove_div' ), 50, 2 );
		}

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
				'show_tags'		=> array(
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
		$show_tags   		= isset( $attributes[ 'show_tags' ] ) ? $attributes[ 'show_tags' ] : '';
		$show_date   		= isset( $attributes[ 'show_date' ] ) ? $attributes[ 'show_date' ] : '';
		$show_image   		= isset( $attributes[ 'show_image' ] ) ? $attributes[ 'show_image' ] : '';
		$image_radius   	= isset( $attributes[ 'image_radius' ] ) ? $attributes[ 'image_radius' ] : 0;
		$border_radius   	= isset( $attributes[ 'border_radius' ] ) ? $attributes[ 'border_radius' ] : 0;
		$border_size   		= isset( $attributes[ 'border_size' ] ) ? $attributes[ 'border_size' ] : '';
		$border_style   	= isset( $attributes[ 'border_style' ] ) ? $attributes[ 'border_style' ] : 'solid';
		$border_color   	= isset( $attributes[ 'border_color' ] ) ? $attributes[ 'border_color' ] : 'transparent';
		$background_color   = isset( $attributes[ 'background_color' ] ) ? $attributes[ 'background_color' ] : 'transparent';
		$text_color   		= isset( $attributes[ 'text_color' ] ) ? $attributes[ 'text_color' ] : '';
		$link_color   		= isset( $attributes[ 'link_color' ] ) ? $attributes[ 'link_color' ] : '';
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

		include_once NGL_PLUGIN_DIR . 'includes/blocks/' . $this->id . '/templates/embed.php';

		return ob_get_clean();

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
	margin-top: 20px;
	margin-bottom: 20px;
}

.ngl-article img {
	display: inline-block;
	overflow: hidden;
}

.ngl-article {
	margin: 0 0 30px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
}

.ngl-article-title {
	margin: 0 0 4px;
	line-height: 1.4;
}

.ngl-article-title a {
	font-weight: bold;
}

.ngl-article-featured {
	margin: 0 0 14px;
}

.ngl-article-featured a {
	display: inline-block;
}

.ngl-article-featured img {
	margin-bottom: 0;
}

.ngl-article-date {
	margin: 8px 0 0;
	font-size: 13px;
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

.ngl-article-tags {
	display: block;
	margin: 0 0 6px;
}

.ngl-article-tag {
	display: inline-block;
    margin: 0 10px 0 0;
    border-radius: 999px;
	font-size: 13px;
	opacity: 0.8;
}

.ngl-articles-add {
	width: 100%;
}

#template_body td table.ngl-articles-table {
	border: none;
}

#template_body td table.ngl-articles-table th,
#template_body td table.ngl-articles-table td {
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

.ngl-article-left { display: inline-block; width: 49.5%; vertical-align: top; box-sizing: border-box; }
.ngl-article-right { display: inline-block; width: 49.5%; vertical-align: top; padding-left: 20px; box-sizing: border-box; }

.ngl-articles-30_70 .ngl-article-left { display: inline-block; width: 30%; vertical-align: top; }
.ngl-articles-30_70 .ngl-article-right { display: inline-block; width: 69%; vertical-align: top; }

.ngl-articles-70_30 .ngl-article-left { display: inline-block; width: 69%; vertical-align: top; }
.ngl-articles-70_30 .ngl-article-right { display: inline-block; width: 30%; vertical-align: top; }

.ngl-article-right .ngl-article-featured { text-align: right; }
.ngl-article-left .ngl-article-featured { text-align: right; }

.ngl-article-left-mobile {
	display: none !important;
}

@media only screen and (max-width:596px) {

	.ngl-article-left-mobile {
		display: block !important;
	}

	.ngl-article-left,
	.ngl-article-right {
		width: 100% !important;
		margin: 0 !important;
	}

	.ngl-article-img-right .ngl-article-right {
		display: none !important;
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
	 * Update excerpt.
	 */
	public function update_excerpt() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$post_id = isset( $_REQUEST[ 'post_id' ] ) ? sanitize_text_field( $_REQUEST[ 'post_id' ] ) : '';
		$excerpt = isset( $_REQUEST[ 'excerpt' ] ) ? sanitize_text_field( $_REQUEST[ 'excerpt' ] ) : '';

		$custom_data = get_option( 'newsletterglue_article_custom_data' );

		if ( empty( $custom_data ) ) {
			$custom_data = array();
		}

		$custom_data[ $post_id ][ 'excerpt' ] = $excerpt;

		update_option( 'newsletterglue_article_custom_data', $custom_data );

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

		$post_tags 		= wp_get_post_tags( $thearticle->ID );
		$display_tags 	= '';

		if ( $post_tags ) {
			$display_tags = '<div class="ngl-article-tags">';
			foreach( $post_tags as $tag ) {
				$display_tags .= '<div class="ngl-article-tag">' . $tag->name . '</div>';
			}
			$display_tags .= '</div>';
		}

		if ( ! empty( $thearticle->is_remote ) ) {
			$featured_image  = $thearticle->image_url;
		} else {
			$featured_image  = ( has_post_thumbnail( $thearticle->ID ) ) ? wp_get_attachment_url( get_post_thumbnail_id( $thearticle->ID ), 'full' ) : $this->default_image_url();
		}

		$thecontent = apply_filters( 'newsletterglue_article_embed_content', apply_filters( 'the_content', $thearticle->post_content ), $thearticle->ID );

		// Generate html for item.
		$item = '<div class="ngl-article-list-item" data-key="' . $key . '" data-post-id="' . $thearticle->ID . '">
						<div class="ngl-article-list-icon"><img src="' . $this->get_favicon( $thearticle ) . '" /></div>
						<div class="ngl-article-list-info">
							<div class="ngl-article-list-title">' . $this->display_title( $thearticle->ID, $thearticle ) . '</div>
							<div class="ngl-article-list-url">' . $this->get_permalink( $thearticle ) . '</div>
							<div class="ngl-article-list-action">
								<a href="#" class="ngl-article-list-red"><i class="trash alternate outline icon"></i>' . __( 'Remove post', 'newsletter-glue' ) . '</a>
							</div>
						</div>
						<div class="ngl-article-list-move">
							<div class="ngl-article-list-move-up"><a href="#"><span class="material-icons">expand_less</span></a></div>
							<div class="ngl-article-list-move-down"><a href="#"><span class="material-icons">expand_more</span></a></div>
						</div>
					</div>';

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
			'tags'				=> $display_tags,
			'featured_image'	=> $featured_image,
			'item'				=> $item,
			'embed'				=> $embed,
			'success'			=> __( 'Add another post', 'newsletter-glue' ),
		);

		wp_send_json( $result );

	}

}

return new NGL_Block_Article;
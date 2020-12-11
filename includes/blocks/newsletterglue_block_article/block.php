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
		}

	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Article embeds', 'newsletter-glue' );
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

		$defaults[ 'name' ]			= __( 'NG: Article embeds', 'newsletter-glue' );
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
			'style'           => $this->asset_id . '-style',
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
				'border_radius'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
				),
				'border_size'	=> array(
					'type'		=> 'number',
					'default'	=> 0,
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
		$new_window   		= ! empty( $attributes[ 'new_window' ] ) ? '_blank' : '_self';
		$nofollow   		= ! empty( $attributes[ 'nofollow' ] ) ? 'nofollow' : '';

		update_option( 'ngl_articles_' . $block_id, array( 1598, 1601, 1604 ) );
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
	min-height: 100px;
	margin: 20px 0;
}

.ngl-article img {
	display: block;
	overflow: hidden;
}

.ngl-article {
	font-size: 14px;
	margin: 0 0 30px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
}

.ngl-article-title {
	margin: 0 0 4px;
}

.ngl-article-title a {
	font-size: 18px;
	font-weight: bold;
}

.ngl-article-featured {
	margin: 0 0 14px;
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

.ngl-article-left { display: inline-block; width: 49%; vertical-align: top; }
.ngl-article-right { display: inline-block; width: 48%; vertical-align: top; margin-left: 2%; }

.ngl-articles-30_70 .ngl-article-left { display: inline-block; width: 30%; vertical-align: top; }
.ngl-articles-30_70 .ngl-article-right { display: inline-block; width: 66%; vertical-align: top; }

.ngl-articles-70_30 .ngl-article-left { display: inline-block; width: 66%; vertical-align: top; }
.ngl-articles-70_30 .ngl-article-right { display: inline-block; width: 30%; vertical-align: top; }

	<?php
	}

	/**
	 * Get date formats.
	 */
	public function get_date_formats() {
		return array( 'd M Y', 'l, j M Y', 'F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y' );
	}

	/**
	 * Get default date format.
	 */
	public function get_default_date_format() {
		$formats = $this->get_date_formats();

		return $formats[ 0 ];
	}

}

return new NGL_Block_Article;
<?php
/**
 * Gutenberg.
 */

class NGL_Block_Article {

	public $id = 'newsletterglue_block_article';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		add_action( 'init', array( $this, 'register_block' ) );

		add_action( 'newsletterglue_add_custom_styles', array( $this, 'email_css' ) );
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
		$dates = array(
			date( 'l, j M Y', current_time( 'timestamp' ) ),
			date( 'F j, Y', current_time( 'timestamp' ) ),
			date( 'Y-m-d', current_time( 'timestamp' ) ),
			date( 'm/d/Y', current_time( 'timestamp' ) ),
			date( 'd/m/Y', current_time( 'timestamp' ) ),
		);

		$date_formats = array();
		foreach( $dates as $date ) {
			$date_formats[] = array( 'value' => $date, 'label' => $date );
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
				'border_color' => array(
					'type' 		=> 'string',
				),
				'background_color' => array(
					'type' 		=> 'string',
				),
				'border_style' => array(
					'type' 		=> 'string',
				),
				'border_size' => array(
					'type' 		=> 'number',
					'default' 	=> 0,
				),
				'border_radius' => array(
					'type' 		=> 'number',
					'default'	=> 0,
				),
				'show_image' 	=> array(
					'type' 		=> 'boolean',
					'default' 	=> true,
				),
				'show_date' 	=> array(
					'type' 		=> 'boolean',
					'default' 	=> true,
				),
				'show_tags' 	=> array(
					'type' 		=> 'boolean',
					'default' 	=> true,
				),
				'image_radius' 	=> array(
					'type' 		=> 'number',
					'default'	=> 0,
				),
				'date_format'	=> array(
					'type'		=> 'string',
				),
				'new_window' 	=> array(
					'type' 		=> 'boolean',
					'default' 	=> false,
				),
				'nofollow' 		=> array(
					'type' 		=> 'boolean',
					'default' 	=> false,
				),
				'image_position' => array(
					'type'		=> 'string',
					'default'	=> 'left',
				),
				'table_ratio'	 => array(
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

		echo '<div class="newsletterglue-articles">output here</div>';

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

	}

}

return new NGL_Block_Article;
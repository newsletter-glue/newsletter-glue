<?php
/**
 * Gutenberg.
 */

class NGL_Block_Metadata {

	public $id = 'newsletterglue_block_metadata';

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

		$defaults[ 'assets_uri' ] 	=  NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/';
		$defaults[ 'readtime' ]		= __( 'Reading time:', 'newsletter-glue' );
		$defaults[ 'read_online' ]  = __( 'Read online', 'newsletter-glue' );

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/metadata', array(
			'editor_script'   => $this->asset_id,
			'style'           => $this->asset_id . '-style',
			'render_callback' => array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

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
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
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
		.ngl-metadata {
			font-size: 12px;
			padding: 20px 0;
		}

		.ngl-metadata > div {
			padding: 0 2px;
			display: inline-block;
			vertical-align: middle;
		}

		.ngl-metadata .ngl-metadata-sep {
			text-align: center;
			width: 14px;
			color: #aaa;
		}

		.ngl-metadata-map-pin {
			width: 12px;
			display: inline-block;
			margin: 0 4px 0 0 !important;
			position: relative;
			top: 3px;
		}

		.ngl-metadata-permalink-arrow {
			width: 10px;
			display: inline-block;
			margin: 0 0 0 4px !important;
			position: relative;
			top: 2px;
		}

		.ngl-metadata-permalink {
			text-decoration: underline !important;
			cursor: pointer;
		}

		.ngl-metadata-pic {
			width: 36px;
			height: 36px;
		}

		.ngl-metadata-pic img {
			width: 30px;
			height: 30px;
			border-radius: 999px;
			margin: 0 6px 0 0;
			position: relative;
			top: 2px;
		}
		<?php
	}

}

return new NGL_Block_Metadata;
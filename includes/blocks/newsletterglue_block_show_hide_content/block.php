<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Show_Hide_Content extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_show_hide_content';

	public $is_pro = false;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ) );
			add_action( 'newsletterglue_add_custom_styles', array( $this, 'email_css' ) );
		}

	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Show/hide content', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Hide selected content from your blog/newsletter.', 'newsletter-glue' );
	}

	/**
	 * Get defaults.
	 */
	public function get_defaults() {

		return array(
			'showemail'	=> true,
			'showblog'	=> false,
		);

	}

	/**
	 * Register the block.
	 */
	public function register_block() {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'showemail'	=> true,
				'showblog'	=> false,
			);
		}

		$js_dir    	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/js/';
		$css_dir   	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/css/';

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id, $css_dir . 'block' . $suffix . '.css', array(), time() );
		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/group', array(
			'attributes'		=> array(
				'showblog'	=> array(
					'type'	=> 'boolean',
				),
				'showemail' => array(
					'type'	=> 'boolean'
				),
			),
			'editor_script' 	=> $this->asset_id,
			'editor_style'  	=> $this->asset_id,
			'style'         	=> $this->asset_id . '-style',
			'render_callback'	=> array( $this, 'render_block' ),
		) );

	}

	/**
	 * Render the block.
	 */
	public function render_block( $attributes, $content ) {

		$defaults = get_option( $this->id );

		if ( ! $defaults ) {
			$defaults = array(
				'showemail'	=> true,
				'showblog'	=> false,
			);
		}

		$show_in_blog  = isset( $attributes[ 'showblog' ] ) ? $attributes[ 'showblog' ] : $defaults[ 'showblog' ];
		$show_in_email = isset( $attributes[ 'showemail' ] ) ? $attributes[ 'showemail' ] : $defaults[ 'showemail' ];

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
		}

		if ( defined( 'NGL_IN_EMAIL' ) ) {
			$content = str_replace( '<section', '<div', $content );
			$content = str_replace( '/section>', '/div>', $content );
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

		if ( isset( $_POST[ 'showemail' ] ) ) {
			$defaults[ 'showemail' ] = true;
		} else {
			$defaults[ 'showemail' ] = false;
		}

		if ( isset( $_POST[ 'showblog' ] ) ) {
			$defaults[ 'showblog' ] = true;
		} else {
			$defaults[ 'showblog' ] = false;
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

return new NGL_Block_Show_Hide_Content;
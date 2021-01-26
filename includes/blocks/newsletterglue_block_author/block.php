<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Author extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_author';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ), 10 );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			add_filter( 'newsletterglue_article_embed_content', array( $this, 'remove_div' ), 50, 2 );
		}

	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Author byline', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add an author byline and follow button to your newsletter.', 'newsletter-glue' );
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

		$defaults[ 'assets_uri' ] 	= NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/';
		$defaults[ 'name' ]			= __( 'NG: Author byline', 'newsletter-glue' );
		$defaults[ 'description' ] 	= __( 'Add an author byline and follow button to your newsletter.', 'newsletter-glue' );
		$defaults[ 'button_text' ]	= __( 'Follow', 'newsletter-glue' );

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/author', array(
			'editor_script'   => $this->asset_id,
			'editor_style'    => $this->asset_id . '-style',
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
		.ngl-author {
			display: flex;
			padding: 20px 0;
			line-height: 1;
		}

		.ngl-author-pic {
			width: 50px;
			min-width: 50px;
			margin: 0 12px 0 0;
		}

		.ngl-author-pic img {
			margin: 0 !important;
			display: block;
			overflow: hidden;
			border-radius: 999px;
		}

		.ngl-author-name {
			font-weight: bold;
			min-width: 20px;
			padding: 0 0 4px;
			font-size: 14px;
			line-height: 14px;
		}

		.ngl-author-bio {
			margin: 0;
			padding: 0 0 4px;
			font-size: 14px;
			line-height: 18px;
		}

		.ngl-author-btn {
			color: #fff !important;
			text-decoration: none !important;
			padding: 4px 8px;
			display: inline-flex;
			align-items: center;
			border: 2px solid transparent;
			font-size: 12px;
			line-height: 16px;
		}

		.ngl-author-btn:hover {
			color: #fff !important;
		}

		.ngl-author-btn-text {
			min-width: 20px;
		}

		.ngl-author-cta img {
			display: inline-block;
			width: 16px !important;
			height: 16px !important;
			margin: 0 4px 0 0 !important;
		}

		.ngl-author-cta .ngl-author-btn-outlined {
			background-color: transparent !important;
			border: 2px solid transparent;
			color: #444 !important;
		}

		.ngl-author-twitter { background-color: #1DA1F2; }
		.ngl-author-instagram { background-color: #ed4956; }
		.ngl-author-facebook { background-color: #1877F2; }
		.ngl-author-twitch { background-color: #9047FF; }
		.ngl-author-tiktok { background-color: #fe2c55; }
		.ngl-author-youtube { background-color: #FF0000; }

		.ngl-author-btn-outlined.ngl-author-twitter { border-color: #1DA1F2 !important; }
		.ngl-author-btn-outlined.ngl-author-instagram { border-color: #ed4956 !important; }
		.ngl-author-btn-outlined.ngl-author-facebook { border-color: #1877F2 !important; }
		.ngl-author-btn-outlined.ngl-author-twitch { border-color: #9047FF !important; }
		.ngl-author-btn-outlined.ngl-author-tiktok { border-color: #fe2c55 !important; }
		.ngl-author-btn-outlined.ngl-author-youtube { border-color: #FF0000 !important; }
		<?php
	}

	/**
	 * Remove this block div from article embeds.
	 */
	public function remove_div( $content, $post_id ) {
		$content = newsletterglue_remove_div( $content, 'ngl-author' );

		return $content;
	}

}

return new NGL_Block_Author;
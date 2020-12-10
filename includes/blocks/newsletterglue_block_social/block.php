<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Social extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_social';

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->asset_id = str_replace( '_', '-', $this->id );

		if ( $this->use_block() === 'yes' ) {
			add_action( 'init', array( $this, 'register_block' ) );
			add_action( 'newsletterglue_add_block_styles', array( $this, 'email_css' ) );

			// Ajax hooks.
			add_action( 'wp_ajax_newsletterglue_ajax_get_embed', array( $this, 'ajax_get_embed' ) );
			add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_embed', array( $this, 'ajax_get_embed' ) );
		}

	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Social embed', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Embed posts from social media by pasting a link.', 'newsletter-glue' );
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

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/social', array(
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

		$supported_embeds = array(
			'twitter' => array(
				'icon'	=> '<svg viewBox="0 0 32 32"><path d="M2 4c4 4 8 8 13 7a6 6 0 0 1 7-7 6 6 0 0 1 4 2 8 8 0 0 0 5-2 8 8 0 0 1-3 4 8 8 0 0 0 4-1 8 8 0 0 1-4 4 18 18 0 0 1-18 19 18 18 0 0 1-10-3 12 12 0 0 0 8-3 8 8 0 0 1-5-4 8 8 0 0 0 3-.5A8 8 0 0 1 0 12a8 8 0 0 0 3 1 8 8 0 0 1-1-9"/></svg>'
			),
		);

		$block_id 	= isset( $attributes[ 'block_id' ] ) ? $attributes[ 'block_id' ] : '';
		$url		= $this->get_embed_url( $block_id );
		$content    = $this->get_platform_embed( $url );
		$html		= null;

		if ( ! empty( $content[ 'html' ] ) ) {
			$html = $content[ 'html' ];
		}

		// On frontend. no html.
		if ( ! is_admin() && ! defined( 'REST_REQUEST' ) && ! $html ) {
			return ob_get_clean();
		}

		if ( ! empty( $content[ 'error' ] ) ) {
			$html = $content[ 'error' ];
		}

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

	}

	/**
	 * Get embed with AJAX.
	 */
	public function ajax_get_embed() {

		$error = '';

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		$url 		= isset( $_REQUEST[ 'url' ] ) ? sanitize_text_field( $_REQUEST[ 'url' ] ) : '';
		$block_id 	= isset( $_REQUEST[ 'block_id' ] ) ? sanitize_text_field( $_REQUEST[ 'block_id' ] ) : '';
		$block_id	= str_replace( '-', '_', $block_id );

		if ( empty( $block_id ) ) {
			wp_die( -1 );
		}

		if ( empty( $url ) ) {
			$error = __( 'Please enter URL first.', 'newsletter-glue' );
		} else if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE || esc_url_raw( $url ) !== $url ) {
			$error = __( 'Please enter a valid URL.', 'newsletter-glue' );
		}

		if ( ! empty( $error ) ) {
			delete_option( 'ngl_embed_' . $block_id );
			delete_option( 'ngl_embed_url_' . $block_id );
			delete_option( 'ngl_embed_data_' . $block_id );

			wp_send_json( array( 'error' => $error ) );
		}

		$result = $this->get_platform_embed( $url );

		if ( ! empty( $result[ 'html' ] ) ) {
			update_option( 'ngl_embed_' . $block_id, $result[ 'html' ] );
			update_option( 'ngl_embed_url_' . $block_id, $url );
			update_option( 'ngl_embed_data_' . $block_id, $result );
		}

		if ( ! $result ) {
			$result[ 'error' ] = __( 'An error has occured.', 'newsletter-glue' );
		}

		if ( isset( $result[ 'html' ] ) && empty( $result[ 'html' ] ) ) {
			$result[ 'error' ] = __( 'We could not retrieve any content from the provided URL.', 'newsletter-glue' );
		}

		wp_send_json( $result );

	}

	/**
	 * Get platform embed by URL.
	 */
	public function get_platform_embed( $url ) {

		$result = false;

		// Twitter embed.
		if ( strstr( $url, 'twitter' ) ) {
			$result = $this->get_twitter_embed( $url );
		}

		return $result;

	}

	/**
	 * Get embed by block ID.
	 */
	public function get_embed( $block_id ) {

		$html = get_option( 'ngl_embed_' . str_replace( '-', '_', $block_id ) );

		return $html;

	}

	/**
	 * Get embed URL by block ID.
	 */
	public function get_embed_url( $block_id ) {

		$url = get_option( 'ngl_embed_url_' . str_replace( '-', '_', $block_id ) );

		return $url;

	}

	/**
	 * Twitter.
	 */
	public function get_twitter_embed( $url ) {

		$response 	= array();

		$request 	= wp_remote_get( 'https://publish.twitter.com/oembed?url=' . urlencode( $url ) . '&omit_script=false' );

		if ( is_wp_error( $request ) ) {
			$response[ 'error' ] = __( 'An error has occured.', 'newsletter-glue' );
			return $response;
		}

		$result 	= json_decode( $request[ 'body' ] );
		$html   	= isset( $result->html ) ? $result->html : '';

		$response[ 'html' ] = $html;
		$response[ 'data' ] = $result;

		return $response;

	}

}

return new NGL_Block_Social;
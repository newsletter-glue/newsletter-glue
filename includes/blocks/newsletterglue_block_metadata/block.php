<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Metadata extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_metadata';

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
	 * Block icon.
	 */
	public function get_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31.5 31.5" class="ngl-block-svg-icon">
			<g transform="translate(-115 -126)">
				<path d="M30.984,12.8l.5-2.812A.844.844,0,0,0,30.656,9H25.4l1.028-5.758a.844.844,0,0,0-.831-.992H22.737a.844.844,0,0,0-.831.7L20.825,9H13.89l1.028-5.758a.844.844,0,0,0-.831-.992H11.23a.844.844,0,0,0-.831.7L9.318,9H3.757a.844.844,0,0,0-.831.7l-.5,2.813a.844.844,0,0,0,.831.992h5.26l-1.607,9H1.346a.844.844,0,0,0-.831.7l-.5,2.813A.844.844,0,0,0,.844,27H6.1L5.076,32.758a.844.844,0,0,0,.831.992H8.763a.844.844,0,0,0,.831-.7L10.675,27H17.61l-1.028,5.758a.844.844,0,0,0,.831.992H20.27a.844.844,0,0,0,.831-.7L22.182,27h5.561a.844.844,0,0,0,.831-.7l.5-2.813a.844.844,0,0,0-.831-.992h-5.26l1.607-9h5.561a.844.844,0,0,0,.831-.7Zm-12.57,9.7H11.479l1.607-9h6.935Z" transform="translate(115 123.75)"/>
			</g>
		</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Newsletter meta data', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add standard meta data to each post.', 'newsletter-glue' );
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

		$defaults[ 'assets_uri' ] 	=  NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/img/';
		$defaults[ 'readtime' ]		= __( 'Reading time:', 'newsletter-glue' );
		$defaults[ 'issue_title' ]  = __( 'Issue #', 'newsletter-glue' );
		$defaults[ 'read_online' ]  = __( 'Read online', 'newsletter-glue' );

		$suffix  = '';

		wp_register_script( $this->asset_id, $js_dir . 'block' . $suffix . '.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), time() );
		wp_localize_script( $this->asset_id, $this->id, $defaults );

		wp_register_style( $this->asset_id . '-style', $css_dir . 'block-ui' . $suffix . '.css', array(), time() );

		register_block_type( 'newsletterglue/metadata', array(
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
		$post_id	   = isset( $attributes[ 'post_id' ] ) ? absint( $attributes[ 'post_id' ] ) : '';
		$post 			= get_post( $post_id );

		// Hidden from blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) && ! $show_in_blog ) {
			$content = '';
		}

		// Hidden from email.
		if ( defined( 'NGL_IN_EMAIL' ) && ! $show_in_email ) {
			$content = '';
		}

		$post_permalink = get_permalink( $post_id );

		$content = str_replace( '{post_permalink}', $post_permalink, $content );
		$content = str_replace( '{post_permalink_preview}', newsletterglue_generate_web_link( $post_id ), $content );

		// Only in blog.
		if ( ! defined( 'NGL_IN_EMAIL' ) ) {
			$content = preg_replace( '~<a([^>]*)(class\\s*=\\s*["\']ngl-metadata-permalink["\'])([^>]*)>(.*?)</a>~i', '', $content );
			$content = preg_replace( '~<img([^>]*)(class\\s*=\\s*["\']ngl-metadata-permalink-arrow["\'])([^>]*)>(.*?)~i', '', $content );
		}

		$content = str_replace( '<div class="ngl-metadata-sep">|</div></div>', '</div>', $content );

		// Recalculate reading time.
		if ( strstr( $content, 'ngl-metadata-readtime-ajax' ) ) {
			$read_time 	= newsletterglue_content_estimated_reading_time( $post->post_content );
			$content 	= preg_replace( '/<div class=\"ngl-metadata-readtime-ajax\">[^<]+<\/div>/', '<div class="ngl-metadata-readtime-ajax">' . $read_time . '</div>', $content );
		}

		if ( defined( 'NGL_IN_EMAIL' ) && $content ) {
			$content = $this->tableize( $content );
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
			padding: 0;
			margin: 0;
			min-height: 50px;
		}
		
		.ngl-metadata img {
			display: inline-block !important;
			margin: 0 !important;
		}

		.ngl-metadata img.ngl-metadata-permalink-arrow {
			width: 10px !important;
			height: 10px !important;
			margin: 0 0 0 4px !important;
		}

		.ngl-metadata img.avatar {
			vertical-align: middle !important;
			width: 30px !important;
			height: 30px !important;
			border-radius: 999px;
			margin: 0 !important;
		}

		img.ngl-metadata-map-pin {
			width: 12px !important;
			height: 14px !important;
			margin: 0 !important;
		}

		.ngl-metadata > div {
			padding: 0 2px;
			display: inline-block;
			vertical-align: middle;
		}

		.ngl-metadata .ngl-metadata-sep {
			text-align: center;
			width: 2px;
			color: #aaa;
		}

		.ngl-metadata-permalink-arrow {
			width: 10px !important;
			display: inline-block !important;
			margin: 0 0 0 4px !important;
			position: relative !important;
			top: 2px !important;
		}

		.ngl-metadata-permalink {
			text-decoration: underline !important;
			cursor: pointer;
		}

		.ngl-metadata-pic {
			width: 30px;
			height: 30px;
		}

		.ngl-metadata-pic img {
			width: 30px;
			height: 30px;
			border-radius: 999px;
			margin: 0 6px 0 0;
			position: relative;
			display: inline-block !important;
		}

		.ngl-metadata .ngl-metadata-readtime,
		.ngl-metadata .ngl-metadata-pic {
			padding-right: 0;
		}
		.ngl-metadata .ngl-metadata-readtime-ajax,
		.ngl-metadata .ngl-metadata-pic {
			padding-left: 0;
		}
		<?php
	}

	/**
	 * Remove this block div from article embeds.
	 */
	public function remove_div( $content, $post_id ) {
		$content = newsletterglue_remove_div( $content, 'ngl-metadata' );

		return $content;
	}

	/**
	 * Tableize.
	 */
	public function tableize( $content ) {

		$output = new simple_html_dom();
		$output->load( $content, true, false );

		// Force image width/height attributes.
		$replace = 'div.wp-block-newsletterglue-metadata .ngl-metadata-pic img';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->width 	= 30;
			$element->height 	= 30;
		}

		$replace = 'div.wp-block-newsletterglue-metadata .ngl-metadata-permalink-arrow, .ngl-metadata-permalink-arrow';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->width 	= 10;
			$element->height 	= 10;
		}

		$replace = '.wp-block-newsletterglue-metadata > .ngl-metadata-map-pin';
		foreach( $output->find( $replace ) as $key => $element ) {
			$element->width 	= 12;
			$element->height 	= 14;
		}

		// Inner divs.
		$replace = '.wp-block-newsletterglue-metadata > div';
		$i = 0;
		foreach( $output->find( $replace ) as $key => $element ) {
			$i++;
			if ( $i == 1 ) {
				$output->find( $replace, $key )->outertext = $element->innertext . '&nbsp;';
			} else {
				$output->find( $replace, $key )->outertext = '&nbsp;' . $element->innertext . '&nbsp;';
			}
		}

		// Look for each metadata block. and put it in table.
		$replace = '.wp-block-newsletterglue-metadata';
		foreach( $output->find( $replace ) as $key => $element ) {

			$align = 'left';

			// Has style.
			if ( $output->find( $replace, $key )->style ) {
				$s = $output->find( $replace, $key )->style;
				$results = [];
				$styles = explode(';', $s);

				foreach ($styles as $style) {
					$properties = explode(':', $style);
					if (2 === count($properties)) {
						$results[trim($properties[0])] = trim($properties[1]);
					}
				}
				if ( isset( $results[ 'text-align' ] ) ) {
					$align = $results[ 'text-align' ];
				}
			}

			$output->find( $replace, $key )->innertext = $output->find( $replace, $key )->innertext = '<table class="ngl-table-tiny" align="' . $align . '" border="0" width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0; width:100%;"><tr><td class="ngl-td-tiny" valign="middle">' . $element->innertext . '</td></tr></table>';
		}

		$output->save();

		return ( string ) $output;

	}

}

return new NGL_Block_Metadata;
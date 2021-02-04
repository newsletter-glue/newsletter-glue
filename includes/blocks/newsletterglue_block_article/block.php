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

}

return new NGL_Block_Article;
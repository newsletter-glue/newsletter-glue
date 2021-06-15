<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Share extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_share';

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
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 111.001 111.001" class="ngl-block-svg-icon">
					<path class="a" d="M-14-283v-99.9A11.115,11.115,0,0,1-2.9-394H85.9A11.115,11.115,0,0,1,97-382.9v66.6a11.113,11.113,0,0,1-11.1,11.1H8.2l-22.194,22.194Zm41.742-92.5a14.333,14.333,0,0,0-9.425,3.443,15.85,15.85,0,0,0-5.568,11.422A16.113,16.113,0,0,0,17.2-348.693l21.745,22.454a3.524,3.524,0,0,0,2.546,1.086,3.524,3.524,0,0,0,2.546-1.086l21.745-22.454a16.11,16.11,0,0,0,4.466-11.942,15.84,15.84,0,0,0-5.565-11.421,14.337,14.337,0,0,0-9.423-3.443,16.213,16.213,0,0,0-11.549,4.971L41.5-368.246l-2.211-2.281A16.192,16.192,0,0,0,27.742-375.5Z" transform="translate(14 394)"/>
				</svg>';
	}

	/**
	 * Block label.
	 */
	public function get_label() {
		return __( 'Social sharing', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'Add social sharing links to your newsletter.', 'newsletter-glue' );
	}

}

return new NGL_Block_Share;
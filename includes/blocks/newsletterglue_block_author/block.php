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

}

return new NGL_Block_Author;
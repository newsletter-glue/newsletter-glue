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

}

return new NGL_Block_Metadata;
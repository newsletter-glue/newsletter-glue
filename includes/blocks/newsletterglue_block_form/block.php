<?php
/**
 * Gutenberg.
 */

if ( ! class_exists( 'NGL_Abstract_Block', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-block.php';
}

class NGL_Block_Form extends NGL_Abstract_Block {

	public $id = 'newsletterglue_block_form';

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
		return __( 'Subscriber form', 'newsletter-glue' );
	}

	/**
	 * Block label.
	 */
	public function get_description() {
		return __( 'New subscribers can sign up to your mailing list with this form.', 'newsletter-glue' );
	}

}

return new NGL_Block_Form;
<?php
/**
 * Abstract Block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Abstract_Block class.
 */
abstract class NGL_Abstract_Block {

	public $id = '';

	public $is_pro = true;

	/**
	 * Load block settings.
	 */
	public function load_settings() {

		$block_id 	= $this->id;
		$file_url 	= NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/settings.php';
		$include 	= apply_filters( 'newsletterglue_include_block_settings', $file_url, $block_id );
		$include 	= apply_filters( $block_id . '_settings_template', $include );

		if ( file_exists( $include ) ) {
			include_once( $include );
		}

	}

	/**
	 * Load block demo.
	 */
	public function load_demo() {

		$block_id 	= $this->id;
		$file_url 	= NGL_PLUGIN_DIR . 'includes/blocks/' . $block_id . '/demo.php';
		$include 	= apply_filters( 'newsletterglue_include_block_demo', $file_url, $block_id );
		$include 	= apply_filters( $block_id . '_demo_template', $include );

		if ( file_exists( $include ) ) {
			include_once( $include );
		}

	}

	/**
	 * Use block.
	 */
	public function use_block() {

		$use_blocks = get_option( 'newsletterglue_use_blocks' );

		return isset( $use_blocks[ $this->id ] ) ? sanitize_text_field( $use_blocks[ $this->id ] ) : 'no';
	}

	/**
	 * Get icon url.
	 */
	public function get_icon_url() {
		return NGL_PLUGIN_URL . 'includes/blocks/' . $this->id . '/icon/icon.svg';
	}

}
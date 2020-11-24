<?php
/**
 * Pro.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Pro class.
 */
class NGL_Pro {

	public function __construct() {

		$this->includes();

		if ( class_exists( 'NGL_License' ) ) {
			$ngl_license = new NGL_License( 'newsletterglue_founding_member_license', '1.0.0', 1261, 'Founding Member', NGL_PLUGIN_FILE );
		}

	}

	public function includes() {
		require_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
	}

}

return new NGL_Pro;
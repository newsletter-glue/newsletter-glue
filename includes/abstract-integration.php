<?php
/**
 * Form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Abstract_Integration class.
 */
abstract class NGL_Abstract_Integration {

	/**
	 * Display credits setting.
	 */
	public function display_credits_setting() {

		include NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-credits.php';

	}

}
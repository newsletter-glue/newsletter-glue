<?php
/**
 * Core.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * NGL_Integration_Core Class.
 */
class NGL_Integration_Core extends NGL_Abstract_Integration {

}
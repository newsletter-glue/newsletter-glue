<?php
/**
 * ConvertKit.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Convertkit {

	public $api_key = null;
	public $api_secret = null;

	public $api = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';
		include_once 'lib/api.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'convertkit' ] ) ? $integrations[ 'convertkit'] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_secret 	= isset( $integration[ 'api_secret' ] ) ? $integration[ 'api_secret' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_convertkit_key'] ) ? $_POST['ngl_convertkit_key'] : '';
		$api_secret = isset( $_POST['ngl_convertkit_secret'] ) ? $_POST['ngl_convertkit_secret'] : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations = get_option( 'newsletterglue_integrations' );
			$convertkit    = isset( $integrations[ 'convertkit' ] ) ? $integrations[ 'convertkit'] : '';
			if ( isset( $convertkit[ 'api_key'] ) ) {
				$api_key 	= $convertkit[ 'api_key' ];
				$api_secret = $convertkit[ 'api_secret' ];
			}
		}

		$this->api       = new ConvertKit_API( $api_key, $api_secret );

		$account = $this->api->make_request( 'v3/account?api_secret=' . $api_secret, 'get' );

		$valid_account = isset( $account[ 'error' ] ) ? false : true;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_convertkit' );

		} else {

			$this->save_integration( $api_key, $api_secret );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_convertkit', $account );

		}

		return $result;
	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'convertkit' ] ) ) {
			unset( $integrations[ 'convertkit' ] );
		}

		if ( empty( $integrations ) ) {
			delete_option( 'newsletterglue_integrations' );
		} else {
			update_option( 'newsletterglue_integrations', $integrations );
		}
	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_secret = '' ) {
		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'convertkit' ] = array();
		$integrations[ 'convertkit' ][ 'api_key' ] 		= $api_key;
		$integrations[ 'convertkit' ][ 'api_secret' ] 	= $api_secret;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'convertkit' ] ) ? $globals[ 'convertkit' ] : '';

		if ( ! $options ) {

			$globals[ 'convertkit' ] = array(

			);

			update_option( 'newsletterglue_options', $globals );

		}
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new ConvertKit_API( $this->api_key, $this->api_secret );

	}

}
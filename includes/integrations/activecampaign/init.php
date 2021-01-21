<?php
/**
 * ActiveCampaign.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Activecampaign extends NGL_Abstract_Integration {

	public $api_url = null;
	public $api_key = null;

	public $api = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/ActiveCampaign.class.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'activecampaign' ] ) ? $integrations[ 'activecampaign'] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_url		= isset( $integration[ 'api_url' ] ) ? $integration[ 'api_url' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_activecampaign_key'] ) ? sanitize_text_field( $_POST['ngl_activecampaign_key'] ) : '';
		$api_url 	= isset( $_POST['ngl_activecampaign_url'] ) ? sanitize_text_field( $_POST['ngl_activecampaign_url'] ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations 	 = get_option( 'newsletterglue_integrations' );
			$activecampaign  = isset( $integrations[ 'activecampaign' ] ) ? $integrations[ 'activecampaign'] : '';
			if ( isset( $activecampaign[ 'api_key'] ) ) {
				$api_key = $activecampaign[ 'api_key' ];
			}
			if ( isset( $activecampaign[ 'api_url'] ) ) {
				$api_url = $activecampaign[ 'api_url' ];
			}
		}

		$this->api = new ActiveCampaign( $api_url, $api_key );

		$account = $this->api->api( 'account/view' );

		if ( ! isset( $account->email ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_activecampaign' );

		} else {

			$this->save_integration( $api_key, $api_url, ( array ) $account );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_activecampaign', ( array ) $account );

		}

		return $result;
	}


	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'activecampaign' ] ) ) {
			unset( $integrations[ 'activecampaign' ] );
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
	public function save_integration( $api_key = '', $api_url = '', $account = array() ) {
		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'activecampaign' ] = array();
		$integrations[ 'activecampaign' ][ 'api_key' ] 		= $api_key;
		$integrations[ 'activecampaign' ][ 'api_url' ] 		= $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'activecampaign' ] ) ? $globals[ 'activecampaign' ] : '';

		if ( ! $options ) {

			$globals[ 'activecampaign' ] = array(
				'from_name' 	=> newsletterglue_get_default_from_name(),
				'from_email'	=> isset( $account[ 'email' ] ) ? $account[ 'email' ] : '',
			);

			update_option( 'newsletterglue_options', $globals );

		}
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

	}

	/**
	 * Get lists.
	 */
	public function get_lists() {

		$_lists = array();

		$lists = $this->api->api( 'list_/list_', array( 'ids' => 'all', 'full' => 0 ) );

		foreach( $lists as $key => $data ) {
			$array = (array) $data;
			$id = @$array['id'];
			if ( $id ) {
				$_lists[ $id ] = @$array[ 'name' ];
			}
		}

		return $_lists;

	}

}
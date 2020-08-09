<?php
/**
 * ConvertKit.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Convertkit extends NGL_Abstract_Integration {

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
		$api_key 	= isset( $_POST['ngl_convertkit_key'] ) ? sanitize_text_field( $_POST['ngl_convertkit_key'] ) : '';
		$api_secret = isset( $_POST['ngl_convertkit_secret'] ) ? sanitize_text_field( $_POST['ngl_convertkit_secret'] ) : '';

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

		$forms   = $this->api->make_request( 'v3/forms?api_key=' . $api_key, 'get' );

		// Either account or forms return error. API key + secret testing.
		$valid_account = isset( $account[ 'error' ] ) || isset( $forms[ 'error' ] ) ? false : true;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_convertkit' );

		} else {

			$this->save_integration( $api_key, $api_secret, $account );

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
	public function save_integration( $api_key = '', $api_secret = '', $account = array() ) {
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
				'from_name' 	=> newsletterglue_get_default_from_name(),
				'from_email'	=> isset( $account[ 'primary_email_address' ] ) ? $account[ 'primary_email_address' ] : '',
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

	/**
	 * Get tags.
	 */
	public function get_tags() {

		$_tags = array( '_everyone' => __( 'Everyone', 'newsletter-glue' ) );

		$tags   = $this->api->make_request( 'v3/tags?api_key=' . $this->api_key, 'get' );

		if ( isset( $tags[ 'tags' ] ) ) {
			foreach( $tags[ 'tags' ] as $key => $data ) {
				$_tags[ $data[ 'id' ] ] = $data[ 'name' ];
			}
		}

		return $_tags;

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		if ( ! $email || ! is_email( $email ) ) {
			$response = array(
				'failed'	=> __( 'Enter a valid email.', 'newsletter-glue' ),
			);
		} else {
			return true;
		}

		return $response;

	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in ConvertKit', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new ConvertKit_API( $this->api_key, $this->api_secret );

		$defaults[ 'tags' ] = $this->get_tags();

		return $defaults;
	}

	/**
	 * Send newsletter.
	 */
	public function send_newsletter( $post_id = 0, $data = array(), $test = false ) {

		if ( defined( 'NGL_SEND_IN_PROGRESS' ) ) {
			return;
		}

		define( 'NGL_SEND_IN_PROGRESS', 'sending' );

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$result = array(
			'fail'	=> __( 'Error occured', 'newsletter-glue' ),
		);

		return $result;

	}

}
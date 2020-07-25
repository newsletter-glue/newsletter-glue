<?php
/**
 * MailerLite.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Mailerlite {

	public $api_key = null;

	public $api = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'mailerlite' ] ) ? $integrations[ 'mailerlite'] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_mailerlite_key'] ) ? $_POST['ngl_mailerlite_key'] : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations 	= get_option( 'newsletterglue_integrations' );
			$mailerlite    	= isset( $integrations[ 'mailerlite' ] ) ? $integrations[ 'mailerlite'] : '';
			if ( isset( $mailerlite[ 'api_key'] ) ) {
				$api_key = $mailerlite[ 'api_key' ];
			}
		}

		$this->api       = new \MailerLiteApi\MailerLite( $api_key );

		// Check if account is valid.
		$account_api = $this->api->me()->get();

		$valid_account = isset( $account_api->account ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_mailerlite' );

		} else {

			$this->save_integration( $api_key, ( array ) $account_api->account );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_mailerlite', ( array ) $account_api->account );

		}

		return $result;
	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'mailerlite' ] ) ) {
			unset( $integrations[ 'mailerlite' ] );
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
	public function save_integration( $api_key = '', $account = array() ) {
		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'mailerlite' ] = array();
		$integrations[ 'mailerlite' ][ 'api_key' ] 		= $api_key;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'mailerlite' ] ) ? $globals[ 'mailerlite' ] : '';

		if ( ! $options ) {

			$globals[ 'mailerlite' ] = array(
				'from_name' 	=> newsletterglue_get_default_from_name(),
				'from_email'	=> isset( $account[ 'from' ] ) ? $account[ 'from' ] : '',
			);

			update_option( 'newsletterglue_options', $globals );

		}
	}

	/**
	 * Connect.
	 */
	public function connect() {

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		$response = array(
			'success'	=> __( '<strong>Verified.</strong> <a href="#">Learn more</a>', 'newsletter-glue' ),
		);

		return $response;

	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in MailerLite', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		$defaults = array();

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
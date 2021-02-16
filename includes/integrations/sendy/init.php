<?php
/**
 * Sendy.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Sendy extends NGL_Abstract_Integration {

	public $app		= 'sendy';
	public $api_url = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/client.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
		$this->api_url 		= isset( $integration[ 'api_url' ] ) ? $integration[ 'api_url' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_sendy_key'] ) ? sanitize_text_field( $_POST['ngl_sendy_key'] ) : '';
		$api_url 	= isset( $_POST['ngl_sendy_url'] ) ? untrailingslashit( esc_url( $_POST['ngl_sendy_url'] ) ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations	= get_option( 'newsletterglue_integrations' );
			$sendy  		= isset( $integrations[ 'sendy' ] ) ? $integrations[ 'sendy'] : '';
			if ( isset( $sendy[ 'api_key'] ) ) {
				$api_key = $sendy[ 'api_key' ];
			}
			if ( isset( $sendy[ 'api_url'] ) ) {
				$api_url = $sendy[ 'api_url' ];
			}
		}

		$this->api = new NGL_Sendy_API( untrailingslashit( $api_url ), $api_key );

		$testconnection = $this->api->post( '/api/campaigns/create.php', array( 'boolean' => true ) );

		if ( strstr( $testconnection, 'Invalid' ) || ! $testconnection ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_sendy' );

		} else {

			$this->save_integration( $api_key, $api_url );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_sendy', array() );

		}

		return $result;

	}

	/**
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_url = '' ) {

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'sendy' ] = array();
		$integrations[ 'sendy' ][ 'api_key' ] = $api_key;
		$integrations[ 'sendy' ][ 'api_url' ] = $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'sendy' ] ) ? $globals[ 'sendy' ] : '';

		if ( ! $options ) {

			$globals[ 'sendy' ] = array(
				'from_name' 	=> newsletterglue_get_default_from_name(),
			);

			update_option( 'newsletterglue_options', $globals );

		}
	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_url",
			'placeholder' 	=> esc_html__( 'Enter Sendy installation URL', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_url'] ) ? $integrations[ $app ]['api_url'] : '',
			'class'			=> 'ngl-text-margin',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
		) );

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

		$subject 	= isset( $data['subject'] ) ? $data['subject'] : '';
		$from_name	= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email	= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$lists		= ! empty( $data['lists'] ) ? $data['lists'] : '';
		$brand		= ! empty( $data['brand'] ) ? $data['brand'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		$post = get_post( $post_id );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		// Do test email.
		if ( $test ) {
			$response = array();

			$test_email = $data[ 'test_email' ];

			if ( $this->is_invalid_email( $test_email ) ) {
				return $this->is_invalid_email( $test_email );
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = newsletterglue_generate_content( $post, $subject, 'mailerlite' );

			wp_mail( $test_email, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body );

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

	}

}
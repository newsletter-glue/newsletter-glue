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
			$options  		= isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';
			if ( isset( $options[ 'api_key'] ) ) {
				$api_key = $options[ 'api_key' ];
			}
			if ( isset( $options[ 'api_url'] ) ) {
				$api_url = $options[ 'api_url' ];
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

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;
		$integrations[ $this->app ][ 'api_url' ] = $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ $this->app ] ) ? $globals[ $this->app ] : '';

		if ( ! $options ) {

			$globals[ $this->app ] = array(
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
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return true;
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

			$body = newsletterglue_generate_content( $post, $subject, $this->app );

			wp_mail( $test_email, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body );

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		// Send a campaign live or draft.
		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );

		$args = array(
			'from_name'		=> $from_name,
			'from_email'	=> $from_email,
			'reply_to'		=> $from_email,
			'boolean'		=> true,
			'title'			=> $subject,
			'subject'		=> $subject,
			'html_text'		=> newsletterglue_generate_content( $post, $subject, $this->app ),
			'list_ids'		=> $lists,
			'brand_id'		=> ( $brand ) ? $brand : 1,
			'send_campaign'	=> ( $schedule === 'immediately' ) ? 1 : 0
		);

		$campaign = $this->api->post( '/api/campaigns/create.php', $args );

		if ( $schedule === 'draft' ) {
			$result = array( 'status' => 'draft' );
		} else {
			$result = array( 'status' => 'sent' );
		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( ( array ) $result ), '' );

		return $campaign;

	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] == 'draft' ) {
				$output[ 'status' ]		= 200;
				$output[ 'type' ]		= 'neutral';
				$output[ 'message' ]    = __( 'Saved as draft', 'newsletter-glue' );
			}

			if ( $result['status'] == 'sent' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}

		}

		return $output;

	}

}
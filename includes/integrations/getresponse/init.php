<?php
/**
 * GetResponse.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Getresponse extends NGL_Abstract_Integration {

	public $api_url = null;

	public $api = null;

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
		$integration  = isset( $integrations[ 'getresponse' ] ) ? $integrations[ 'getresponse'] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_getresponse_key'] ) ? sanitize_text_field( $_POST['ngl_getresponse_key'] ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations	= get_option( 'newsletterglue_integrations' );
			$getresponse  	= isset( $integrations[ 'getresponse' ] ) ? $integrations[ 'getresponse'] : '';
			if ( isset( $getresponse[ 'api_key'] ) ) {
				$api_key = $getresponse[ 'api_key' ];
			}
		}

		$this->api = new NGL_GetResponse_API( $api_key );

		$account = $this->api->get( '/accounts' );

		if ( ! isset( $account[ 'email' ] ) ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_getresponse' );

		} else {

			$this->save_integration( $api_key, $account );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_getresponse', $account );

		}

		return $result;

	}


	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'getresponse' ] ) ) {
			unset( $integrations[ 'getresponse' ] );
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

		$integrations[ 'getresponse' ] = array();
		$integrations[ 'getresponse' ][ 'api_key' ] = $api_key;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'getresponse' ] ) ? $globals[ 'getresponse' ] : '';

		if ( ! $options ) {

			$globals[ 'getresponse' ] = array(
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

		$this->api = new NGL_GetResponse_API( $this->api_key );

	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in GetResponse', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$defaults = array();

		$defaults[ 'lists' ] = $this->get_lists();

		return $defaults;

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$senders = $this->api->get( '/from-fields' );

		// Check if email is a valid sender.
		$verified = false;
		if ( $senders ) {
			foreach( $senders as $key => $data ) {
				if ( isset( $data[ 'email' ] ) && trim( $email ) === trim( $data[ 'email' ] ) && $data[ 'isActive' ] == 'true' ) {
					$verified = true;
				}
			}
		}

		if ( $verified ) {

			$response = array(
				'success'	=> __( '<strong>Verified.</strong> <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">Learn more</a>', 'newsletter-glue' ),
			);

		} else {

			$response = array(
				'failed'	=> __( '<strong>Email not verified. This means your emails won&rsquo;t send.<br />
					<a href="https://app.getresponse.com/email-addresses" target="_blank">Verify email now <i class="external alternate icon"></i></a></strong> Or <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">learn more.</a>', 'newsletter-glue' ),
			);

		}

		return $response;

	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = $this->api->get( '/campaigns' );

		if ( isset( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$_lists[ $data[ 'campaignId' ] ] = $data[ 'name' ];
			}
		}

		return $_lists;
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

		$subject 		= isset( $data['subject'] ) ? urldecode( $data['subject'] ) : '';
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$campaign		= isset( $data['lists'] ) ? $data['lists'] : '';
		//$segment		= isset( $data['segment'] ) && $data['segment'] && ( $data['segment'] != '_everyone' ) ? $data['segment'] : '';
		$schedule  	 	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$fromFieldId    = '';

		$post = get_post( $post_id );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		$this->api = new NGL_GetResponse_API( $this->api_key );

		$senders = $this->api->get( '/from-fields' );
		foreach( $senders as $key => $sender ) {
			if ( $sender[ 'email' ] == $from_email ) {
				$fromFieldId = $sender[ 'fromFieldId' ];
			}
		}

		$args = array(
			'content'	=> array(
				'html'	=> newsletterglue_generate_content( $post, $subject, 'getresponse' ),
			),
			'subject'	=> $subject,
			'campaign'	=> array(
				'campaignId'	=> $campaign
			),
			'fromField'	=> array(
				'fromFieldId' 	=> $fromFieldId,
			),
			'replyTo'	=> array(
				'fromFieldId' 	=> $fromFieldId,
			),
			'editor'	=> 'custom',
			'type'		=> $schedule === 'immediately' ? 'broadcast' : 'draft',
			'name'		=> sprintf( __( 'Newsletter Glue - Campaign %s', 'newsletter-glue' ), uniqid() ),
			'sendSettings' => array(
				'selectedCampaigns'	=> array( $campaign ),
			),
		);

		$newsletter = $this->api->post( '/newsletters', $args );

		return $newsletter;
	}

}
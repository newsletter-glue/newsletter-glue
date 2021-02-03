<?php
/**
 * MailerLite.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Mailerlite extends NGL_Abstract_Integration {

	public $app 	= 'mailerlite';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/vendor/autoload.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_mailerlite', array( $this, 'newsletterglue_email_content_mailerlite' ), 10, 3 );
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
		$api_key 	= isset( $_POST['ngl_mailerlite_key'] ) ? sanitize_text_field( $_POST['ngl_mailerlite_key'] ) : '';

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

		$defaults[ 'groups' ] 	= $this->get_groups();
		$defaults[ 'segments' ] = $this->get_segments();

		return $defaults;
	}

	/**
	 * Get groups.
	 */
	public function get_groups() {
		$_groups = array();

		$groups = $this->api->groups();
		$array  = $groups->get();

		if ( ! empty( $array ) && isset( $array->items ) ) {
			foreach( (array) $array->items as $key => $data ) {
				$_groups[ $data->id ] = $data->name;
			}
		}

		return $_groups;
	}

	/**
	 * Get groups.
	 */
	public function get_segments() {
		$_segments = array();

		$segments 	= $this->api->segments();
		$array  	= $segments->get();

		if ( ! empty( $array ) && isset( $array->items ) && ! empty( $array->items[0]->data ) ) {
			foreach( $array->items[0]->data as $key => $data ) {
				$_segments[ $data->id ] = $data->title;
			}
		}

		return $_segments;
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

		$campaignId = 0;

		$subject 	= isset( $data['subject'] ) ? $data['subject'] : '';
		$from_name	= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email	= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$groups		= isset( $data['groups'] ) && ! empty( $data['groups'] ) && is_array( $data['groups'] ) ? array_map( 'intval', $data['groups'] ) : '';
		$segments	= isset( $data['segments'] ) && ! empty( $data['segments'] ) && is_array( $data['segments'] ) ? array_map( 'intval', $data['segments'] ) : '';
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

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		// At least set groups.
		if ( empty( $groups ) && empty( $segments ) ) {
			$_groups 	= $this->get_groups();
			$groups 	= array_keys( $_groups );
		}

		$campaignsApi = $this->api->campaigns();

		// Set campaign data.
		$campaignData = array(
			'type' 		=> 'regular',
			'subject'	=> $subject,
			'from_name'	=> $from_name,
			'from'		=> $from_email,
		);

		// Add groups and segments.
		if ( ! empty( $groups ) ) {
			$campaignData[ 'groups' ] = $groups;
		}
		if ( ! empty( $segments ) ) {
			$campaignData[ 'segments' ] = $segments;
		}

		$campaign = $campaignsApi->create( $campaignData );

		if ( isset( $campaign->id ) ) {
			$campaignId = $campaign->id;
		}

		// Add content.
		if ( $campaignId > 0 ) {

			$plain_content = __( 'Your email client does not support HTML emails. Open newsletter here: {$url}. If you do not want to receive emails from us, click here: {$unsubscribe}', 'newsletter-glue' );

			$contentData = array(
				'html'	=> newsletterglue_generate_content( $post, $subject, 'mailerlite' ),
				'plain' => $plain_content,
			);

			$result = $campaignsApi->addContent( $campaignId, $contentData );

		}

		// Send it.
		if ( $schedule === 'draft' ) {

			$result = array( 'status' => 'draft' );

		} else {

			$result = $campaignsApi->send( $campaignId ); 

		}

		newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( (array) $result ), $campaignId );

		return $result;

	}

	/**
	 * Set content type as HTML.
	 */
	public function wp_mail_content_type() {
		return 'text/html';
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

		}

		if ( isset( $result[ 'id' ] ) ) {
			$output[ 'status' ] 	= 200;
			$output[ 'type'   ] 	= 'success';
			$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
		}

		return $output;

	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$this->api = new \MailerLiteApi\MailerLite( $this->api_key );

		$subscriber = array(
			'email' 	=> $email,
			'name' 		=> ! empty( $name ) ? $name : '',
		);

		if ( ! empty( $list_id ) ) {
			$groupsApi	 	= $this->api->groups();
			$result 		= $groupsApi->addSubscriber( $list_id, $subscriber );
		} else {
			$subscribersApi = $this->api->subscribers();
			$result 		= $subscribersApi->create( $subscriber );
		}

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$groupsApi	 	= $this->api->groups();
			$result 		= $groupsApi->addSubscriber( $extra_list_id, $subscriber );
		}

		return true;

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_mailerlite( $content, $post, $subject ) {

		$content .= '<p class="ngl-credits"><a href="{$unsubscribe}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a></p>';

		return $content;

	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
			'helper'		=> '<a href="https://app.mailerlite.com/integrations/api/" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
		) );

	}

}
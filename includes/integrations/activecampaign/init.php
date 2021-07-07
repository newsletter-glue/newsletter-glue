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

	public $app 	= 'activecampaign';
	public $api_url = null;
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/ActiveCampaign.class.php';

		$this->get_api_key();

		add_filter( 'newsltterglue_activecampaign_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';

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
			$integrations 	= get_option( 'newsletterglue_integrations' );
			$options  		= isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';
			if ( isset( $options[ 'api_key'] ) ) {
				$api_key = $options[ 'api_key' ];
			}
			if ( isset( $options[ 'api_url'] ) ) {
				$api_url = $options[ 'api_url' ];
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
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $api_url = '', $account = array() ) {

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] 		= $api_key;
		$integrations[ $this->app ][ 'api_url' ] 		= $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ $this->app ] ) ? $globals[ $this->app ] : '';

		if ( ! $options ) {

			$globals[ $this->app ] = array(
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
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		$defaults = array();

		$defaults[ 'lists' ] 	= $this->get_lists();

		return $defaults;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {

		$_lists = array();

		$lists = $this->api->api( 'list_/list_', array( 'ids' => 'all', 'full' => 0 ) );

		if ( ! empty( $lists ) ) {
			foreach( $lists as $key => $data ) {
				$array = (array) $data;
				$id = @$array['id'];
				if ( $id ) {
					$_lists[ $id ] = @$array[ 'name' ];
				}
			}
		}

		return $_lists;

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

		$post = get_post( $post_id );

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$subject 		= isset( $data['subject'] ) ? urldecode( $data['subject'] ) : urldecode( $post->post_title );
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$lists			= isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? $data[ 'lists' ] : '';
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;

		}

		// Do test email.
		if ( $test ) {
			$response = array();

			$test_email = $data[ 'test_email' ];

			if ( ! is_email( $test_email ) ) {
				$response[ 'fail' ] = __( 'Please enter a valid email', 'newsletter-glue' );
				return $response;
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = newsletterglue_generate_content( $post, $subject, $this->app );

			wp_mail( $test_email, sprintf( __( '[Test] %s', 'newsletter-glue' ), $subject ), $body );

			$response['success'] = $this->get_test_success_msg();

			return $response;

		}

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		if ( empty( $lists ) ) {
			$thelists = $this->get_lists();
			$lists = array_keys( $thelists );
		}

		$params = array(
			'fromemail'			=> $from_email,
			'fromname'			=> $from_name,
			'subject'			=> $subject,
			'format'			=> 'mime',
			'reply2'			=> $from_email,
			'htmlconstructor' 	=> 'editor',
			'html'				=> newsletterglue_generate_content( $post, $subject, $this->app ),
			'charset' 			=> 'utf-8',
			'encoding'			=> 'quoted-printable',
		);

		foreach( $lists as $list_id ) {
			$params[ "p[$list_id]" ] = $list_id;
		}

		$result = $this->api->api( 'message/add', $params );

		// Message ID available.
		if ( isset( $result->id ) ) {
			$message_id = $result->id;
			$args = array(
				'name'			=> sprintf( __( 'Newsletter Glue - Campaign %s', 'newsletter-glue' ), uniqid() ),
				'status'		=> ( $schedule == 'immediately' ) ? 1 : 0,
				'embed_images'	=> 1,
			);
			$args[ "m[$message_id]" ] = 100;
			foreach( $lists as $list_id ) {
				$args[ "p[$list_id]" ] = $list_id;
			}
			$send = $this->api->api( 'campaign/create', $args );

			// Store the status.
			if ( isset( $send->id ) ) {

				if ( $schedule === 'draft' ) {
					$status = array( 'status' => 'draft' );
				} else {
					$status = array( 'status' => 'sent' );
				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( ( array ) $status ), $send->id );

				return $status;
			}

		}

	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] === 'draft' ) {
				$output[ 'status' ]		= 200;
				$output[ 'type' ]		= 'neutral';
				$output[ 'message' ]    = __( 'Saved as draft', 'newsletter-glue' );
			}

			if ( $result[ 'status' ] === 'sent' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}

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

		$fname = '';
		$lname = '';

		if ( isset( $name ) ) {
			$name_array = $array = explode( ' ', $name, 2 );
			$fname = $name_array[0];
			$lname = isset( $name_array[1] ) ? $name_array[1] : '';
		}

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		if ( ! empty( $list_id ) ) {

			$args = array(
				"p[$list_id]" 	=> $list_id,
				"email"			=> $email,
				"first_name"	=> $fname,
				"last_name"		=> $lname,
			);

			if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
				$args[ "p[$extra_list_id]" ] = $extra_list_id;
			}

			$this->api->api( 'contact/add', $args );

		}

		return true;

	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

		$app = $this->app;

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_url",
			'placeholder' 	=> esc_html__( 'Enter API URL', 'newsletter-glue' ),
			'value'			=> isset( $integrations[ $app ]['api_url'] ) ? $integrations[ $app ]['api_url'] : '',
			'class'			=> 'ngl-text-margin',
		) );

		newsletterglue_text_field( array(
			'id' 			=> "ngl_{$app}_key",
			'placeholder' 	=> esc_html__( 'Enter API Key', 'newsletter-glue' ),
			'helper'		=> '<a href="https://www.activecampaign.com/login" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
			'value'			=> isset( $integrations[ $app ]['api_key'] ) ? $integrations[ $app ]['api_key'] : '',
		) );

	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return false;
	}

	/**
	 * Get email verify help.
	 */
	public function get_email_verify_help() {
		return 'https://help.activecampaign.com/hc/en-us/articles/360015584680-Verify-your-email-domain';
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );
		return $this->get_lists();
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$html = $this->convert_tags( $html, $post_id );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {

		switch ( $tag ) {
			case 'unsubscribe_link' :
				return '%UNSUBSCRIBELINK%';
			break;
			case 'phone' :
				return '%PHONE%';
			break;
			case 'list' :
				return '%LISTNAME%';
			break;
			case 'full_name' :
				return '%FULLNAME%';
			break;
			case 'first_name' :
				return '%FIRSTNAME%';
			break;
			case 'last_name' :
				return '%LASTNAME%';
			break;
			case 'email' :
				return '%EMAIL%';
			break;
			case 'update_preferences' :
				return '%UPDATELINK%';
			break;
			case 'admin_address' :
				return '%SENDER-INFO-SINGLELINE%';
			break;
			default :
				return apply_filters( "newsletterglue_{$this->app}_custom_tag", '', $tag, $post_id );
			break;
		}

		return false;
	}

}
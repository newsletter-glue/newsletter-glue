<?php
/**
 * Sendinblue.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Sendinblue extends NGL_Abstract_Integration {

	public $app		= 'sendinblue';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/SendinblueApiClient.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'sendinblue' ] ) ? $integrations[ 'sendinblue'] : '';

		$this->api_key 		= isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';

	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key 	= isset( $_POST['ngl_sendinblue_key'] ) ? sanitize_text_field( $_POST['ngl_sendinblue_key'] ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations 	= get_option( 'newsletterglue_integrations' );
			$sendinblue    	= isset( $integrations[ 'sendinblue' ] ) ? $integrations[ 'sendinblue'] : '';
			if ( isset( $sendinblue[ 'api_key'] ) ) {
				$api_key = $sendinblue[ 'api_key' ];
			}
		}

		$this->api       = new NGL_SendinblueApiClient( $api_key );

		// Check if account is valid.
		$account_api = $this->api->getAccount();

		$valid_account = isset( $account_api[ 'email' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_sendinblue' );

		} else {

			$this->save_integration( $api_key, $account_api );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_sendinblue', $account_api );

		}

		return $result;
	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'sendinblue' ] ) ) {
			unset( $integrations[ 'sendinblue' ] );
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

		$integrations[ 'sendinblue' ] = array();
		$integrations[ 'sendinblue' ][ 'api_key' ] 		= $api_key;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'sendinblue' ] ) ? $globals[ 'sendinblue' ] : '';

		if ( ! $options ) {

			$globals[ 'sendinblue' ] = array(
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

		$this->api = new NGL_SendinblueApiClient( $this->api_key );

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		if ( ! $email ) {
			$response = array( 'failed' => __( 'Please enter email', 'newsletter-glue' ) );
		} elseif ( ! is_email( $email ) ) {
			$response = array( 'failed'	=> __( 'Invalid email', 'newsletter-glue' ) );
		}

		if ( ! empty( $response ) ) {
			return $response;
		}

		$this->api = new NGL_SendinblueApiClient( $this->api_key );

		$senders = $this->get_senders();

		// Check if email is a valid sender.
		$verified = false;
		foreach( $senders as $key => $data ) {
			if ( isset( $data[ 'email' ] ) ) {
				if ( $email == $data[ 'email' ] && $data['active'] == true ) {
					$verified = true;
				}
			}
		}

		if ( $verified ) {

			$response = array(
				'success'	=> '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'			=> __( 'Not verified', 'newsletter-glue' ),
				'failed_details'	=> '<a href="https://account.sendinblue.com/senders/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a> <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
			);

		}

		return $response;

	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in Sendinblue', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_SendinblueApiClient( $this->api_key );

		$defaults = array();

		return $defaults;
	}

	/**
	 * Get Senders.
	 */
	public function get_senders() {
		$senders = $this->api->getSenders();
		if ( isset( $senders[ 'senders' ] ) ) {
			return $senders[ 'senders' ];
		}
	}

	/**
	 * Get Lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = $this->api->getAllLists();

		if ( isset( $lists[ 'lists' ] ) ) {
			foreach( $lists[ 'lists' ] as $key => $data ) {
				$_lists[ $data[ 'id' ] ] = $data[ 'name' ];
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

		$subject 	= isset( $data['subject'] ) ? $data['subject'] : '';
		$from_name	= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email	= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$lists      = isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? array_map( 'intval', $data['lists'] ) : '';

		$this->api  = new NGL_SendinblueApiClient( $this->api_key );

		// At least set lists.
		if ( empty( $lists ) ) {
			$_lists 	= $this->get_lists();
			$lists 		= array_keys( $_lists );
		}

		$post = get_post( $post_id );

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		// Verify domain.
		$senders = $this->get_senders();

		$verified = false;
		foreach( $senders as $key => $sender_info ) {
			if ( isset( $sender_info[ 'email' ] ) ) {
				if ( $from_email == $sender_info[ 'email' ] ) {
					$verified = true;
				}
			}
		}

		if ( ! $verified ) {

			$result = array(
				'fail'	=> __( 'Your <strong>From Email</strong> address isn&rsquo;t verified.', 'newsletter-glue' ) . '<br />' . '<a href="https://account.sendinblue.com/senders/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a> <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
			);

			if ( ! $test ) {
				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ) );
			}

			return $result;

		}

		// Prepare campaign attributes.
		$campaign = array(
			'sender'		=> array(
				'name'	=> $from_name,
				'email'	=> $from_email,
			),
			'name'			=> $subject,
			'htmlContent'	=> newsletterglue_generate_content( $post, $subject, 'sendinblue' ),
			'subject'		=> $subject,
			'replyTo'		=> $from_email,
			'recipients'	=> array(
				'listIds'	=> $lists,
			),
		);

		// Create a campaign.
		$result = $this->api->createCampaign( $campaign );

		// Handle errors with creating this campaign.
		if ( isset( $result[ 'code' ] ) ) {

			if ( $test ) {

				if ( $result[ 'code' ] == 'account_under_validation' ) {
					$errors[ 'fail' ] = sprintf( __( 'Your Sendinblue account is being validated. You can&rsquo;t create another campaign.<br />%s', 'newsletter-glue' ),
						'<a href="https://help.sendinblue.com/hc/en-us/articles/209408165--Why-has-my-Sendinblue-account-not-been-validated-yet-" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' );
				} else {
					$errors[ 'fail' ] = $result[ 'message' ];
				}

				return $errors;

			}

		} else {

			// Campaign created.
			$campaign_id = $result[ 'id' ];

			// Send campaign as test then delete it.
			if ( $test ) {

				$response = array();

				$test_emails = array();
				$test_emails[] = $data['test_email'];

				// Send campaign to a test email.
				$result = $this->api->sendCampaignTest( $campaign_id, array( 'emailTo' => $test_emails ) );

				// Validate the latest response.
				if ( isset( $result[ 'code' ] ) ) {
					$response[ 'fail' ] = sprintf( __( 'Email address isn&rsquo;t an existing contact.<br />Sendinblue only sends test emails to existing contacts. %s', 'newsletter-glue' ), 
					'<a href="https://my.sendinblue.com/users/list" target="_blank">' . __( 'Add new contact', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' );
				} else {
					$response[ 'success' ] = $this->get_test_success_msg();
				}

				// Keep one campaign only for test.
				$last_test_id = get_post_meta( $post_id, '_ngl_last_test', true );
				if ( $last_test_id ) {
					$this->api->deleteCampaign( $last_test_id );
				}
				update_post_meta( $post_id, '_ngl_last_test', $campaign_id );

				return $response;

			} else {

				if ( $schedule === 'draft' ) {

					$result = array(
						'status' => 'draft'
					);

				}

				if ( $schedule === 'immediately' ) {
					$result = $this->api->sendCampaign( $campaign_id );
				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $campaign_id );

				return $result;

			}

		}

		return $result;
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

		} else {

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

		$fname = '';
		$lname = '';

		if ( isset( $name ) ) {
			$name_array = $array = explode( ' ', $name, 2 );
			$fname = $name_array[0];
			$lname = isset( $name_array[1] ) ? $name_array[1] : '';
		}

		$this->api  = new NGL_SendinblueApiClient( $this->api_key );

		$attributes = new stdClass();
		$attributes->FNAME = trim( $fname );
		$attributes->LNAME = trim( $lname );

		$user = array(
			'email'				=> $email,
			'updateEnabled'		=> true,
			'attributes'		=> $attributes,
		);

		$listIds = array();

		if ( ! empty( $list_id ) ) {
			$listIds[] = $list_id;
		}
		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$listIds[] = $extra_list_id;
		}

		if ( ! empty( $listIds ) ) {
			$user[ 'listIds' ] = $listIds;
		}

		$result = $this->api->createUser( $user );

		return true;

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
			'helper'		=> '<a href="https://account.sendinblue.com/advanced/api" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
		) );

	}

}
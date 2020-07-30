<?php
/**
 * Mailchimp.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Class.
 */
class NGL_Mailchimp {

	public $api_key = null;

	public $api = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';
		include_once 'lib/batch.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {
		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'mailchimp' ] ) ? $integrations[ 'mailchimp'] : '';
		$this->api_key = isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key = isset( $_POST['ngl_mailchimp_key'] ) ? $_POST['ngl_mailchimp_key'] : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations = get_option( 'newsletterglue_integrations' );
			$mailchimp    = isset( $integrations[ 'mailchimp' ] ) ? $integrations[ 'mailchimp'] : '';
			if ( isset( $mailchimp[ 'api_key'] ) ) {
				$api_key = $mailchimp[ 'api_key' ];
			}
		}

		$this->api = new NGL_Mailchimp_API( $api_key );

		$this->api->verify_ssl = false;

		$account = $this->api->get( '/' );

		$valid_account = ! empty( $account ) && isset( $account[ 'account_id' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_mailchimp' );

		} else {

			$this->save_integration( $api_key, $account );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_mailchimp', $account );

		}

		return $result;
	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'mailchimp' ] ) ) {
			unset( $integrations[ 'mailchimp' ] );
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
	public function save_integration( $api_key = '', $account = '' ) {
		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'mailchimp' ] = array();
		$integrations[ 'mailchimp' ][ 'api_key' ] = $api_key;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'mailchimp' ] ) ? $globals[ 'mailchimp' ] : '';

		if ( ! $options ) {

			$globals[ 'mailchimp' ] = array(
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

		$this->api = new NGL_Mailchimp_API( $this->api_key );

		$this->api->verify_ssl = false;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new NGL_Mailchimp_API( $this->api_key );

		$this->api->verify_ssl = false;

		$defaults[ 'audiences' ] = $this->get_audiences();

		return $defaults;
	}

	/**
	 * Get audiences.
	 */
	public function get_audiences() {
		$audiences = array();

		$data = $this->api->get( 'lists' );

		if ( ! empty( $data[ 'lists' ] ) ) {
			foreach( $data[ 'lists' ] as $key => $array ) {
				$audiences[ $array[ 'id' ] ] = $array[ 'name' ];
			}
		}

		return $audiences;
	}

	/**
	 * Get segments.
	 */
	public function get_segments( $audience_id = '' ) {
		$segments = array( '_everyone' => __( 'Everyone in audience', 'newsletter-glue' ) );

		$data = $this->api->get( 'lists/' . $audience_id . '/segments' );

		if ( isset( $data['segments' ] ) && ! empty( $data['segments'] ) ) {
			foreach( $data['segments'] as $key => $array ) {
				$segments[ $array['id'] ] = $array['name'];
			}
		}

		return $segments;
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
		$audience	= isset( $data['audience'] ) ? $data['audience'] : '';
		$segment	= isset( $data['segment'] ) && $data['segment'] && ( $data['segment'] != '_everyone' ) ? $data['segment'] : '';
		$schedule   = isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		// API request.
		$this->api = new NGL_Mailchimp_API( $this->api_key );
		$this->api->verify_ssl = false;

		$post = get_post( $post_id );

		// Verify domain.
		$domain_parts = explode( '@', $from_email );
		$domain = isset( $domain_parts[1] ) ? $domain_parts[1] : '';

		$result = $this->api->get( 'verified-domains/' . $domain );

		if ( isset( $result['status'] ) && $result['status'] === 404 ) {

			// Add unverified domain as campaign data.
			if ( ! $test ) {
				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ) );
			}

			$result = array(
				'fail'	=> sprintf( __( 'Your <strong><em>From Email</em></strong> address isn&rsquo;t verified.<br />
						%s Or %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/account/domains/" target="_blank">' . __( 'Verify email now &#8599;.', 'newsletter-glue' ) . '</a>', '<a href="https://docs.memberhero.pro/article/7-unverified-email" target="_blank">' . __( 'learn more.', 'newsletter-glue' ) . '</a>' ),
			);

			return $result;

		}

		// Settings.
		$settings = array(
			'subject_line' 	=> $subject,
			'reply_to' 		=> $from_email,
			'from_name' 	=> $from_name,
		);

		// Setup campaign array.
		$campaign_array = array(
			'type' 			=>	'regular',
			'recipients' 	=> array(
				'list_id' 	=> $audience,
			),
			'settings'		=> $settings
		);

		// Add segment.
		if ( $segment ) {
			$campaign_array['recipients']['segment_opts'] = array( 'saved_segment_id' => ( int ) $segment );
		}

		// Create a campaign.
		$result = $this->api->post( 'campaigns', $campaign_array );

		// Get campaign ID.
		$response 	= $this->api->getLastResponse();
		$output 	= json_decode( $response['body'] );

		if ( ! empty( $output->id ) ) {

			$campaign_id = $output->id;

			// Manage campaign content
			$result = $this->api->put( 'campaigns/' . $campaign_id . '/content', [
				'html'	=> newsletterglue_generate_content( $post, $subject ),
			] );

			if ( $test ) {

				$response = array();

				$test_emails = array();
				$test_emails[] = $data['test_email'];

				$result = $this->api->post( 'campaigns/' . $campaign_id . '/actions/test', array(
					'test_emails'	=> $test_emails,
					'send_type'		=> 'html',
				) );

				// Process test email response.
				if ( isset( $result['status'] ) && $result['status'] == 400 ) {

					$response['fail'] = $this->get_test_limit_msg();

				} else {

					$response['success'] = $this->get_test_success_msg();

				}

				return $response;

			} else {

				if ( $schedule === 'immediately' ) {

					$result = $this->api->post( 'campaigns/' . $campaign_id . '/actions/send' );

				}

				if ( $schedule === 'draft' ) {

					$result = array(
						'status' => 'draft'
					);

				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $campaign_id );

				return $result;

			}

		} else {

			$errors = array();

			if ( $test ) {
				if ( isset( $output->status ) ) {
					if ( $output->status == 400 ) {
						if ( 'settings.subject_line' === $output->errors[0]->field ) {
							$errors[ 'fail' ]   = __( 'Whoops! The subject line is empty.<br />Fill it out to send.', 'newsletter-glue' );
						}
					}
				}
				return $errors;
			}

			if ( ! $test ) {
				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ) );
			}

			return $result;

		}

	}

	/**
	 * Check if the account is free.
	 */
	public function is_free_account() {
		$options = get_option( 'newsletterglue_mailchimp' );

		if ( isset( $options[ 'pricing_plan_type' ] ) ) {
			if ( $options[ 'pricing_plan_type' ] === 'forever_free' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Test success.
	 */
	public function get_test_success_msg() {

		$message = __( 'Your email is on its way!<br />Check your inbox in 3-5 minutes.', 'newsletter-glue' ) 
		. '<br /><span style="color:rgba(0, 0, 0, 0.6) !important;">' . sprintf( __( 'Can&rsquo;t find your email? %s', 'newsletter-glue' ), '<a href="https://docs.memberhero.pro/article/11-email-delivery" target="_blank">' . __( 'Get help', 'newsletter-glue' ) . '</a>' ) . '.</span>';

		return $message;

	}

	/**
	 * Test failed.
	 */
	public function get_test_limit_msg() {

		if ( $this->is_free_account() ) {
			$test_count = 24;
		} else {
			$test_count = 200;
		}

		$message = __( 'Try testing again tomorrow?', 'newsletter-glue' );
		$message .= '<br />';
		$message .= sprintf( __( 'You&rsquo;ve sent too many test emails today. Mailchimp only allows %s test emails every 24 hours for your account.', 'newsletter-glue' ), $test_count );

		return $message;
	}

	/**
	 * Prepare result for plugin.
	 */
	public function prepare_message( $result ) {
		$output = array();

		if ( isset( $result['status'] ) ) {

			if ( $result['status'] == 400 ) {
				$output[ 'status' ] 	= 400;
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Missing subject', 'newsletter-glue' );
				$output[ 'help' ]       = '';
			}

			if ( $result['status'] == 404 ) {
				$output[ 'status' ] 	= 404;
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Unverified domain', 'newsletter-glue' );
				$output[ 'notice' ]		= sprintf( __( 'Your email newsletter was not sent, because your email address is not verified. %s Or %s', 'newsletter-glue' ), 
				'<a href="https://admin.mailchimp.com/account/domains/" target="_blank">' . __( 'Verify email now &#8599;.', 'newsletter-glue' ) . '</a>', '<a href="https://docs.memberhero.pro/article/7-unverified-email" target="_blank">' . __( 'learn more.', 'newsletter-glue' ) . '</a>' );
				$output[ 'help' ]       = 'https://docs.memberhero.pro/article/7-unverified-email';
			}

			if ( $result['status'] == 'draft' ) {
				$output[ 'status' ]		= 200;
				$output[ 'type' ]		= 'neutral';
				$output[ 'message' ]    = __( 'Saved as draft', 'newsletter-glue' );
			}

		} else {

			if ( $result === true ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}

		}

		return $output;
	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in Mailchimp', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		$this->api = new NGL_Mailchimp_API( $this->api_key );
		$this->api->verify_ssl = false;

		// Verify domain.
		$parts  = explode( '@', $email );
		$domain = isset( $parts[1] ) ? $parts[1] : '';

		$result = $this->api->get( 'verified-domains/' . $domain );

		if ( isset( $result['verified'] ) && $result['verified'] == true ) {

			$response = array(
				'success'	=> __( '<strong>Verified.</strong> <a href="https://docs.memberhero.pro/article/7-unverified-email" target="_blank">Learn more</a>', 'newsletter-glue' ),
			);

		} else {

			$response = array(
				'failed'	=> __( '<strong>Email not verified. This means your emails won&rsquo;t send.<br />
					<a href="https://admin.mailchimp.com/account/domains/" target="_blank">Verify email now &#8599;.</a></strong> Or <a href="https://docs.memberhero.pro/article/7-unverified-email" target="_blank">learn more.</a>', 'newsletter-glue' ),
			);

		}

		return $response;
	}

}
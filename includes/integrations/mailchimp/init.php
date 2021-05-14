<?php
/**
 * Mailchimp.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Mailchimp extends NGL_Abstract_Integration {

	public $app		= 'mailchimp';
	public $api_key = null;
	public $api 	= null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/api.php';
		include_once 'lib/batch.php';

		$this->get_api_key();

		add_filter( 'newsltterglue_mailchimp_html_content', array( $this, 'html_content' ), 10, 2 );
	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {
		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';
		$this->api_key = isset( $integration[ 'api_key' ] ) ? $integration[ 'api_key' ] : '';
	}

	/**
	 * Add Integration.
	 */
	public function add_integration() {

		// Get API key from input.
		$api_key = isset( $_POST['ngl_mailchimp_key'] ) ? sanitize_text_field( $_POST['ngl_mailchimp_key'] ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations 	= get_option( 'newsletterglue_integrations' );
			$options		= isset( $integrations[ $this->app ] ) ? $integrations[ $this->app ] : '';
			if ( isset( $options[ 'api_key'] ) ) {
				$api_key = $options[ 'api_key' ];
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
	 * Save Integration.
	 */
	public function save_integration( $api_key = '', $account = '' ) {

		delete_option( 'newsletterglue_integrations' );

		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ $this->app ] = array();
		$integrations[ $this->app ][ 'api_key' ] = $api_key;

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
	 * Get default list ID.
	 */
	public function get_default_list_id() {
		$audiences = array();

		$this->api = new NGL_Mailchimp_API( $this->api_key );

		$this->api->verify_ssl = false;

		$data = $this->api->get( 'lists', array( 'count' => 1000 ) );

		if ( ! empty( $data[ 'lists' ] ) ) {
			foreach( $data[ 'lists' ] as $key => $array ) {
				return $array[ 'id' ];
			}
		}

		return '';
	}

	/**
	 * Get audiences.
	 */
	public function get_audiences() {
		$audiences = array();

		$data = $this->api->get( 'lists', array( 'count' => 1000 ) );

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

		$data = $this->api->get( 'lists/' . $audience_id . '/segments', array( 'count' => 1000 ) );

		if ( isset( $data['segments' ] ) && ! empty( $data['segments'] ) ) {
			foreach( $data['segments'] as $key => $array ) {
				$segments[ $array['id'] ] = $array['name'];
			}
		}

		return $segments;

	}

	/**
	 * Get segments HTML.
	 */
	public function get_segments_html( $audience_id = '' ) {
		?>
		<div class="ngl-metabox-flex ngl-metabox-segment">
			<div class="ngl-metabox-header">
				<label for="ngl_segment"><?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?></label>
				<?php echo $this->show_loading(); ?>
			</div>
			<div class="ngl-field">
				<?php
					$segment = '_everyone';

					newsletterglue_select_field( array(
						'id' 			=> 'ngl_segment',
						'legacy'		=> true,
						'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/audience/" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ),
						'options'		=> $this->get_segments( $audience_id ),
						'default'		=> $segment,
						'class'			=> 'ngl-ajax',
					) );
				?>
			</div>
		</div>
		<?php
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
		$audience		= isset( $data['audience'] ) ? $data['audience'] : $this->get_default_list_id();
		$segment		= isset( $data['segment'] ) && $data['segment'] && ( $data['segment'] != '_everyone' ) ? $data['segment'] : '';
		$schedule  	 	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';

		if ( $test ) {
			if ( $this->is_invalid_email( $data[ 'test_email' ] ) ) {
				return $this->is_invalid_email( $data[ 'test_email' ] );
			}
		}

		// API request.
		$this->api = new NGL_Mailchimp_API( $this->api_key );
		$this->api->verify_ssl = false;

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

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
				'fail'	=> __( 'Your <strong>From Email</strong> address isn&rsquo;t verified.', 'newsletter-glue' ) . '<br />' . '<a href="https://admin.mailchimp.com/account/domains/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a> <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
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
				'html'	=> newsletterglue_generate_content( $post, $subject, $this->app ),
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

				// Let's delete the campaign.
				$this->api->delete( 'campaigns/' . $campaign_id );

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
				'<a href="https://admin.mailchimp.com/account/domains/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>', '<a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">' . __( 'learn more.', 'newsletter-glue' ) . '</a>' );
				$output[ 'help' ]       = 'https://docs.newsletterglue.com/article/7-unverified-email';
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

		$this->api = new NGL_Mailchimp_API( $this->api_key );
		$this->api->verify_ssl = false;

		// Verify domain.
		$parts  = explode( '@', $email );
		$domain = isset( $parts[1] ) ? $parts[1] : '';

		$result = $this->api->get( 'verified-domains/' . $domain );

		if ( isset( $result['verified'] ) && $result['verified'] == true ) {

			$response = array(
				'success'	=> '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>',
			);

		} else {

			$response = array(
				'failed'			=> __( 'Not verified', 'newsletter-glue' ),
				'failed_details'	=> '<a href="https://admin.mailchimp.com/account/domains/" target="_blank">' . __( 'Verify email now', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a> <a href="https://docs.newsletterglue.com/article/7-unverified-email" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
			);

		}

		return $response;
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

		$this->api = new NGL_Mailchimp_API( $this->api_key );
		$this->api->verify_ssl = false;

		if ( isset( $name ) ) {
			$name_array = $array = explode( ' ', $name, 2 );
			$fname = $name_array[0];
			$lname = isset( $name_array[1] ) ? $name_array[1] : '';
		}

		$double_optin = isset( $double_optin ) && $double_optin == 'no' ? 'subscribed' : 'pending';

		$hash 		= $this->api::subscriberHash( $email );
		$batch		= $this->api->new_batch();
		
		if ( ! empty( $list_id ) ) {
			$batch->put( "op$list_id", "lists/$list_id/members/$hash", [
					'email_address' 	=> $email,
					'status'        	=> $double_optin,
					'status_if_new' 	=> $double_optin,
					'merge_fields' 	 	=> array(
						'FNAME'	=> $fname,
						'LNAME'	=> $lname
					),
			] );
		}

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$batch->put( "op$extra_list_id", "lists/$extra_list_id/members/$hash", [
					'email_address' 	=> $email,
					'status'        	=> $double_optin,
					'status_if_new' 	=> $double_optin,
					'merge_fields' 	 	=> array(
						'FNAME'	=> $fname,
						'LNAME'	=> $lname
					),
			] );
		}

		$batch->execute();

		$result = $batch->check_status();

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
			'helper'		=> '<a href="https://admin.mailchimp.com/account/api-key-popup/" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
		) );

	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		if ( ! defined( 'NGL_SEND_IN_PROGRESS' ) ) {
			return $html;
		}

		$html = str_replace( '{{ unsubscribe_link }}', '*|UNSUB|*', $html );
		$html = str_replace( '{{ list }}', '*|LIST:NAME|*', $html );
		$html = str_replace( '{{ address }}', '*|LIST:ADDRESS|*', $html );
		$html = str_replace( '{{ address_html }}', '*|HTML:LIST_ADDRESS_HTML|*', $html );
		$html = str_replace( '{{ rewards }}', '*|IF:REWARDS|* *|REWARDS|* *|END:IF|*', $html );

		return $html;
	}

}
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

	public $api_url = null;
	public $api_key = null;

	public $api = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/ActiveCampaign.class.php';

		$this->get_api_key();

	}

	/**
	 * Get API Key.
	 */
	public function get_api_key() {

		$integrations = get_option( 'newsletterglue_integrations' );
		$integration  = isset( $integrations[ 'activecampaign' ] ) ? $integrations[ 'activecampaign'] : '';

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
			$integrations 	 = get_option( 'newsletterglue_integrations' );
			$activecampaign  = isset( $integrations[ 'activecampaign' ] ) ? $integrations[ 'activecampaign'] : '';
			if ( isset( $activecampaign[ 'api_key'] ) ) {
				$api_key = $activecampaign[ 'api_key' ];
			}
			if ( isset( $activecampaign[ 'api_url'] ) ) {
				$api_url = $activecampaign[ 'api_url' ];
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
	 * Remove Integration.
	 */
	public function remove_integration() {
		$integrations = get_option( 'newsletterglue_integrations' );

		// Delete the integration.
		if ( isset( $integrations[ 'activecampaign' ] ) ) {
			unset( $integrations[ 'activecampaign' ] );
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
	public function save_integration( $api_key = '', $api_url = '', $account = array() ) {
		$integrations = get_option( 'newsletterglue_integrations' );

		$integrations[ 'activecampaign' ] = array();
		$integrations[ 'activecampaign' ][ 'api_key' ] 		= $api_key;
		$integrations[ 'activecampaign' ][ 'api_url' ] 		= $api_url;

		update_option( 'newsletterglue_integrations', $integrations );

		// Add default options.
		$globals = get_option( 'newsletterglue_options' );
		$options = ! empty( $globals ) && isset( $globals[ 'activecampaign' ] ) ? $globals[ 'activecampaign' ] : '';

		if ( ! $options ) {

			$globals[ 'activecampaign' ] = array(
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
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Immediately', 'newsletter-glue' ),
			'draft'			=> __( 'Save as draft in ActiveCampaign', 'newsletter-glue' ),
		);

		return $options;

	}

	/**
	 * Get form defaults.
	 */
	public function get_form_defaults() {

		$this->api = new ActiveCampaign( $this->api_url, $this->api_key );

		$defaults = array();

		$defaults[ 'lists' ] 	= $this->get_lists();
		$defaults[ 'segments' ] = $this->get_segments();

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
	 * Get segments.
	 */
	public function get_segments() {

		$_segments = array();

		$this->api->version( 2 );

		$segments = $this->api->api( 'segment/list_', array( 'sort' => 'name' ) );
		$segments = json_decode( $segments );

		if ( ! empty( $segments ) ) {
			foreach( $segments as $key => $data ) {
				$array = (array) $data;
				$id = @$array['id'];
				if ( $id ) {
					$_segments[ $id ] = @$array[ 'name' ];
				}
			}
		}

		return $_segments;

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
			$response = true;
		}

		return $response;

	}

	/**
	 * Set content type as HTML.
	 */
	public function wp_mail_content_type() {
		return 'text/html';
	}

	/**
	 * Show test email section.
	 */
	public function show_test_email( $settings, $defaults, $post ) {
		$this->test_column( $settings, $defaults, $post );
		?>
		<div class="ngl-metabox-flex no-padding">
			<div class="ngl-metabox-header">
			&nbsp;
			</div>
			<div class="ngl-field">
				<div class="ngl-action">
					<button class="ui primary button ngl-test-email ngl-is-default" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Send test now', 'newsletter-glue' ); ?></button>
					<button class="ui primary button ngl-test-email ngl-alt ngl-is-sending" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><i class="sync alternate icon"></i><?php esc_html_e( 'Sending...', 'newsletter-glue' ); ?></button>
					<button class="ui primary button ngl-test-email ngl-alt ngl-is-valid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Sent!', 'newsletter-glue' ); ?></button>
					<button class="ui primary button ngl-test-email ngl-alt ngl-is-invalid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Could not send', 'newsletter-glue' ); ?></button>
				</div>
				<div class="ngl-action-link is-hidden">
					<a href="#" class="ngl-link ngl-retest"><?php esc_html_e( 'Start again', 'newsletter-glue' ); ?></a>
				</div>
			</div>
			<div class="ngl-test-notice">
				<div class="ngl-test-notice-content"><?php _e( 'Sent by WordPress.<br />Might look slightly different to the final email sent to subscribers by ActiveCampaign.', 'newsletter-glue' ); ?></div>
				<div class="ngl-test-result ngl-is-valid is-hidden">

				</div>
				<div class="ngl-test-result ngl-is-invalid is-hidden">

				</div>
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

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$subject 	= isset( $data['subject'] ) ? $data['subject'] : '';
		$from_name	= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email	= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$lists		= isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? $data[ 'lists' ] : '';
		$segments	= isset( $data['segments'] ) && ! empty( $data['segments'] ) && is_array( $data['segments'] ) ? $data['segments'] : '';
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

			if ( ! is_email( $test_email ) ) {
				$response[ 'fail' ] = __( 'Please enter a valid email', 'newsletter-glue' );
				return $response;
			}

			add_filter( 'wp_mail_content_type', array( $this, 'wp_mail_content_type' ) );

			$body = newsletterglue_generate_content( $post, $subject, 'activecampaign' );

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
			'html'				=> newsletterglue_generate_content( $post, $subject, 'activecampaign' ),
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

}
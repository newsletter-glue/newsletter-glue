<?php
/**
 * Campaign Monitor.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'NGL_Abstract_Integration', false ) ) {
	include_once NGL_PLUGIN_DIR . 'includes/abstract-integration.php';
}

/**
 * Main Class.
 */
class NGL_Campaignmonitor extends NGL_Abstract_Integration {

	public $app 	= 'campaignmonitor';
	public $api_key = null;
	public $api 	= null;
	public $lists 	= array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Include needed files.
		include_once 'lib/csrest_general.php';
		include_once 'lib/csrest_campaigns.php';
		include_once 'lib/csrest_lists.php';
		include_once 'lib/csrest_clients.php';
		include_once 'lib/csrest_subscribers.php';

		$this->get_api_key();

		add_filter( 'newsletterglue_email_content_campaignmonitor', array( $this, 'newsletterglue_email_content_campaignmonitor' ), 10, 3 );

		add_action( 'newsletterglue_edit_more_settings', array( $this, 'newsletterglue_edit_more_settings' ), 50, 3 );

		add_filter( 'newsltterglue_campaignmonitor_html_content', array( $this, 'html_content' ), 10, 2 );
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
		$api_key = isset( $_POST['ngl_campaignmonitor_key'] ) ? sanitize_text_field( $_POST['ngl_campaignmonitor_key'] ) : '';

		// Test mode. no key provided.
		if ( ! $api_key ) {
			$integrations 		= get_option( 'newsletterglue_integrations' );
			$options    		= isset( $integrations[ $this->app ] ) ? $integrations[ $this->app] : '';
			if ( isset( $options[ 'api_key'] ) ) {
				$api_key = $options[ 'api_key' ];
			}
		}

		$this->api = new CS_REST_General( array( 'api_key' => $api_key ) );

		$api = $this->api->get_primary_contact();

		if ( isset( $api->response ) ) {
			$account = ( array ) $api->response;
		} else {
			$valid_account = false;
		}

		$valid_account = isset( $account ) && isset( $account[ 'EmailAddress' ] ) ? true : false;

		if ( ! $valid_account ) {

			$this->remove_integration();

			$result = array( 'response' => 'invalid' );

			delete_option( 'newsletterglue_campaignmonitor' );

		} else {

			// Get client ID.
			$clients = $this->api->get_clients();
			$client_data = isset( $clients->response ) ? (array) $clients->response : false;
			if ( $client_data ) {
				$account[ 'ClientID' ]  = $client_data[0]->ClientID;
				$account[ 'Name' ]		= $client_data[0]->Name;
			}

			$this->save_integration( $api_key, $account );

			$result = array( 'response' => 'successful' );

			update_option( 'newsletterglue_campaignmonitor', $account );

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
				'from_email'	=> isset( $account[ 'EmailAddress' ] ) ? $account[ 'EmailAddress' ] : '',
				'unsub'			=> $this->default_unsub(),
			);

			update_option( 'newsletterglue_options', $globals );

		}
	}

	/**
	 * Get a client ID.
	 */
	public function get_client_id() {

		$client = get_option( 'newsletterglue_campaignmonitor' );

		return isset( $client['ClientID'] ) ? $client['ClientID'] : false;
	}

	/**
	 * Get lists.
	 */
	public function get_lists() {
		$_lists = array();

		$lists = new CS_REST_Clients( $this->get_client_id(), array( 'api_key' => $this->api_key ) );
		$api   = $lists->get_lists();
		$resp  = (array) $api->response;

		foreach( $resp as $key => $data ) {
			$_lists[ $data->ListID ] = $data->Name;
		}

		$this->lists = $_lists;

		return $_lists;
	}

	/**
	 * Get segments.
	 */
	public function get_segments() {
		$_segments = array();

		$_segments = get_transient( 'newsletterglue_cs_segments' );

		// This request can be huge. cache to expire every hour.
		if ( ! $_segments ) {
			foreach( $this->lists as $list_id => $name ) {
				$request  = new CS_REST_Lists( $list_id, array( 'api_key' => $this->api_key ) );
				$api	  = $request->get_segments();
				$response = $api->response;
				foreach( $response as $key => $data ) {
					$_segments[ $data->SegmentID ] = $data->Title;
				}
			}
			set_transient( 'newsletterglue_cs_segments', $_segments, ( 60 * 60 ) );
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

		$post = get_post( $post_id );

		// If no data was provided. Get it from the post.
		if ( empty( $data ) ) {
			$data = get_post_meta( $post_id, '_newsletterglue', true );
		}

		$subject 		= isset( $data['subject'] ) ? urldecode( $data['subject'] ) : urldecode( $post->post_title );
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : newsletterglue_get_default_from_name();
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : $this->get_current_user_email();
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$lists			= isset( $data['lists'] ) && ! empty( $data['lists'] ) && is_array( $data['lists'] ) ? $data['lists'] : '';
		$segments		= isset( $data['segments'] ) && ! empty( $data['segments'] ) && is_array( $data['segments'] ) ? $data['segments'] : '';

		// Empty content.
		if ( $test && isset( $post->post_status ) && $post->post_status === 'auto-draft' ) {

			$response['fail'] = $this->nothing_to_send();

			return $response;
		}

		// Exit early if email is invalid.
		if ( $test ) {
			if ( ! is_email( $data['test_email'] ) ) {
				$response['fail'] = __( 'Please enter a valid email address to test your campaign.', 'newsletter-glue' );
				return $response;
			}
		}

		$api  = new CS_REST_Campaigns( '', array( 'api_key' => $this->api_key ) );

		// Create html file.
		$filename	= uniqid() . '.html';
		$uploaddir 	= wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;
		$htmlurl 	= $uploaddir['url'] . '/' . $filename;
		$handle 	= fopen( $uploadfile, 'w+' );
		fwrite( $handle, newsletterglue_generate_content( $post, $subject, $this->app ) );
		fclose( $handle );

		$campaign_info = array(
			'FromName'			=> $from_name,
			'FromEmail'			=> $from_email,
			'ReplyTo'			=> $from_email,
			'Name'				=> sprintf( __( 'Newsletter Glue - Campaign %s', 'newsletter-glue' ), uniqid() ),
			'Subject'			=> $subject,
			'HtmlUrl'			=> $htmlurl,
		);

		// Add segments and/or lists.
		if ( $segments ) {
			$campaign_info[ 'SegmentIDs' ] = $segments;
		} else if ( $lists ) {
			$campaign_info[ 'ListIDs' ] = $lists;
		} else {
			// Add default lists.
			if ( $test ) {
				$lists = $this->get_lists();
				$lists = array_keys( $lists );
				$campaign_info[ 'ListIDs' ] = $lists;
			}
		}

		// Create a campaign.
		$campaign = $api->create( $this->get_client_id(), $campaign_info );

		$resp = (array) $campaign->response;

		// Errors.
		if ( isset( $resp['Code'] ) ) {

			if ( ! $test ) {

				wp_delete_file( $uploadfile );

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $resp ) );

			} else {

				if ( $resp['Code'] == 310 ) {
					$response = array(
						'fail'		=> __( 'HTML content has to be valid and served via a remote URL.', 'newsletter-glue' ),
					);
				}

				if ( $resp['Code'] == 4202 ) {
					$response = array(
						'fail'		=> __( 'Email content contains JavaScript. Please remove it and try again.', 'newsletter-glue' ),
					);
				}

				wp_delete_file( $uploadfile );

				return $response;

			}

		} else {

			$campaign_id = $resp[0];
			$api  = new CS_REST_Campaigns( $campaign_id, array( 'api_key' => $this->api_key ) );

			if ( ! $test ) {

				if ( $schedule === 'draft' ) {

					$result = array(
						'status' 	=> 'draft'
					);

				}

				if ( $schedule === 'immediately' ) {

					$result = array(
						'status'	=> 'success',
					);

					$schedule_options = array(
						'ConfirmationEmail'		=> $from_email,
						'SendDate'				=> 'immediately',
					);

					$api_send = $api->send( $schedule_options );

				}

				newsletterglue_add_campaign_data( $post_id, $subject, $this->prepare_message( $result ), $campaign_id );

				wp_delete_file( $uploadfile );

				return $result;

			} else {

				// Test when campaign is created.
				$response = array();

				$api_send_preview = $api->send_preview( array( $data['test_email'] ) );

				$send_resp = (array) $api_send_preview->response;

				if ( isset( $send_resp['Code'] ) ) {
					$response['fail'] = __( 'Please enter a valid email address to test your campaign.', 'newsletter-glue' );
				} else {
					$response['success'] = $this->get_test_success_msg();
				}

				// When used for testing, delete the campaign.
				$api->delete();

				wp_delete_file( $uploadfile );

				return $response;

			}

		}

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
			if ( $result['status'] == 'success' ) {
				$output[ 'status' ] 	= 200;
				$output[ 'type'   ] 	= 'success';
				$output[ 'message' ] 	= __( 'Sent', 'newsletter-glue' );
			}
		}

		if ( isset( $result['Code'] ) ) {
			if ( $result['Code'] == 303 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Duplicate Campaign Name', 'newsletter-glue' );
			}
			if ( $result['Code'] == 304 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Subject Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 305 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'From Name Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 307 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Invalid From Email Address', 'newsletter-glue' );
			}
			if ( $result['Code'] == 308 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Reply-To Address Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 310 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'HTML Content URL Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 315 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'List IDs or Segments Required', 'newsletter-glue' );
			}
			if ( $result['Code'] == 319 ) {
				$output[ 'type' ] 		= 'error';
				$output[ 'message' ] 	= __( 'Campaign Name Required', 'newsletter-glue' );
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

		$user = array(
			'Name'										=> ! empty( $name ) ? $name : '',
			'EmailAddress'								=> ! empty( $email ) ? $email : '',
			'ConsentToTrack'							=> 'yes',
			'Resubscribe'								=> true,
			'RestartSubscriptionBasedAutoResponders'	=> true,
		);

		if ( $email && ! empty( $list_id ) ) {
			$api 	= new CS_REST_Subscribers( $list_id, array( 'api_key' => $this->api_key ) );
			$result = $api->add( $user );
		}

		if ( $email && isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$api 	= new CS_REST_Subscribers( $extra_list_id, array( 'api_key' => $this->api_key ) );
			$result = $api->add( $user );
		}

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
			'helper'		=> '<a href="https://help.campaignmonitor.com/api-keys" target="_blank">' . __( 'Get API key', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>',
		) );

	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_campaignmonitor( $content, $post, $subject ) {

		if ( strstr( $content, '{{ unsubscribe_link }}' ) ) {
			return $content;
		}

		$post_id		= $post->ID;
		$data 			= get_post_meta( $post_id, '_newsletterglue', true );
		$default_unsub  = $this->default_unsub();
		$unsub		 	= ! empty( $data[ 'unsub' ] ) ? $data[ 'unsub' ] : $default_unsub;

		if ( empty( $unsub ) ) {
			$unsub = $this->default_unsub();
		}

		$content .= '<p class="ngl-unsubscribe">' . wp_kses_post( $unsub ) . '</p>';

		return $content;

	}

	/**
	 * Default unsub.
	 */
	public function default_unsub() {
		return '<a href="{{ unsubscribe_link }}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a> to stop receiving these emails.';
	}

	/**
	 * Add extra settings to metabox.
	 */
	public function newsletterglue_edit_more_settings( $app, $settings, $ajax = false ) {
		if ( $app === $this->app ) {

			$default_unsub = $this->default_unsub();
			$unsub = ! empty( $settings->unsub ) ? $settings->unsub : newsletterglue_get_option( 'unsub', $app );

			?>
			<div class="ngl-metabox-flexfull">
				<div class="ngl-metabox-flex">
					<div class="ngl-metabox-flex">
						<div class="ngl-metabox-header">
							<label for="ngl_unsub"><?php esc_html_e( 'Edit unsubscribe message', 'newsletter-glue' ); ?></label>
							<div class="ngl-label-verification">
								<a href="#" class="ngl-textarea-append" data-selector="ngl_unsub" data-value="<?php echo esc_html(  '<a href="{{ unsubscribe_link }}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a>' ); ?>"><?php _e( 'Insert unsubscribe tag', 'newsletter-glue' ); ?></a>
							</div>
							<div class="ngl-label-more">
								<a href="#" class="ngl-textarea-reset" data-selector="ngl_unsub"><?php _e( 'Reset', 'newsletter-glue' ); ?></a>
							</div>
						</div>
						<div class="ngl-field">
							<textarea name="ngl_unsub" id="ngl_unsub" data-default="<?php echo esc_html( $default_unsub ); ?>"><?php echo stripslashes_deep( $unsub ); ?></textarea>
						</div>
					</div>
					<div class="ngl-metabox-flex">

					</div>
				</div>
			</div>
			<?php
		}
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
		return 'https://help.campaignmonitor.com/permission-settings';
	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		$output = new simple_html_dom();
		$output->load( $html, true, false );

		// Author.
		$replace = '#template_inner a';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( $element->href === '{{ unsubscribe_link }}' ) {
				$element->outertext = '<unsubscribe>' . wp_strip_all_tags( $element->innertext ) . '</unsubscribe>';
			}
		}

		$output->save();

		$html = ( string ) $output;

		$html = str_replace( '{{ unsubscribe_link }}', '<unsubscribe>' . __( 'Unsubscribe', 'newsletter-glue' ) . '</unsubscribe>', $html );

		$html = $this->convert_tags( $html, $post_id );

		return $html;
	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0 ) {

		switch ( $tag ) {

		}

		return false;
	}

}
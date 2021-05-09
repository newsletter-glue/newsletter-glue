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

		add_filter( 'newsletterglue_email_content_sendy', array( $this, 'newsletterglue_email_content_sendy' ), 10, 3 );

		add_action( 'newsletterglue_edit_more_settings', array( $this, 'newsletterglue_edit_more_settings' ), 50, 3 );

		add_filter( 'newsltterglue_sendy_html_content', array( $this, 'html_content' ), 10, 2 );
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
				'unsub'			=> $this->default_unsub(),
				'track_opens'	=> 1,
				'track_clicks'	=> 1,
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

		$subject 		= isset( $data['subject'] ) ? $data['subject'] : '';
		$from_name		= isset( $data['from_name'] ) ? $data['from_name'] : '';
		$from_email		= isset( $data['from_email'] ) ? $data['from_email'] : '';
		$lists			= ! empty( $data['lists'] ) ? $data['lists'] : '';
		$brand			= ! empty( $data['brand'] ) ? $data['brand'] : '';
		$schedule   	= isset( $data['schedule'] ) ? $data['schedule'] : 'immediately';
		$track_opens 	= ! empty( $data[ 'track_opens' ] ) ? absint( $data[ 'track_opens' ] ) : 0;
		$track_clicks 	= ! empty( $data[ 'track_clicks' ] ) ? absint( $data[ 'track_clicks' ] ) : 0;

		$post 		= get_post( $post_id );
		$html_text 	= newsletterglue_generate_content( $post, $subject, $this->app );

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

			$body = $html_text;

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
			'html_text'		=> $html_text,
			'plain_text'	=> wp_strip_all_tags( $html_text ), 
			'send_campaign'	=> ( $schedule === 'immediately' ) ? 1 : 0,
			'track_opens'	=> $track_opens,
			'track_clicks'	=> $track_clicks,
		);

		if ( $schedule != 'immediately' ) {
			$args[ 'brand_id' ] = ( $brand ) ? $brand : 1;
		} else {
			$args[ 'list_ids' ] = $lists;
		}

		$campaign = $this->api->post( '/api/campaigns/create.php', $args );

		if ( ! $campaign || ( is_string( $campaign ) && strstr( $campaign, 'Forbidden' ) ) )  {
			$args[ 'html_text' ] = str_replace( 'xmlns="http://www.w3.org/1999/xhtml"', '', $html_text );
			$args[ 'html_text' ] = str_replace( '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', '', $args[ 'html_text' ] );
			$campaign = $this->api->post( '/api/campaigns/create.php', $args );
		}

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

	/**
	 * Get settings.
	 */
	public function get_settings() {
		$settings = new stdclass;

		$settings->unsub 		= newsletterglue_get_option( 'unsub', $this->app );

		$settings->track_clicks = newsletterglue_get_option( 'track_clicks', $this->app );
		$settings->track_opens  = newsletterglue_get_option( 'track_opens', $this->app );

		return $settings;
	}

	/**
	 * Customize content.
	 */
	public function newsletterglue_email_content_sendy( $content, $post, $subject ) {

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

		$unsub = str_replace( '{{ unsubscribe_link }}', '[unsubscribe]', $unsub );

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

			$track_clicks = isset( $settings->track_clicks ) ? $settings->track_clicks : newsletterglue_get_option( 'track_clicks', $app );
			$track_opens  = isset( $settings->track_opens ) ? $settings->track_opens : newsletterglue_get_option( 'track_opens', $app );
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
						<div class="ngl-metabox-header">
							<?php esc_html_e( 'Sendy tracking', 'newsletter-glue' ); ?>
							<?php $this->input_verification_info(); ?>
						</div>
						<div class="ngl-field">
							<div class="ngl-field ngl-tabbed-check">
								<div class="ngl-label-sub"><?php _e( 'Track clicks:', 'newsletter-glue' ); ?></div>
								<input type="text" class="ngl-value-hidden" name="ngl_track_clicks" id="ngl_track_clicks" value="<?php echo absint( $track_clicks ); ?>" />
								<div class="ui basic buttons">
								  <div class="ui button <?php echo $track_clicks == 1 ? 'active' : ''; ?>" data-value="1"><i class="check icon"></i><?php _e( 'Yes', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_clicks == 0 ? 'active' : ''; ?>" data-value="0"><i class="close icon"></i><?php _e( 'No', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_clicks == 2 ? 'active' : ''; ?>" data-value="2"><i class="check icon"></i><?php _e( 'Anonymously', 'newsletter-glue' ); ?></div>
								</div>
							</div>
							<div class="ngl-field ngl-tabbed-check">
								<div class="ngl-label-sub"><?php _e( 'Track opens:', 'newsletter-glue' ); ?></div>
								<input type="text" class="ngl-value-hidden" name="ngl_track_opens" id="ngl_track_opens" value="<?php echo absint( $track_opens ); ?>" />
								<div class="ui basic buttons">
								  <div class="ui button <?php echo $track_opens == 1 ? 'active' : ''; ?>" data-value="1"><i class="check icon"></i><?php _e( 'Yes', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_opens == 0 ? 'active' : ''; ?>" data-value="0"><i class="close icon"></i><?php _e( 'No', 'newsletter-glue' ); ?></div>
								  <div class="ui button <?php echo $track_opens == 2 ? 'active' : ''; ?>" data-value="2"><i class="check icon"></i><?php _e( 'Anonymously', 'newsletter-glue' ); ?></div>
								</div>
							</div>
						</div>
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
		return 'https://sendy.co/forum/discussion/13226/how-to-verify-email-addresses-in-ses/p1';
	}

	/**
	 * Add user to this ESP.
	 */
	public function add_user( $data ) {
		extract( $data );

		if ( empty( $email ) ) {
			return -1;
		}

		$name = ! empty( $name ) ? $name : '';

		$this->api = new NGL_Sendy_API( untrailingslashit( $this->api_url ), $this->api_key );

		if ( ! empty( $list_id ) ) {
			$args = array(
				'api_key'	=> $this->api_key,
				'name'		=> $name,
				'email'		=> $email,
				'list'		=> $list_id
			);

			$subscribe = $this->api->post( '/subscribe', $args );
		}

		if ( isset( $extra_list ) && ! empty( $extra_list_id ) ) {
			$args = array(
				'api_key'	=> $this->api_key,
				'name'		=> $name,
				'email'		=> $email,
				'list'		=> $extra_list_id
			);

			$subscribe = $this->api->post( '/subscribe', $args );
		}

		return true;

	}

	/**
	 * Replace universal tags with esp tags.
	 */
	public function html_content( $html, $post_id ) {

		if ( ! defined( 'NGL_SEND_IN_PROGRESS' ) ) {
			return $html;
		}

		$html = str_replace( '{{ unsubscribe_link }}', '[unsubscribe]', $html );

		return $html;
	}

}
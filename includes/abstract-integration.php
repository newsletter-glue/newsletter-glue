<?php
/**
 * Form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NGL_Abstract_Integration class.
 */
abstract class NGL_Abstract_Integration {

	public $app = '';

	/**
	 * Get settings.
	 */
	public function get_settings() {

		$settings = new stdclass;

		return $settings;

	}

	/**
	 * Set content type as HTML.
	 */
	public function wp_mail_content_type() {
		return 'text/html';
	}

	/**
	 * Display settings.
	 */
	public function show_settings( $settings, $defaults, $post ) {
		$this->show_from_options( $settings, $defaults, $post );
		$this->show_test_email( $settings, $defaults, $post );
		$this->show_schedule_and_image_options( $settings, $defaults, $post );
	}

	/**
	 * Last onboarding screen.
	 */
	public function load_last_onboarding_screen() {
		$app = $this->app;
		include NGL_PLUGIN_DIR . 'includes/admin/onboarding/views/last-step.php';
	}

	/**
	 * Show test email options.
	 */
	public function show_test_email( $settings, $defaults, $post ) {
		$this->test_column( $settings, $defaults, $post );
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-test.php';
	}

	/**
	 * Show test column.
	 */
	public function test_column( $settings, $defaults, $post ) {
		?>
		<div class="ngl-metabox-flex">

			<div class="ngl-metabox-flex">

				<div class="ngl-metabox-header">
					<label for="ngl_test_email"><?php esc_html_e( 'Send test email', 'newsletter-glue' ); ?></label>
				</div>

				<div class="ngl-field">
					<?php
						newsletterglue_text_field( array(
							'id' 			=> 'ngl_test_email',
							'value'			=> isset( $settings->test_email ) ? $settings->test_email : $defaults->test_email,
						) );
					?>
					<div class="ngl-action">
						<button class="ui primary button ngl-test-email ngl-is-default" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Send', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-sending" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><i class="sync alternate icon"></i><?php esc_html_e( 'Sending', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-valid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Sent!', 'newsletter-glue' ); ?></button>
						<button class="ui primary button ngl-test-email ngl-alt ngl-is-invalid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Could not send', 'newsletter-glue' ); ?></button>
					</div>
				</div>

			</div>

			<div class="ngl-metabox-flex">
				<div class="ngl-metabox-flex-link">
					<a href="<?php echo add_query_arg( 'preview_email', $post->ID, get_preview_post_link() ); ?>" target="_blank" class="ngl-email-preview-button"><?php _e( 'Preview email in browser', 'newsletter-glue' ); ?><span>(<?php _e( 'opens in new tab', 'newsletter-glue' ); ?>)</span></a>
				</div>
			</div>

		</div>
		<?php
	}

	/**
	 * Show subject.
	 */
	public function show_subject( $settings, $defaults, $post ) {
		global $post_type;

		if ( $post_type === 'ngl_pattern' ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/subject-line.php';
	}

	/**
	 * Show from name/email options.
	 */
	public function show_from_options( $settings, $defaults, $post ) {
		global $post_type;

		if ( $post_type === 'ngl_pattern' ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-from-settings.php';
	}

	/**
	 * Show email verification info.
	 */
	public function email_verification_info() {
		if ( $this->has_email_verify() ) :
		?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Verifying...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><i class="check circle icon"></i></span>
				<span class="ngl-process-text"></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><i class="exclamation circle icon"></i></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
		<?php
		else :
		?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><i class="check circle icon"></i></span>
				<span class="ngl-process-text"><?php _e( 'Saved', 'newsletter-glue' ); ?></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><i class="exclamation circle icon"></i></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
		<?php
		endif;
	}

	/**
	 * Show input verification info.
	 */
	public function input_verification_info() {
		?>
		<div class="ngl-label-verification">
			<span class="ngl-process ngl-ajax is-hidden is-waiting">
				<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-valid">
				<span class="ngl-process-icon"><i class="check circle icon"></i></span>
				<span class="ngl-process-text"><?php _e( 'Saved', 'newsletter-glue' ); ?></span>
			</span>

			<span class="ngl-process ngl-ajax is-hidden is-invalid">
				<span class="ngl-process-icon"><i class="exclamation circle icon"></i></span>
				<span class="ngl-process-text"></span>
			</span>
		</div>
		<div class="ngl-label-more">

		</div>
		<?php
	}

	/**
	 * Show schedule / header image options.
	 */
	public function show_schedule_and_image_options( $settings, $defaults, $post ) {
		global $post_type;

		if ( $post_type === 'ngl_pattern' ) {
			return;
		}

		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-options.php';
	}

	/**
	 * Show loading state.
	 */
	public function show_loading() {

		$html = '<div class="ngl-metabox-loading">
			<span class="ngl-metabox-spinner"><img src="' . admin_url( '/images/spinner.gif' ) . '" alt=""></span>
		</div>';

		return $html;

	}

	/**
	 * Nothing to send message.
	 */
	public function nothing_to_send() {
		return __( 'Whoops! There&rsquo;s nothing to send.<br />Please save post as draft first.', 'newsletter-glue' );
	}

	/**
	 * Test success.
	 */
	public function get_test_success_msg() {
		return '&nbsp;';
	}

	/**
	 * Returns true if test emails are sent by WordPress.
	 */
	public function test_email_by_wordpress() {
		return false;
	}

	/**
	 * Verify email address.
	 */
	public function verify_email( $email = '' ) {

		$email = trim( $email );

		if ( ! $email ) {
			$response = array( 'failed' => __( 'Please enter email', 'newsletter-glue' ) );
		} elseif ( ! is_email( $email ) ) {
			$response = array( 'failed'	=> __( 'Invalid email', 'newsletter-glue' ) );
		} else {
			$response = array( 'success'=> '<strong>' . __( 'Verified', 'newsletter-glue' ) . '</strong>' );
		}

		return $response;

	}

	/**
	 * Has email verify.
	 */
	public function has_email_verify() {
		return true;
	}

	/**
	 * Check email address.
	 */
	public function is_invalid_email( $email = '' ) {
		$response = array();

		if ( empty( $email ) ) {
			$response[ 'fail' ] = __( 'Please enter email', 'newsletter-glue' );
		} elseif ( ! is_email( $email ) ) {
			$response[ 'fail' ] = __( 'Invalid email', 'newsletter-glue' );
		}

		if ( ! empty( $response[ 'fail' ] ) ) {
			return $response;
		}

		return false;

	}

	/**
	 * Get connect settings.
	 */
	public function get_connect_settings( $integrations = array() ) {

	}

	/**
	 * Get schedule options.
	 */
	public function get_schedule_options() {

		$options = array(
			'immediately'	=> __( 'Send now', 'newsletter-glue' ),
			'draft'			=> sprintf( __( 'Save as draft in %s', 'newsletter-glue' ), newsletterglue_get_name( $this->app ) ),
		);

		return $options;

	}

	/**
	 * Remove Integration.
	 */
	public function remove_integration() {

		delete_option( 'newsletterglue_integrations' );

		$response = array( 'successful' => true );

		return $response;
	}

	/**
	 * Get current user email.
	 */
	public function get_current_user_email() {
		global $current_user;
		return $current_user->user_email;
	}

	/**
	 * Convert merge tags in HTML.
	 */
	public function convert_tags( $html, $post_id = 0 ) {

		preg_match_all( "/{{(.*?)}}/", $html, $matches );

		if ( ! empty( $matches[0] ) ) {
			$results = $matches[0];
			foreach( $results as $result ) {
				$clean = explode( ',fallback=', $result );
				$tag = trim( str_replace( array( '{{', '}}' ), '', $clean[0] ) );

				if ( isset( $clean[1] ) ) {
					$fallback = str_replace( ' }}', '', $clean[1] );
					$fallback = trim( $fallback );
				} else {
					$fallback = '';
				}

				// Find the tag in ESP list first.
				if ( $this->get_tag( $tag, $post_id, $fallback ) && ! isset( $_REQUEST[ 'view_newsletter' ] ) && ! isset( $_REQUEST[ 'preview_email' ] ) ) {
					$html = str_replace( $result, $this->get_tag( $tag, $post_id, $fallback ), $html );
				} else if ( $this->get_global_tag( $tag, $post_id ) ) {
					$html = str_replace( $result, $this->get_global_tag( $tag, $post_id ), $html );
				// If the ESP does not support it. Add fallback.
				} else {
					if ( isset( $clean[1] ) ) {
						$string = str_replace( ' }}', '', $clean[1] );
						$string = trim( $string );
						$html = str_replace( $result, $string, $html );
					} else {
						$html = str_replace( $clean[0], '', $html );
					}
				}
			}
		}

		return $html;

	}

	/**
	 * Code supported tags for this ESP.
	 */
	public function get_tag( $tag, $post_id = 0, $fallback = null ) {
		return false;
	}

	/**
	 * Global merge tags.
	 */
	public function get_global_tag( $tag, $post_id = 0 ) {

		switch( $tag ) {
			case 'webversion' :
				return newsletterglue_generate_web_link( $post_id );
			break;
			case 'blog_post' :
				return get_permalink( $post_id );
			break;
		}

		return false;
	}

	/**
	 * Get lists compat.
	 */
	public function _get_lists_compat() {
		return null;
	}

}
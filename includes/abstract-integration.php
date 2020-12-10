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

	/**
	 * Display general settings.
	 */
	public function show_global_settings() {
		include NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-general.php';
	}

	/**
	 * Show send as newsletter checkbox.
	 */
	public function show_send_option() {
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-as-newsletter.php';
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
					<?php esc_html_e( 'Send test email to', 'newsletter-glue' ); ?>
				</div>

				<div class="ngl-field">
					<?php
						newsletterglue_text_field( array(
							'id' 			=> 'ngl_test_email',
							'value'			=> isset( $settings->test_email ) ? $settings->test_email : $defaults->test_email,
						) );
					?>
				</div>

			</div>

			<div class="ngl-metabox-flex">

				<div class="ngl-metabox-header">
					<?php esc_html_e( 'Preview email in new tab', 'newsletter-glue' ); ?>
				</div>

				<div class="ngl-field">
					<a href="<?php echo add_query_arg( 'preview_newsletterglue_email', $post->ID ); ?>" target="_blank" class="ui button secondary"><?php _e( 'Preview email', 'newsletter-glue' ); ?></a>
				</div>

			</div>

		</div>
		<?php
	}

	/**
	 * Show subject.
	 */
	public function show_subject( $settings, $defaults, $post ) {
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/subject-line.php';
	}

	/**
	 * Show from name/email options.
	 */
	public function show_from_options( $settings, $defaults, $post ) {
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-from-settings.php';
	}

	/**
	 * Show schedule / header image options.
	 */
	public function show_schedule_and_image_options( $settings, $defaults, $post ) {
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/send-options.php';
	}

	/**
	 * Show states.
	 */
	public function show_states( $post ) {
		include NGL_PLUGIN_DIR . 'includes/admin/metabox/views/messages.php';
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

		$message = __( 'Your email is on its way!<br />Check your inbox in 3-5 minutes.', 'newsletter-glue' ) 
		. '<br /><span style="color:rgba(0, 0, 0, 0.6) !important;">' . sprintf( __( 'Can&rsquo;t find your email? %s', 'newsletter-glue' ), '<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank">' . __( 'Get help', 'newsletter-glue' ) . '</a>' ) . '.</span>';

		return $message;

	}

}
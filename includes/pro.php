<?php
/**
 * Pro.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * NGL_Pro class.
 */
class NGL_Pro {

	public $id 			= 'newsletterglue_pro_license';
	public $item_id 	= 1266;
	public $item_name 	= 'Newsletter Glue Pro';

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->includes();

		if ( class_exists( 'NGL_License' ) ) {
			$this->init_license();
		}

		// Add setting tab.
		add_filter( 'newsletterglue_settings_tabs', array( $this, 'add_tab' ), 20 );
		add_filter( 'newsletterglue_settings_tab_license_save_button', '__return_false' );
		add_action( 'newsletterglue_settings_tab_license', array( $this, 'show_settings' ), 20 );

		// AJAX functions.
		add_action( 'wp_ajax_newsletterglue_check_license', array( $this, 'check_license' ) );
		add_action( 'wp_ajax_nopriv_newsletterglue_check_license', array( $this, 'check_license' ) );

		add_action( 'wp_ajax_newsletterglue_deactivate_license', array( $this, 'deactivate_license' ) );
		add_action( 'wp_ajax_nopriv_newsletterglue_deactivate_license', array( $this, 'deactivate_license' ) );

	}

	/**
	 * Init license.
	 */
	public function init_license() {
		$ngl_license = new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
	}

	/**
	 * Includes.
	 */
	public function includes() {
		require_once NGL_PLUGIN_DIR . 'includes/libraries/license-handler.php';
	}

	/**
	 * Add tab.
	 */
	public function add_tab( $tabs ) {

		foreach( $tabs as $key => $value ) {
			$new_tabs[ $key ] = $value;
			if ( $key == 'css' ) {
				$new_tabs[ 'license' ] = __( 'Pro License', 'newsletter-glue' );
			}
		}

		return $new_tabs;

	}

	/**
	 * Check if it has valid license.
	 */
	public function has_valid_license() {
		return get_option( $this->id ) ? true : false;
	}

	/**
	 * Show tab.
	 */
	public function show_settings() {
	?>
		<div class="ui large header">
			<?php esc_html_e( 'Pro License', 'newsletter-glue' ); ?>
			<div class="sub header"><?php echo __( 'Add your Newsletter Glue Pro license here to receive updates.', 'newsletter-glue' ); ?></div>
		</div>

		<div class="ngl-cards">

			<div class="ngl-card ngl-card-license">

				<!-- License form -->
				<div class="ngl-card-add2 ngl-card-license-form <?php if ( $this->has_valid_license() ) echo 'ngl-hidden'; ?>">
					<div class="ngl-card-heading"><?php _e( 'Newsletter Glue Pro', 'newsletter-glue' ); ?></div>
					<div class="ngl-misc-fields">
						<form action="" method="post" class="ngl-license-form">

							<?php
								newsletterglue_text_field( array(
									'id' 			=> $this->id,
									'label'			=> __( 'License key', 'newsletter-glue' ),
									'helper'		=> '<a href="https://newsletterglue.com/pricing/" target="_blank">' . __( 'Get license key', 'newsletter-glue' ) . '</a>',
									'value'			=> get_option( $this->id ),
								) );
							?>

							<div class="ngl-btn">
								<button class="ui primary button" type="submit"><i class="sync alternate icon"></i><?php esc_html_e( 'Activate', 'newsletter-glue' ); ?></button>
							</div>

						</form>
					</div>
				</div>

				<div class="ngl-card-view <?php if ( ! $this->has_valid_license() ) echo 'ngl-hidden'; ?>">

					<div class="ngl-card-heading"><?php _e( 'Newsletter Glue Pro', 'newsletter-glue' ); ?></div>

					<div class="ngl-btn">
						<button class="ui primary button ngl-ajax-test-connection"><i class="sync alternate icon"></i><?php esc_html_e( 'test', 'newsletter-glue' ); ?></button>
					</div>

					<div class="ngl-helper">
						<a href="#" class="ngl-ajax-edit-connection"><i class="pencil alternate icon"></i><?php echo __( 'edit', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-ajax-remove-connection"><i class="trash alternate icon"></i><?php echo __( 'deactivate', 'newsletter-glue' ); ?></a>
					</div>

				</div>

				<!-- Testing connection -->
				<div class="ngl-card-state is-testing ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="sync alternate icon"></i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Verifying license...', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-stop-test"><?php echo __( 'Stop verification', 'newsletter-glue' ); ?></a>
					</div>
				</div>

				<!-- Connection working -->
				<div class="ngl-card-state is-working ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="check circle icon"></i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Activated!', 'newsletter-glue' ); ?></div>
					</div>
				</div>

				<!-- Connection not working -->
				<div class="ngl-card-state is-invalid ngl-hidden">
					<div class="ngl-card-link-start is-right">
						<a href="#" class="ui basic noborder button ngl-ajax-test-close"><i class="times circle outline icon"></i><?php esc_html_e( 'Close', 'newsletter-glue' ); ?></a>
					</div>
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="material-icons">error_outline</i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Not connected', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-test-again"><?php echo __( 'Test again', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-ajax-edit-connection"><?php echo __( 'Edit license details', 'newsletter-glue' ); ?></a>
					</div>
					<div class="ngl-card-link-end">
						<a href="mailto:support@newsletterglue.com" class="ui basic noborder button" target="_blank"><i class="question circle outline icon"></i><?php esc_html_e( 'Get help', 'newsletter-glue' ); ?></a>
					</div>
				</div>

				<!-- Connection removed -->
				<div class="ngl-card-state is-removed ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="material-icons">delete_forever</i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'License deactivated', 'newsletter-glue' ); ?></div>
					</div>
				</div>

				<!-- Remove connection -->
				<div class="ngl-card-state confirm-remove ngl-hidden">
					<div class="ngl-card-state-wrap">
						<div class="ngl-card-state-icon"><i class="trash alternate icon"></i></div>
						<div class="ngl-card-state-text"><?php esc_html_e( 'Deactivate license?', 'newsletter-glue' ); ?></div>
					</div>
					<div class="ngl-card-state-alt ngl-helper">
						<a href="#" class="ngl-ajax-remove ngl-helper-alert"><?php echo __( 'Confirm', 'newsletter-glue' ); ?></a>
						<a href="#" class="ngl-back"><?php echo __( 'Go back', 'newsletter-glue' ); ?></a>
					</div>
				</div>

			</div>

		</div>
	<?php
	}

	/**
	 * Check license.
	 */
	public function check_license() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		foreach( $_POST as $key => $value ) {
			if ( strstr( $key, '_license' ) ) {
				$id = $key;
			}
		}

		if ( ! isset( $id ) || ! class_exists( 'NGL_License' ) ) {
			wp_die( -1 );
		}

		$code 			= isset( $_POST[ $id ] ) ? sanitize_text_field( $_POST[ $id ] ) : '';
		$ngl_license 	= new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
		$result			= $ngl_license->_activate( $code );

		// Deactivate current license.
		$current_code 	= get_option( $this->id );
		if ( trim( $current_code ) !== $code ) {
			$ngl_license->_deactivate( $current_code );
		}

		$this->save_license( $code, $result );

		wp_send_json( $result );

	}

	/**
	 * Check license.
	 */
	public function deactivate_license() {

		check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			wp_die( -1 );
		}

		if ( ! class_exists( 'NGL_License' ) ) {
			wp_die( -1 );
		}

		$current_code 	= get_option( $this->id );
		$ngl_license 	= new NGL_License( $this->id, NGL_VERSION, $this->item_id, $this->item_name, NGL_PLUGIN_FILE );
		$ngl_license->_deactivate( $current_code );

		delete_option( $this->id );
		delete_option( $this->id . '_expires' );

		wp_die();
	}

	/**
	 * Save license.
	 */
	public function save_license( $code, $result ) {

		delete_option( $this->id );
		delete_option( $this->id . '_expires' );

		if ( isset( $result[ 'status' ] ) ) {

			if ( $result[ 'status' ] === 'valid' ) {
				update_option( $this->id, $code );
				update_option( $this->id . '_expires', $result[ 'expires' ] );
			}

		}

	}

}

return new NGL_Pro;
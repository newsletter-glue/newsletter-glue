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

		// Admin notice.
		add_action( 'admin_notices', array( $this, 'admin_notice' ), 120 );

		// Social embeds.
		add_filter( 'newsletterglue_generate_content', array( $this, 'social_embeds' ), 100, 2 );

		// Custom CSS.
		add_action( 'newsletterglue_email_styles', array( $this, 'embed_css' ), 50 );
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
									'helper'		=> '<a href="https://newsletterglue.com/account/" target="_blank">' . __( 'Get license key', 'newsletter-glue' ) . '</a>',
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

	/**
	 * Show admin notice.
	 */
	public function admin_notice() {
		if ( $this->has_valid_license() ) {
			return;
		}

		if ( get_user_meta( get_current_user_id(), '_ngl_remove_license_notice', true ) ) {
			return;
		}
		?>
		<div class="ngl-notice welcome notice is-dismissible" data-key="license_notice">
			<p class="ngl-notice-logo"><?php _e( 'Thanks for purchasing Newsletter Glue! Get frequent updates as we improve the plugin.', 'newsletter-glue' ); ?></p>
			<p><a href="<?php echo admin_url( 'admin.php?page=ngl-settings&tab=license' ); ?>"><?php _e( 'Add license key to get updates', 'newsletter-glue' ); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="https://newsletterglue.com/account"><?php _e( 'Get license key', 'newsletter-glue' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Display social embeds.
	 */
	public function social_embeds( $html, $post ) {

		$dom = new \DOMDocument();

		libxml_use_internal_errors( true );

		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8') );

		libxml_clear_errors();

		$finder = new \DOMXPath( $dom );

		$supports = array(
			'twitter',
			'youtube',
			'soundcloud',
			'spotify',
			'reddit',
		);

		foreach( $supports as $support ) {
			$nodes = $finder->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' is-provider-" . $support . " ')]" );
			if ( ! empty( $nodes ) ) {
				foreach ( $nodes as $node ) {
					$html = call_user_func_array( array( $this, 'get_' . $support ), array( $node->nodeValue ) );
					if ( $html ) {
						$newdiv = $dom->createElement( 'div', $html );
						$newdiv->setAttribute( 'class', 'ngl-embed-social ngl-embed-' . $support );
						$node->parentNode->replaceChild( $newdiv, $node );
					} else {
						$node->parentNode->removeChild( $node );
					}
				}
			}
		}

		return htmlspecialchars_decode( $dom->saveHTML() );

	}

	/**
	 * Get spotify.
	 */
	public function get_spotify( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://open.spotify.com/oembed?url=' . $url );
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		return $url;

	}

	/**
	 * Get reddit.
	 */
	public function get_reddit( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://www.reddit.com/oembed?url=' . $url );
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', ( string ) trim( $data->html ) );

		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank">' . esc_html( $data->title ) . '</a>
					</div>
					<div class="ngl-embed-icon">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . NGL_PLUGIN_URL . '/assets/images/social/soundcloud.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Get soundcloud.
	 */
	public function get_soundcloud( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://soundcloud.com/oembed?format=json&url=' . $url );
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$html = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . $data->thumbnail_url . '" class="ngl-embed-soundcloud-thumb" /></a>';

		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank">' . esc_html( $data->title ) . '</a><br />
						' . $data->description . '<br />' . $data->author_name . '<br />' . $data->author_url . '
					</div>
					<div class="ngl-embed-icon">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . NGL_PLUGIN_URL . '/assets/images/social/soundcloud.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Get youtube.
	 */
	public function get_youtube( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://www.youtube.com/oembed?url=' . $url );
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data ) ) {
			return false;
		}

		$image_url = str_replace( 'hqdefault', 'maxresdefault', $data->thumbnail_url );

		$html = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . $image_url . '" class="ngl-embed-youtube-thumb" /></a>';

		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank">' . $data->title . '</a>
					</div>
					<div class="ngl-embed-icon">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . NGL_PLUGIN_URL . '/assets/images/social/youtube.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Get tweet.
	 */
	public function get_twitter( $url ) {

		$url = urlencode( untrailingslashit( trim( $url ) ) );

		$request  = wp_remote_get( 'https://publish.twitter.com/oembed?omit_script=true&url=' . $url );
		$response = wp_remote_retrieve_body( $request );

		$data = json_decode( $response );

		if ( empty( $data->html ) ) {
			return false;
		}

		$html = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', ( string ) trim( $data->html ) );
		$html = str_replace( 'blockquote', 'div', trim( $html ) );

		$stripped = preg_replace( '/<p\b[^>]*>(.*?)<\/p>/i', '', $html );
		preg_match( '#<a(.*?)</a>#i', $stripped, $match );
		$date = wp_strip_all_tags( $match[0] );
		$formatted_date = '<a href="' . urldecode( trim( $url ) ) . '" target="_blank">' . date_i18n( 'M j, Y', strtotime( $date ) ) . '</a>';

		preg_match( '%(<p[^>]*>.*?</p>)%i', $html, $regs );
		$html = $regs[0];

		if ( preg_match("/^https?:\/\/(www\.)?twitter\.com\/(#!\/)?(?<name>[^\/]+)(\/\w+)*$/", $data->author_url, $regs ) ) {
			$username = '<a href="' . $data->author_url . '" target="_blank">@' . $regs[ 'name' ] . '</a>';
		} else {
			$username = '<a href="' . $data->author_url . '" target="_blank">' . $data->author_url . '</a>';
		}

		$html .= '<div class="ngl-embed-date">' . $formatted_date . '</div>';
		$html .= '<div class="ngl-embed-meta">
					<div class="ngl-embed-metadata">
						<strong>' . $data->author_name . '</strong><br>
						' . $username . '
					</div>
					<div class="ngl-embed-icon">
						<a href="' . urldecode( trim( $url ) ) . '" target="_blank"><img src="' . NGL_PLUGIN_URL . '/assets/images/social/twitter.png" /></a>
					</div>
				</div>';

		return $html;

	}

	/**
	 * Embed CSS.
	 */
	public function embed_css() {
		?>
		.ngl-embed-social {
			background: #fff !important;
			box-shadow: 0 1px 2px #aaa;
			border-radius: 5px;
			padding: 20px;
			font-size: 13px;
			line-height: 1.4;
			margin: 0 0 25px;
		}

		.ngl-embed-social p {
			line-height: 1.4;
			font-size: 14px;
			color: #111 !important;
		}

		.ngl-embed-social a {
			color: rgb(27, 149, 224) !important;
		}

		.ngl-embed-meta {
			display: flex;
			border-top: 1px solid rgb(204, 214, 221);
			line-height: 1.4;
			padding: 12px 0 0;
			margin: 12px 0 0;
		}

		.ngl-embed-metadata {
			display: inline-block;
			text-align: left;
		}

		.ngl-embed-metadata strong {
			font-weight: bold;
			color: #111;
		}

		.ngl-embed-icon {
			display: inline-block;
			text-align: right;
			margin-left: auto;
			min-width: 30px;
		}

		.ngl-embed-icon img {
			width: 30px !important;
			height: 30px !important;
			margin: 0 !important;
		}

		.ngl-embed-date,
		.ngl-embed-date a {
			font-size: 12px;
			color: rgb(91, 112, 131) !important;
			text-decoration: none !important;
		}

		.ngl-embed-twitter {
			background: #fff !important;
			border: 1px solid rgb(204, 214, 221);
			box-shadow: none !important;
			color: #111 !important;
		}

		.ngl-embed-twitter p {
			margin: 0 0 8px !important;
		}

		.ngl-embed-twitter .ngl-embed-metadata a {
			color: rgb(91, 112, 131) !important;
			text-decoration: none !important;
		}

		.ngl-embed-youtube {
			padding: 0;
		}

		.ngl-embed-youtube a {
			color: #ff0000 !important;
			text-decoration: none !important;
		}

		.ngl-embed-youtube-thumb {
			margin: 0 !important;
			border-radius: 5px 5px 0 0 !important;
		}

		.ngl-embed-youtube .ngl-embed-meta {
			margin: 0 !important;
			border: none;
			padding: 20px;
		}

		.ngl-embed-youtube .ngl-embed-metadata {
			margin-right: 50px;
		}

		.ngl-embed-youtube .ngl-embed-metadata a {
			color: #333 !important;
		}
		<?php
	}

}

return new NGL_Pro;
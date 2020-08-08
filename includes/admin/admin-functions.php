<?php
/**
 * Admin Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin menu links.
 */
function newsletterglue_get_screen_ids() {
	$screen_ids = array();
	$screen_id  = sanitize_title( __( 'Newsletter Glue', 'newsletter-glue' ) );

	$screen_ids[] = 'post';
	$screen_ids[] = 'edit-post';
	$screen_ids[] = $screen_id . '_page_ngl-connect';
	$screen_ids[] = $screen_id . '_page_ngl-settings';

	return apply_filters( 'newsletterglue_screen_ids', $screen_ids );
}

/**
 * Add mopinion embed code.
 */
function newsletterglue_add_mopinion_code() {
	?>
	<!-- Mopinion Pastea.se  start --><script type="text/javascript">(function(){var id="qfjb9r1n398ymi9lw63cip5v3q5grhfbr45";var js=document.createElement("script");js.setAttribute("type","text/javascript");js.setAttribute("src","//deploy.mopinion.com/js/pastease.js");document.getElementsByTagName("head")[0].appendChild(js);var t=setInterval(function(){try{new Pastease.load(id);clearInterval(t)}catch(e){}},50)})();</script><!-- Mopinion Pastea.se end -->
	<?php
}
add_action( 'admin_footer', 'newsletterglue_add_mopinion_code' );

/**
 * Check if the plugin has no active api.
 */
function newsletterglue_has_no_active_api( $selected = '' ) {

	$apis = get_option( 'newsletterglue_integrations' );

	if ( empty( $apis ) ) {
		return true;
	}

	return false;

}

/**
 * Get default API connection.
 */
function newsletterglue_default_connection() {

	$apis = get_option( 'newsletterglue_integrations' );

	if ( empty( $apis ) ) {
		return false;
	}

	$apis = array_keys( $apis );

	return $apis[0];
}

/**
 * Generate email template content from post and subject.
 */
function newsletterglue_generate_content( $post, $subject, $app = '' ) {

	$html = newsletterglue_get_email_template( $post, $subject, $app );

	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

	// This is email content.
	$the_content = '<h1>' . $subject . '</h1>';
	$the_content .= apply_filters( 'the_content', $post->post_content );

	// If credits can be displayed.
	if ( get_option( 'newsletterglue_credits' ) ) {
		$the_content .= '<p class="ngl-credits">' . sprintf( __( 'Seamlessly sent by %s', 'newsletter-glue' ), '<a href="https://memberhero.pro/newsletterglue/">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ) . '</p>';
	}

	// Add special app text.
	if ( $app == 'mailerlite' ) {
		$the_content .= '<p class="ngl-credits"><a href="{$unsubscribe}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a></p>';
	}

	// Process content.
	$html = str_replace( '{content}', $the_content, $html );
	$html = str_replace( '{custom_css}', get_option( 'newsletterglue_css' ), $html );
	$html = preg_replace( '/<!--(.*)-->/Uis', '', $html );

	return apply_filters( 'newsletterglue_generate_content', $html, $post );
}

/**
 * Get email template html.
 */
function newsletterglue_get_email_template( $post, $subject, $app ) {
	ob_start();

	include( NGL_PLUGIN_DIR . 'includes/admin/views/email-styles.php' );

	return ob_get_clean();	
}

/**
 * Add deactivate modal layout.
 */
function newsletterglue_deactivate_modal() {
	global $pagenow;

	if ( 'plugins.php' !== $pagenow ) {
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/deactivate.php';

}
add_action( 'admin_footer', 'newsletterglue_deactivate_modal' );

/**
 * Send feedback regarding new connections.
 */
function newsletterglue_feedback_modal() {
	global $pagenow;

	if ( 'admin.php' !== $pagenow ) {
		return;
	}

	if ( ! isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'ngl-connect' ) {
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/feedback.php';

}
add_action( 'admin_footer', 'newsletterglue_feedback_modal' );
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

$html = '<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><style type="text/css">

body {
	-webkit-text-size-adjust: 100%;
	line-height: 1.5;
}

h1, h2, h3, h4, h5, h6 {
	margin: 0 0 30px;
	padding-top: 20px;
	line-height: 1.5;
}

h1 { font-size: 32px; }
h2 { font-size: 28px; }
h3 { font-size: 24px; }
h4 { font-size: 22px; }
h5 { font-size: 20px; }
h6 { font-size: 18px; }

p, ul, ol {
	margin: 0 0 30px;
	font-size: 18px;
	line-height: 1.5;
}

figure {
	margin: 0 0 30px;
}

img {
	margin: 0 auto 30px auto;
	border-radius: 6px;
	display: block;
	max-width: 100%;
}

ul.blocks-gallery-grid {
	list-style-type: none;
}

.wp-block-columns {

}

.wp-block-columns .wp-block-column {
	display: inline-block;
	width: 45%;
	vertical-align: top;
	padding-right: 2%;
}

p.ngl-credits {
	font-size: 13px;
	text-align: center;
	color: #999;
	padding-top: 70px;
}

p.ngl-credits a {
	color: #999;
	text-decoration: underline;
}

.wp-block-button__link {
	padding: 12px 24px;
	font-size: 16px;
	text-decoration: none;
}

.wp-block-button__link.has-vivid-cyan-blue-background-color { background-color: #0693e3; }

@media only screen and (max-width:596px) {

	body {
		line-height: 1.6;
	}

	h1, h2, h3, h4, h5, h6 {
		line-height: 1.6;
	}

	h1 { font-size: 28px; }
	h2 { font-size: 24px; }
	h3 { font-size: 22px; }
	h4 { font-size: 20px; }
	h5 { font-size: 18px; }
	h6 { font-size: 16px; }

	p, ul, ol {
		font-size: 16px;
		line-height: 1.6;
	}

}

{custom_css}

</style></head><body>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="template_wrapper">
	<tr>
		<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container">
				<tr>
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
							<tr>
								<td></td>
								<td width="600">
								{content}
								</td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body></html>';

	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

	// This is email content.
	$the_content = '<h1>' . $subject . '</h1>';
	$the_content .= apply_filters( 'the_content', $post->post_content );

	$credits = get_option( 'newsletterglue_credits' );

	if ( $credits ) {
		$the_content .= '<p class="ngl-credits">' . sprintf( __( 'Seamlessly sent by %s', 'newsletter-glue' ), '<a href="https://memberhero.pro/newsletterglue/">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ) . '</p>';
	}

	// Add special apps content.
	if ( $app == 'mailerlite' ) {
		$the_content .= '<p class="ngl-credits"><a href="{$unsubscribe}">' . __( 'Unsubscribe', 'newsletter-glue' ) . '</a></p>';
	}

	$html = str_replace( '{content}', $the_content, $html );
	// End email content.

	$html = str_replace( '{custom_css}', get_option( 'newsletterglue_css' ), $html );

	$html = preg_replace( '/<!--(.*)-->/Uis', '', $html );

	return apply_filters( 'newsletterglue_generate_content', $html, $post );
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
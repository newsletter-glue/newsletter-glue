<?php
/**
 * Misc Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Global hooks.
 */
add_filter( 'newsletterglue_settings_tab_blocks_save_button', '__return_false' );
add_filter( 'newsletterglue_settings_tab_connect_save_button', '__return_false' );

/**
 * Creates a preview for emails.
 */
function newsletterglue_preview_emails() {

	if ( ! empty( $_GET['preview_email'] ) ) {

		if ( ! current_user_can( 'manage_newsletterglue' ) ) {
			return;
		}

		$post_id = absint( $_GET[ 'preview_email' ] );

		$test = get_post( $post_id );
		if ( ! isset( $test->ID ) ) {
			return;
		}

		ob_start();

		$data = get_post_meta( $test->ID, '_newsletterglue', true );

		$app = isset( $data[ 'app' ] ) ? $data[ 'app' ] : '';

		if ( $app ) {
			include_once newsletterglue_get_path( $app ) . '/init.php';
			$classname = 'NGL_' . ucfirst( $app );
			$api = new $classname();
		}

		echo newsletterglue_generate_content( $post_id, ! empty( $data[ 'subject' ] ) ? $data[ 'subject' ] : '', $app );

		$message = ob_get_clean();

		echo $message;

		// Debug
		if ( isset( $_REQUEST[ 'showsize' ] ) ) {
			echo '<center><br /><br /><small>Estimated email size in kilobytes: ' . round( strlen( $message ) / 1024 ) . 'kb' . '</small><br /><br /></center>';
		}

		exit;

	}

}
add_action( 'init', 'newsletterglue_preview_emails', 1000 );

/**
 * View newsletter in web.
 */
function newsletterglue_view_in_web() {

	if ( ! empty( $_GET['view_newsletter'] ) ) {

		$post_id = ! empty( $_GET[ 'id' ] ) ? absint( $_GET[ 'id' ] ) : 0;
		$token   = $_GET[ 'view_newsletter' ];

		$test = get_post( $post_id );
		if ( ! isset( $test->ID ) ) {
			return;
		}

		$current_token = get_post_meta( $test->ID, '_newsletterglue_token', true );
		if ( $token !== $current_token ) {
			return;
		}

		ob_start();

		$data = get_post_meta( $test->ID, '_newsletterglue', true );

		$app = isset( $data[ 'app' ] ) ? $data[ 'app' ] : '';

		if ( $app ) {
			include_once newsletterglue_get_path( $app ) . '/init.php';
			$classname = 'NGL_' . ucfirst( $app );
			$api = new $classname();
		}

		echo newsletterglue_generate_content( $post_id, ! empty( $data[ 'subject' ] ) ? $data[ 'subject' ] : '', $app );

		$message = ob_get_clean();

		echo $message;

		exit;

	}

}
add_action( 'init', 'newsletterglue_view_in_web', 1000 );

/**
 * Generate web link for a post ID.
 */
function newsletterglue_generate_web_link( $post_id = 0 ) {

	// Get token.
	$token = get_post_meta( $post_id, '_newsletterglue_token', true );
	if ( ! $token ) {
		$token = bin2hex( random_bytes( 16 ) );
		update_post_meta( $post_id, '_newsletterglue_token', $token );
	}

	$view_in_web = add_query_arg( 'view_newsletter', $token, home_url() );
	$view_in_web = add_query_arg( 'id', $post_id, $view_in_web );

	return $view_in_web;

}

/**
 * Checks if post is scheduled.
 */
function newsletterglue_is_post_scheduled( $post_id ) {
	return get_post_meta( $post_id, '_ngl_future_send', true ) ? true : false;
}

/**
 * Returns true if free version is being used.
 */
function newsletterglue_is_free_version() {

	$plugin_data = get_plugin_data( NGL_PLUGIN_FILE );

	if ( isset( $plugin_data[ 'Name' ] ) ) {
		if ( stristr( $plugin_data[ 'Name' ], 'PRO' ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Send newsletter when post is finally published.
 */
function newsletterglue_publish_future_post( $post_id ) {

	$has_newsletter = get_post_meta( $post_id, '_ngl_future_send', true );

	if ( $has_newsletter ) {

		newsletterglue_send( $post_id );

		delete_post_meta( $post_id, '_ngl_future_send' );

	}

}
add_action( 'publish_future_post', 'newsletterglue_publish_future_post' );

/**
 * Send the newsletter and mark as sent.
 */
function newsletterglue_send( $post_id = 0, $test = false ) {

	$response = null;

	$post = get_post( $post_id );
	$data = get_post_meta( $post_id, '_newsletterglue', true );

	if ( ! $test ) {
		$data[ 'sent' ] = true;
	}

	update_post_meta( $post_id, '_newsletterglue', $data );

	$app = $data[ 'app' ];

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname = 'NGL_' . ucfirst( $app );

	$api = new $classname();

	// Send the newsletter.
	$response = $api->send_newsletter( $post_id, $data, $test );

	return $response;
}

/**
 * Mark a newsletter as unsent.
 */
function newsletterglue_reset_newsletter( $post_id = 0 ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	// Allow campaign to be resent.
	if ( isset( $data[ 'sent' ] ) ) {
		unset( $data[ 'sent' ] );
	}

	update_post_meta( $post_id, '_newsletterglue', $data );

	delete_post_meta( $post_id, '_ngl_future_send' );

	// campaigns.
	$campaigns = get_post_meta( $post_id, '_ngl_results', true );
	if ( $campaigns && is_array( $campaigns ) ) {
		foreach( $campaigns as $key => $item ) {
			if ( isset( $item[ 'type' ] ) && $item[ 'type' ] == 'schedule' ) {
				unset( $campaigns[ $key ] );
			}
		}
	}
	update_post_meta( $post_id, '_ngl_results', $campaigns );
}

/**
 * Get form defaults.
 */
function newsletterglue_get_form_defaults( $post = 0, $api = '' ) {

	$defaults = new stdclass;

	// Subject.
	if ( $post->post_status === 'auto-draft' ) {
		$subject = '';
	} else {
		$subject = get_the_title( $post->ID );
	}

	$app = newsletterglue_default_connection();

	$defaults->from_name		= newsletterglue_get_option( 'from_name', $app );
	$defaults->from_email 		= newsletterglue_get_option( 'from_email', $app );
	$defaults->test_email		= newsletterglue_get_option( 'from_email', $app );
	$defaults->subject     		= $subject;
	$defaults->add_featured 	= get_option( 'newsletterglue_add_featured' );
	$defaults->preview_text		= '';

	// Get options from API.
	if ( method_exists( $api, 'get_form_defaults' ) ) {

		$api_options = $api->get_form_defaults();

		foreach( $api_options as $key => $value ) {

			$defaults->{$key} = $value;

		}

	}

	return $defaults;
}

/**
 * Save newsletter options as meta data.
 */
function newsletterglue_save_data( $post_id, $data ) {

	$meta = array();

	$old_meta = get_post_meta( $post_id, '_newsletterglue', true );

	if ( isset( $old_meta ) && ! empty( $old_meta[ 'sent' ] ) ) {
		$meta[ 'sent' ] = true;
	}

	foreach( $data as $key => $value ) {
		if ( strstr( $key, 'ngl_' ) ) {
			$key = str_replace( 'ngl_', '', $key );
			$meta[ $key ] = $value;

			if ( $key === 'when' ) {
				$timestamp = strtotime( $value );
				$meta[ 'timestamp' ] = $timestamp;
			}
		}
	}

	if ( ! isset( $meta[ 'add_featured' ] ) ) {
		$meta[ 'add_featured' ] = 0;
	}

	if ( isset( $meta ) && ! empty( $meta ) ) {
		if ( empty( $meta[ 'brand' ] ) ) {
			$meta[ 'brand' ] = '';
		}
		if ( empty( $meta[ 'lists' ] ) ) {
			$meta[ 'lists' ] = '';
		}
		if ( empty( $meta[ 'groups' ] ) ) {
			$meta[ 'groups' ] = '';
		}
		if ( empty( $meta[ 'segments' ] ) ) {
			$meta[ 'segments' ] = '';
		}
		if ( empty( $meta[ 'track_opens' ] ) ) {
			$meta[ 'track_opens' ] = 0;
		}
		if ( empty( $meta[ 'track_clicks' ] ) ) {
			$meta[ 'track_clicks' ] = 0;
		}
		update_post_meta( $post_id, '_newsletterglue', $meta );
	}

	// Set token.
	$token = get_post_meta( $post_id, '_newsletterglue_token', true );
	if ( ! $token ) {
		$token = bin2hex( random_bytes( 16 ) );
		update_post_meta( $post_id, '_newsletterglue_token', $token );
	}

}

/**
 * Get newsletter options as meta data.
 */
function newsletterglue_get_data( $post_id ) {

	$data = get_post_meta( $post_id, '_newsletterglue', true );

	$s = new stdclass;

	if ( is_array( $data ) ) {
		foreach( $data as $key => $value ) {
			$s->{$key} = $value;
		}
	}

	return $s;

}

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
 * Add title to newsletter.
 */
function newsletterglue_add_title( $title, $post ) {
	if ( isset( $post->post_type ) && $post->post_type == 'ngl_pattern' ) {
		return false;
	}

	return '<h1 class="title">' . $title . '</h1>';
}


/**
 * Fix the content.
 */
function newsletterglue_fix_the_content( $content ) {

	$post_id = get_the_ID();

	$content = str_replace( trailingslashit( admin_url() ) . '%7B%7B%20', '{{ ', $content );
	$content = str_replace( untrailingslashit( admin_url() ) . '%7B%7B%20', '{{ ', $content );
	$content = str_replace( 'http://%7B%7B%20', '{{ ', $content );
	$content = str_replace( 'https://%7B%7B%20', '{{ ', $content );
	$content = str_replace( '%20%7D%7D/', ' }}', $content );
	$content = str_replace( '%20%7D%7D', ' }}', $content );

	if ( ! empty( $post_id ) ) {
		include_once NGL_PLUGIN_DIR . 'includes/integrations/core/init.php';
		$api 		= new NGL_Integration_Core;
		$content 	= $api->convert_tags( $content, $post_id );
	}

	return $content;
}
add_filter( 'the_content', 'newsletterglue_fix_the_content', 1 );

/**
 * Generate email template content from post and subject.
 */
function newsletterglue_generate_content( $post = '', $subject = '', $app = '' ) {

	global $ng_post;

	// If post ID is provided.
	if ( is_numeric( $post ) ) {
		$post_id = $post;
		$post    = get_post( $post_id );
	}

	// No subject.
	if ( empty( $subject ) ) {
		$subject = $post->post_title;
	}

	$ng_post = $post;

	// This is intended for email.
	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		define( 'NGL_IN_EMAIL', true );
	}

	$post_type = isset( $post->post_type ) ? $post->post_type : '';

	$data 			= get_post_meta( $post->ID, '_newsletterglue', true );
	$preview_text 	= isset( $data[ 'preview_text' ] ) ? esc_attr( $data[ 'preview_text' ] ) : '';

	$position = get_option( 'newsletterglue_position_featured' );
	if ( ! $position ) {
		$position = 'below';
	}

	// Blog title.
	$show_title 	= get_option( 'newsletterglue_add_title' );
	$link_to_post 	= get_option( 'newsletterglue_link_title' );

	// Blog logo.
	$show_logo		= get_option( 'newsletterglue_add_logo' );

	// Remove auto embed.
	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

	$the_content = '';

	// Add preview text to email.
	if ( ! empty( $preview_text ) ) {
		$the_content .= '<div class="ngl-preview-text">' . $preview_text . '</div>';
	}

	// Add logo.
	if ( $show_logo !== 'no' ) {
		$the_content .= newsletterglue_add_logo();
	}

	$title = isset( $post ) && isset( $post->post_title ) ? $post->post_title : $subject;

	// Link to title.
	if ( $link_to_post === 'yes' ) {
		$title = '<a href="' . get_permalink( $post->ID ) . '" class="ngl-title-to-post" target="_blank">' . $title . '</a>';
	}

	// Masthead and heading
	if ( $position == 'above' ) {
		$the_content .= newsletterglue_add_masthead_image( $post, 'above' );
		if ( $show_title !== 'no' ) {
			$the_content .= newsletterglue_add_title( $title, $post );
		}
	} else {
		if ( $show_title !== 'no' ) {
			$the_content .= newsletterglue_add_title( $title, $post );
		}
		$the_content .= newsletterglue_add_masthead_image( $post, 'below' );
	}

	// Post content.
	$the_post_content = do_shortcode( $post->post_content );
	$the_post_content = do_blocks( $the_post_content );
	$the_post_content = wpautop( $the_post_content );
	$the_content .= $the_post_content;

	// Credits.
	if ( get_option( 'newsletterglue_credits' ) && $post_type != 'ngl_pattern' ) {
		$the_content .= '<p class="ngl-credits">' . sprintf( __( 'Built with %s', 'newsletter-glue' ), '<a href="https://newsletterglue.com/?utm_source=newsletter&utm_medium=ng-signature" target="_blank">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ) . '</p>';
	}

	// Allow 3rd party to customize content tag.
	if ( $post_type != 'ngl_pattern' ) {
		$the_content = apply_filters( 'newsletterglue_email_content_' . $app, $the_content, $post, $subject );
	}

	$the_content = apply_filters( 'newsletterglue_email_content', $the_content, $post, $subject, $app );

	// Get the email template including css tags.
	$html = newsletterglue_get_email_template( $post, $subject, $app );

	// Process content tags.
	$html = str_replace( '{{ title }}', $subject, $html );
	$html = str_replace( '{{ content }}', $the_content, $html );
	$html = str_replace( 'http://{{', '{{', $html );
	$html = str_replace( 'https://{{', '{{', $html );

	// Filter for original content. before email work.
	$html = apply_filters( 'newsletterglue_generate_content', $html, $post );

	// Email compatible emails.
	$html = apply_filters( 'newsletterglue_generated_html_output', $html, $post->ID, $app );

	// Emogrify process.
	$emogrifier_class = '\\Pelago\\Emogrifier';
	if ( ! class_exists( $emogrifier_class ) ) {
		include_once NGL_PLUGIN_DIR . 'includes/libraries/class-emogrifier.php';
	}
	try {
		$emogrifier = new $emogrifier_class( $html );
		$html    	= $emogrifier->emogrify();
	} catch ( Exception $e ) {

	}

	$html = str_replace( array( '%7B', '%7D', '%24', '%5B', '%5D', '*%7C', '%7C*' ), array( '{', '}', '$', '[', ']', '*|', '|*' ), $html );
	$html = str_replace( '@media only screen and (max-width:642px) {', 'p.ngl-unsubscribe a { color: #707070 !important; text-decoration: underline; } a, #template_inner td a { color: ' . newsletterglue_get_theme_option( 'a_colour' ) . '; } @media only screen and (max-width:642px) {' . "\r\n", $html );
	$html = wp_encode_emoji( $html );
	$html = str_replace( '{{%20', '{{ ', $html );
	$html = str_replace( '%20}}', ' }}', $html );
	$html = str_replace( trailingslashit( admin_url() ) . '{{', '{{', $html );
	$html = str_replace( untrailingslashit( admin_url() ) . '{{', '{{', $html );

	// ESP html filter.
	$html = apply_filters( "newsltterglue_{$app}_html_content", $html, $post->ID );

	return apply_filters( 'newsletterglue_final_html_content', $html );
}

/**
 * After all emogrify is done.
 */
add_filter( 'newsletterglue_final_html_content', 'newsletterglue_final_html_content', 10, 1 );
function newsletterglue_final_html_content( $html ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
		} else {
		$font = "Arial, Helvetica, sans-serif";
	}

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Outlook rect for button.
	$replace = 'a.wp-block-button__link';
	foreach( $output->find( $replace ) as $key => $element ) {
		$s = $element->style;
		$results = [];
		$styles = explode(';', $s);

		foreach ($styles as $style) {
			$properties = explode(':', $style);
			if (2 === count($properties)) {
				$results[trim($properties[0])] = trim($properties[1]);
			}
		}

		$color 		= ! empty( $results[ 'color' ] ) ? $results[ 'color' ] : 'inherit';
		$background = ! empty( $results[ 'background-color' ] ) ? $results[ 'background-color' ] : '#0088A0';
		$font_size  = newsletterglue_get_theme_option( 'p_size' ) . 'px';
		$innertext  = wp_strip_all_tags( $element->innertext );
		$href		= $element->href;
		$length     = ( mb_strlen( $innertext ) * 10 ) + 25;
		$radius		= ! empty( $results[ 'border-radius' ] ) ? $results[ 'border-radius' ] : '0px';
		$radius		= str_replace( 'px', '', $radius );
		$radius		= $radius * 2 . '%';

		if ( strstr( $element->class, 'is-style-outlined' ) ) {
			if ( $element->style ) {
				$element->style = $element->style . 'color: ' . $background . ' !important;';
			}
		}

		if ( strstr( $element->class, 'is-style-outlined' ) ) {

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
									<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:49px; " arcsize="' . $radius . '" strokecolor="' . $color . '" strokeweight="1pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
									<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;">' . $innertext.  '</center></v:textbox>
									</v:roundrect>
									<![endif]-->' . $element->outertext;
		} else {

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
									<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:49px; " arcsize="' . $radius . '" strokecolor="' . $background . '" strokeweight="0pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
									<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;">' . $innertext.  '</center></v:textbox>
									</v:roundrect>
									<![endif]-->' . $element->outertext;
		}

	}

	// Remove extra padding.
	$replace = 'td.ngl-callout-content table tr td';
	$ret = $output->find( $replace, -1 );
	if ( $ret && $ret->style ) {
		$ret->style = str_replace( '0 0 10px;', '0;', $ret->style );
	}

	// Author bio button.
	$replace = 'div.ngl-author-cta a';
	foreach( $output->find( $replace ) as $key => $element ) {
		$s = $element->style;
		$results = [];
		$styles = explode(';', $s);

		foreach ($styles as $style) {
			$properties = explode(':', $style);
			if (2 === count($properties)) {
				$results[trim($properties[0])] = trim($properties[1]);
			}
		}

		$color 		= ! empty( $results[ 'color' ] ) ? $results[ 'color' ] : '#ffffff';
		$background = ! empty( $results[ 'background-color' ] ) ? $results[ 'background-color' ] : '#0088A0';
		$font_size  = '12px';
		$innertext  = wp_strip_all_tags( $element->innertext );
		$href		= $element->href;
		$length     = ( mb_strlen( $innertext ) * 10 ) + 25;
		$radius		= ! empty( $results[ 'border-radius' ] ) ? $results[ 'border-radius' ] : '0px';
		$radius		= str_replace( 'px', '', $radius );
		$radius		= $radius * 2 . '%';

		if ( ! empty( $results[ 'border-width' ] ) && $results[ 'border-width' ] == '2px' ) {

			if ( isset( $results[ 'border-color' ] ) ) {
				$strokecolor = $results[ 'border-color' ];
			} else {
				$strokecolor = $color;
			}

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
										<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:30px; " arcsize="' . $radius . '" strokecolor="' . $strokecolor . '" strokeweight="1pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
										<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;">' . $innertext.  '</center></v:textbox>
										</v:roundrect>
										<![endif]-->' . $element->outertext;
		} else {

			$element->innertext = '<span style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . ';">' . $element->innertext . '</span>';
			$element->outertext = '<!--[if mso]>
										<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $href . '" style="v-text-anchor:middle; width: ' . $length . 'px; height:30px; " arcsize="' . $radius . '" strokecolor="' . $background . '" strokeweight="0pt" fillcolor="' . $background . '" o:button="true" o:allowincell="true" o:allowoverlap="false">
										<v:textbox inset="2px,2px,2px,2px"><center style="font-family: ' . $font . '; color: ' . $color . '; font-size: ' . $font_size . '; line-height: 1.1;">' . $innertext.  '</center></v:textbox>
										</v:roundrect>
										<![endif]-->' . $element->outertext;
		}
	}

	// Fix weird markup.
	$replace = '.wp-block-image table figure table';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'img' ) ) {
		$theimage = $element->find( 'img', 0 )->outertext;
		$element->outertext = '';
		if ( $element->parent()->find( 'a' ) ) {
			$element->parent()->find( 'a', 0 )->innertext = $theimage;
		} else {
			$element->parent()->innertext = $theimage;
		}
		}
	}

	// Remove unwanted class junk.
	$replace = 'table.ngl-table, #template_inner img, table.ngl-table-callout, table.wp-block-newsletterglue-callout, td.ngl-callout-content, a.ngl-metadata-permalink, .wp-block-button__link, div.wp-block-buttons, .wp-block-button, h1.title, p.has-drop-cap, p.has-text-color, a.logo, tr.root-tr, .has-inline-color, .has-text-align-left, .has-text-align-center, .ngl-social-link, .ngl-share-description';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( ! strstr( $element->class, 'ngl-table-ngl-unsubscribe' ) && ! strstr( $element->class, 'logo-image' ) ) {
			$element->class = '';
			$element->removeAttribute( 'class' );
			$element->removeAttribute( 'alt' );
			$element->removeAttribute( 'data-gap' );
			$element->removeAttribute( 'data-href' );
			$element->removeAttribute( 'data-align' );
			$element->removeAttribute( 'data-boxed-gap' );
		}
	}

	$output->save();

	return ( string ) $output;
}


/**
 * After all emogrify is done.
 */
add_filter( 'newsletterglue_final_html_content', 'newsletterglue_final_html_content2', 20, 1 );
function newsletterglue_final_html_content2( $html ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Do not include the wp block image div.
	$replace = '.wp-block-image';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Remove unwanted group block.
	$replace = '.wp-block-newsletterglue-group';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Remove unwanted class junk.
	$replace = 'table.ngl-table, #template_inner img, table.ngl-table-callout, table.wp-block-newsletterglue-callout, td.ngl-callout-content, a.ngl-metadata-permalink, .wp-block-button__link, div.wp-block-buttons, .wp-block-button, h1.title, p.has-drop-cap, p.has-text-color, a.logo, tr.root-tr, .has-inline-color, .has-text-align-left, .has-text-align-center, .ngl-social-link, .ngl-share-description';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( ! strstr( $element->class, 'ngl-table-ngl-unsubscribe' ) && ! strstr( $element->class, 'logo-image' ) ) {
			$element->class = '';
			$element->removeAttribute( 'class' );
			$element->removeAttribute( 'alt' );
			$element->removeAttribute( 'data-gap' );
			$element->removeAttribute( 'data-href' );
			$element->removeAttribute( 'data-align' );
			$element->removeAttribute( 'data-boxed-gap' );
		}
	}

	$output->save();

	return ( string ) $output;
}

/**
 * Fix most email client issues here.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook1', 1, 3 );
function newsletterglue_generated_html_output_hook1( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Fix border-radius.
	$replace = '.is-style-rounded';
	foreach( $output->find( $replace ) as $key => $element ) {
		foreach( $element->find( 'img' ) as $a => $b ) {
			$b->style = $b->style . 'border-radius: 999px !important;';
		}
	}

	// Spacers.
	$replace = '.wp-block-spacer';
	foreach( $output->find( $replace ) as $key => $element ) {
		$s = $element->style;
		$results = [];
		$styles = explode(';', $s);

		foreach ($styles as $style) {
			$properties = explode(':', $style);
			if (2 === count($properties)) {
				$results[trim($properties[0])] = trim($properties[1]);
			}
		}
		if ( ! empty( $results[ 'height' ] ) ) {
			$clean_height = absint( $results[ 'height' ] );
			$element->outertext = $clean_height;
			$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-spacer"><tr><td height="' . $clean_height .'" style="height: ' . $clean_height . 'px; padding: 0 !important; font-size: 0px; line-height: 100%;">&nbsp;</td></tr></table>';
		}
	}

	// Outline style.
	$replace = '.is-style-outline';
	foreach( $output->find( $replace ) as $key => $element ) {
		foreach( $element->find( 'a' ) as $a => $b ) {
			$b->class = $b->class . ' is-style-outlined';
		}
	}

	// Fix figures. direct images.
	$replace = 'figure.aligncenter, figure.alignleft, figure.alignright, figure.wp-block-image';
	foreach( $output->find( $replace ) as $key => $element ) {
		$align = 'center';
		if ( strstr( $element->class, 'aligncenter' ) ) {
			$align = 'center';
		}
		if ( strstr( $element->class, 'alignleft' ) ) {
			$align = 'left';
		}
		if ( strstr( $element->class, 'alignright' ) ) {
			$align = 'right';
		}
		foreach( $element->find( 'img' ) as $a => $b ) {
			if ( ! $b->class ) {
				$b->class = 'wp-image wp-image-' . $align;
			}
			if ( $b->class && strstr( $b->class, 'wp-image-' ) ) {
				$b->class = $b->class . ' wp-image wp-image-' . $align;
			}
			$b->{ 'data-align' } = $align;
		}
		$element->outertext = $element->innertext;
	}

	// Force image width.
	$replace = 'figure.wp-block-image img';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->class = ! empty( $element->class ) ? $element->class . ' wp-image' : 'wp-image';
		$element->style = $element->style . 'min-width: 10px; margin-bottom:0 !important;';
	}

	// Fix figures/images.
	$replace = 'figure.aligncenter, figure.alignleft, figure.alignright, figure.wp-block-image';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'figcaption', 0 ) ) {
			if ( $element->find( 'figcaption', 0 )->find( 'a' ) ) {
				$element->find( 'figcaption', 0 )->find( 'a', 0 )->class = 'caption-link';
			}
		}
		if ( $element->find( 'a' ) && ! strstr( $element->find( 'a', 0 )->class, 'caption-link' ) ) {
			$element->find( 'img', 0 )->{'data-href'} = $element->find( 'a', 0 )->href;
			$element->find( 'a', 0 )->outertext = $element->innertext;
		}
		if ( $element->find( 'figcaption', 0 ) ) {
			$element->outertext = $element->find( 'img', 0 )->outertext . $element->find( 'figcaption', 0 )->outertext;
		} else {
			$element->outertext = $element->find( 'img', 0 )->outertext;
		}
	}

	// Output column.
	$replace = '.wp-block-columns .wp-block-column';
	foreach( $output->find( $replace ) as $key => $element ) {

		$col_count = count( $output->find( $replace, $key )->parent()->find( 'div.wp-block-column' ) );

		$width = '';

		// Has style.
		if ( $output->find( $replace, $key )->style ) {
			
			$find_parent = $element->parent()->parent()->style ? $element->parent()->parent()->style : false;
			$the_width = 600;
			if ( $find_parent ) {
				$inner_r = [];
				$inner_ss = explode(';', $find_parent);

				foreach ($inner_ss as $inner_s) {
					$inner_props = explode(':', $inner_s);
					if (2 === count($inner_props)) {
						$inner_r[trim($inner_props[0])] = trim($inner_props[1]);
					}
				}
				if ( isset( $inner_r[ 'padding' ] ) ) {
					$split = explode( ' ', $inner_r[ 'padding' ] );
					$split_n = absint( $split[1] );
					$the_width = $the_width - absint( $split_n ) * 2;
				}
			}

			$s = $output->find( $replace, $key )->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			if ( isset( $results[ 'flex-basis' ] ) ) {
				$width = absint( $results[ 'flex-basis' ] ) / 100 * $the_width;
			} else {
				$width = $the_width / $col_count;
			}
		} else {

		}

		$valign = 'top';

		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-center' ) ) {
			$valign = 'middle';
		}
		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-bottom' ) ) {
			$valign = 'bottom';
		}

		$output->find( $replace, $key )->outertext = '<td class="column" width="' . $width . '" style="vertical-align: ' . $valign . ';" valign="' . $valign . '">' . $element->innertext . '</td>';
	}

	// Add columns wrapper as a table.
	$replace = '.wp-block-columns';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table-columns"><tr class="root-tr">' . $element->innertext . '</tr></table>';
	}

	// Change all figures.
	$replace = 'figure.wp-block-table';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<div class="wp-block-table">' . $element->innertext . '</div>';
	}

	// Convert embed metadata to table.
	$replace = '.ngl-embed-metadata';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<table width="100%" border="0" cellpadding="20" cellspacing="0"><tr><td width="50%" align="left" valign="top" style="vertical-align: top;margin:0 !important;">' . $element->outertext . '</td>';
	}

	$replace = '.ngl-embed-icon';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<td width="50%" align="right" valign="top" style="vertical-align: top;margin:0 !important;text-align: right !important;">' . $element->outertext . '</td></tr></table>';
	}

	// Gallery block.
	$replace = '.blocks-gallery-grid';
	foreach( $output->find( $replace ) as $key => $element ) {
		$class = $element->parent()->class;
		$cols = 1;
		if ( strstr( $class, 'columns-4' ) ) {
			$cols = 4;
		}
		if ( strstr( $class, 'columns-3' ) ) {
			$cols = 3;
		}
		if ( strstr( $class, 'columns-2' ) ) {
			$cols = 2;
		}
		$html = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
		$i = 0;
		foreach( $element->find( 'li' ) as $item => $list ) {
			$i++;
			$width = ( 600 / $cols ) - 20;
			$image = '<img src="' . $list->find( 'img', 0 )->src. '" alt="" width="' . $width . '" style="margin: 0;display: block; max-width: 100%; min-width: 50px; width: 100%;" />';
			$html .= '<td valign="top" style="vertical-align: top;margin:0;">' . $image . '</td>';
			if ( $i % $cols == 0 ) {
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
		$output->find( $replace, $key )->outertext = $html;
	}

	$replace = '#template_inner > div > img, #template_inner > div > a';
	foreach( $output->find( $replace ) as $key => $element ) {
		$class = ! empty( $element->class ) ? ' ngl-table-' . $element->class : '';
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-' . $element->tag . $class . '"><tr><td>' . $element->outertext . '</td></tr></table>';
	}

	$output->save();

	return ( string ) $output;
}

/**
 * Add table to full width image.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook2', 2, 3 );
function newsletterglue_generated_html_output_hook2( $html, $post_id, $app ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
		} else {
		$font = "Arial, Helvetica, sans-serif";
	}

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Cite.
	$replace = 'cite';
	$cite_style = 'font-weight: bold;font-size: 14px;font-style:normal;margin-top:10px;display:block;';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$element->style = $element->style . $cite_style;
		} else {
			$element->style = $cite_style;
		}
	}

	// Quotes.
	$replace = 'blockquote';
	foreach( $output->find( $replace ) as $key => $element ) {
		$style = $element->style;
		$align = 'left';
		if ( strstr( $style, 'left' ) ) {
			$align = 'left';
		} else if ( strstr( $style, 'right' ) ) {
			$align = 'right';
		} else if ( strstr( $style, 'center' ) ) {
			$align = 'center';
		}
		$accent = '#eee';
		if ( newsletterglue_get_theme_option( 'a_colour' ) ) {
			$accent = newsletterglue_get_theme_option( 'a_colour' );
		}
		$output->find( $replace, $key )->outertext = '<div class="ngl-quote" style="margin-left: 0;border-' . $align . ': 3px solid ' . $accent . ';padding-' . $align . ': 20px;' . $style . '">' . $element->innertext . '</div>';
	}

	// Set td widths.
	$replace = '.ngl-table-columns';
	$total = 0;
	foreach( $output->find( $replace ) as $key => $element ) {

		$holder = 600;

		if ( strstr( $element->parent->class, 'ngl-callout-content' ) ) {
			$holder = $element->parent->{ 'data-boxed-gap' };
		}

		$col_count = 0;
		$total = 0;
		foreach( $element->find( 'td' ) as $a => $td ) {
			$total = $total + absint( $td->width );
			$unset = $holder - $total;
			if ( ! $td->width ) {
				$col_count = $col_count + 1;
			}
		}
		foreach( $element->find( 'td' ) as $a => $td ) {
			if ( ! $td->width && $unset != $holder ) {
				$td->width = $unset / $col_count;
			}
			if ( ! $td->width && $unset == $holder && $col_count == 2 ) {
				$td->width = $holder / 2;
			}
		}
	}

	// Remove richtext spacers.
	$replace = 'i.ngl-tag-spacer';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = ' ';
	}

	// Content in merge tags.
	$replace = 'span.ngl-tag';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Force publish date for some tags.
	$replace = 'span.auto_date';
	foreach( $output->find( $replace ) as $key => $element ) {
		$format 	= $element->{ 'data-date-format' };
		$post_id 	= $element->{ 'data-post-id' };
		if ( $format && $post_id ) {
			$element->outertext = date_i18n( $format, get_post_timestamp( $post_id ) );
		}
	}

	$output->save();

	return ( string ) $output;
}

/**
 * Adjustments.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook3', 3, 3 );
function newsletterglue_generated_html_output_hook3( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$base = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol', 'img' );

	$replaces = array();
	$elements = array(
		'#template_inner h1',
		'#template_inner h2',
		'#template_inner h3',
		'#template_inner h4',
		'#template_inner h5',
		'#template_inner h6',
		'#template_inner p',
		'#template_inner ol',
		'#template_inner ul',
		'#template_inner figure',
		'#template_inner .ngl-quote',
		'#template_inner .wp-block-buttons',
		'#template_inner .ngl-embed-social',
		'#template_inner .wp-block-table > table',
		'#template_inner img.wp-image',
		'.ngl-article-img-full',
	);

	// Group block.
	foreach( $base as $inner ) {
		$elements[] = '.wp-block-newsletterglue-group > ' . $inner;
	}

	// Container block.
	foreach( $base as $inner ) {
		$elements[] = '.ngl-callout-content ' . $inner;
	}

	foreach( $elements as $el ) {
		$replaces[] = $el;
	}

	$replace = implode( ', ', $replaces );
	foreach( $output->find( $replace ) as $key => $element ) {
		$td_align = '';

		if ( strstr( $element->innertext, 'mso]' ) || strstr( $element->innertext, 'endif]' ) ) {
			if ( ! strstr( $element->class, 'wp-block-button' ) ) {
				$element->outertext = $element->innertext;
				continue;
			}
		}
		$class = ! empty( $element->class ) ? ' ngl-table-' . $element->class : '';
		if ( $element->tag == 'img' ) {
			if ( $element->{ 'data-align' } ) {
				$align = $element->{ 'data-align' };
				if ( $align == 'center' ) {
					$element->style = $element->style . 'margin: auto !important;';
					$td_align = 'align="center"';
				}
				if ( $align == 'right' ) {
					$element->style = $element->style . 'margin-left: auto !important;';
					$td_align = 'align="right"';
				}
			}
			if ( strstr( $element->class, 'ngl-' ) ) {
				continue;
			}
		}
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-' . $element->tag . $class . '"><tr><td ' . $td_align . '>' . $element->outertext . '</td></tr></table>';
	}

	$output->save();

	return ( string ) $output;
}

/**
 * Fix image widths.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook4', 4, 3 );
function newsletterglue_generated_html_output_hook4( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '#template_inner img';
	foreach( $output->find( $replace ) as $key => $element ) {

		if ( strstr( $element->class, 'callout-img' ) ) {
			continue;
		}

		if ( strstr( $element->class, 'wp-image' ) ) {
			continue;
		}

		if ( $element->parent()->parent()->tag && $element->parent()->parent()->tag == 'td' ) {
			$td = $element->parent()->parent();
			$threshold = strstr( $element->class, 'callout-img' ) ? 528 : 600;
			$threshold = strstr( $element->class, 'embed-thumb-' ) ? $threshold - 2 : $threshold;
			$image_width = newsletterglue_get_image_width_by_td( $td, $threshold );
			if ( ! strstr( $td->class, 'ngl-td-' ) && ! strstr( $element->class, 'ngl-' ) && $image_width ) {
				$element->width = floor( $image_width );
				$element->height = '';
			}
		}

		if ( $element->parent()->tag && $element->parent()->tag == 'td' ) {
			$td = $element->parent();
			$threshold = strstr( $element->class, 'callout-img' ) ? 528 : 600;
			$threshold = strstr( $element->class, 'embed-thumb-' ) ? $threshold - 2 : $threshold;
			$image_width = newsletterglue_get_image_width_by_td( $td, $threshold );
			if ( ! strstr( $td->class, 'ngl-td-' ) && ! strstr( $element->class, 'ngl-' ) && $image_width ) {
				$element->width = floor( $image_width );
				$element->height = '';
			}
		}

	}

	$output->save();

	return ( string ) $output;
}

/**
 * Get cached image sizes.
 */
function newsletterglue_cached_image_size( $source ) {
	$caches = get_option( 'newsletterglue_image_sizes' );
	$hash = md5( $source );
	if ( ! empty( $caches ) && isset( $caches[ $hash ] ) ) {
		return $caches[ $hash ];
	}
	return false;
}

/**
 * Set cached image sizes.
 */
function newsletterglue_cached_image_size_set( $source, $image ) {
	$caches = get_option( 'newsletterglue_image_sizes' );
	$hash = md5( $source );
	$caches[ $hash ] = $image;
	update_option( 'newsletterglue_image_sizes', $caches );
}

/**
 * Set font-family per td.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook5', 5, 3 );
function newsletterglue_generated_html_output_hook5( $html, $post_id, $app ) {

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$email_font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
	} else {
		$email_font = "Arial, Helvetica, sans-serif";
	}

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Add font-family to all TDs.
	$replace = '#template_inner, #template_inner td';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$element->style = $element->style . "font-family: $email_font;";
		} else {
			$element->style = "font-family: $email_font;";
		}
	}

	// Image width/height.
	if ( function_exists( 'getimagesize' ) ) {
		$replace = '#template_inner img.wp-image';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( ! empty( $element->src ) ) {
				$image = newsletterglue_cached_image_size( $element->src ) ? newsletterglue_cached_image_size( $element->src ) : getimagesize( $element->src );
				if ( ! empty( $image[0] ) ) {
					if ( $image[0] && ! $element->width ) {
						$element->width = $image[0];
						$element->height = $image[1];
					}
					newsletterglue_cached_image_size_set( $element->src, $image );
				}
			}
		}

		$replace = '#template_inner img.logo-image';
		foreach( $output->find( $replace ) as $key => $element ) {
			if ( ! empty( $element->src ) ) {
				$image = newsletterglue_cached_image_size( $element->src ) ? newsletterglue_cached_image_size( $element->src ) : getimagesize( $element->src );
				if ( ! empty( $image[0] ) ) {
					$element->width = $image[0];
					$element->height = $image[1];
					newsletterglue_cached_image_size_set( $element->src, $image );
				}
			}
		}
	}

	// Logo wrapper.
	$replace = 'div.ngl-logo';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->class, 'right' ) ) {
			$element->find( 'td', 0 )->align = 'right';
		} else if ( strstr( $element->class, 'left' ) ) {
			$element->find( 'td', 0 )->align = 'left';
		} else {
			$element->find( 'td', 0 )->align = 'center';
		}
		$element->outertext = $element->innertext;
	}

	// Featured wrapper.
	$replace = 'div.ngl-masthead';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Meta data.
	$replace = 'div.ngl-metadata';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->find( 'td', 0 ) ) {
			$s = $element->style;
			if ( $element->find( 'td', 0 )->style ) {
				$element->find( 'td', 0 )->style = $element->find( 'td', 0 )->style . $s;
			} else {
				$element->find( 'td', 0 )->style = $s;
			}
			$element->outertext = $element->innertext;
		}
	}

	// Author.
	$replace = 'div.ngl-author';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = $element->innertext;
	}

	// Has background.
	$replace = 'p.has-background, a.has-background';
	foreach( $output->find( $replace ) as $key => $element ) {
		$td = $element->parent;
		if ( $element->style ) {
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			if ( ! empty( $results[ 'background-color' ] ) ) {
				if ( $td->tag == 'td' ) {
					$td->style = $td->style . 'background-color:' . $results[ 'background-color' ] . ';';
				};
			}
		}
		$td->style = $td->style . 'padding: 20px;';
	}

	$output->save();

	return ( string ) $output;

}

/**
 * Images.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook6', 6, 3 );
function newsletterglue_generated_html_output_hook6( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	// Set width/height.
	$replace = '#template_inner img[data-w]';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->width = $element->{ 'data-w' };
		$element->height = $element->{ 'data-h' };
		$element->removeAttribute( 'data-w' );
		$element->removeAttribute( 'data-h' );
	}

	// Images.
	$replace = '#template_inner img';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->width ) {
			$max_width = $element->width . 'px';
			if ( $element->width > 600 ) {
				$element->width = 560;
				$max_width = '100%';
				$element->height = '';
			}
			if ( $element->style ) {
				$element->style = $element->style . 'max-width: ' . $max_width . ';';
			} else {
				$element->style = 'max-width: ' . $max_width . ';';
			}
		}
	}

	// Images inside columns td.
	$replace = '#template_inner .ngl-table-columns td';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->width && is_numeric( $element->width ) && ! strstr( $element->class, 'ngl-td-auto' ) ) {
			$max_w = $element->width - 40;
			foreach( $element->find( 'img' ) as $image_id => $el ) {
				if ( $el->width && $el->width > $max_w ) {
					$el->style = $el->style . 'max-width: ' . $max_w . 'px;';
					$el->width = $max_w;
					$el->height = '';
				}
			}
		}
	}

	// Empty divs = brs.
	$replace = '#template_inner div';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->innertext == '' ) {
			$element->outertext = '<br /><br />';
		}
	}

	// Inline colored links.
	$replace = 'span.has-inline-color';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( $element->style ) {
			$s = $element->style;
			$results = [];
			$styles = explode(';', $s);

			foreach ($styles as $style) {
				$properties = explode(':', $style);
				if (2 === count($properties)) {
					$results[trim($properties[0])] = trim($properties[1]);
				}
			}
			if ( ! empty( $results[ 'color' ] ) ) {
				$element->parent->style = $element->parent->style . 'color: ' . $results[ 'color' ] . ' !important;';
			}
		}
	}

	// Find all images that could have links.
	$replace = '#template_inner img[data-href]';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '<a href="' . $element->{'data-href'} . '">' . $element->outertext . '</a>';
	}

	// Figcaption.
	$replace = '#template_inner figcaption';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ngl-table-caption"><tr><td>' . $element->innertext . '</td></tr></table>';
	}

	// Tables containing inline elements.
	$replace = '.ngl-table-inline';
	foreach( $output->find( $replace ) as $key => $element ) {
		$align = 'left';
		if ( strstr( $element->class, '-center' ) ) {
			$align = 'center';
		} else if ( strstr( $element->class, '-right' ) ) {
			$align = 'right';
		}

		if ( $element->find( '.ngl-share-description' ) ) {
			$saved_text = $element->find( '.ngl-share-description', 0 )->outertext;
			$element->find( '.ngl-share-description', 0 )->outertext = '';
		} else {
			$saved_text = '';
		}

		$inner_html = strip_tags( $element->innertext, '<a><img>' );
		$inner_html = str_replace( '<a', '<td><a', $inner_html );
		$inner_html = str_replace( '</a>', '</a></td>', $inner_html );
		$inner_html = '<table border="0" cellpadding="0" cellspacing="0" align="' . $align . '"><tr>' . $inner_html . '</tr></table>';

		if ( $saved_text ) {
			$saved_text = '<table width="100%" border="0" cellpadding="0" cellspacing="0" align="' . $align . '"><tr><td valign="middle" align="' . $align . '" style="text-align: ' . $align . ';padding-bottom: 10px !important;">' . $saved_text . '</td></tr></table>';
		}

		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table ' . str_replace( 'undefined', '', $element->class ) . ' align-' . $align . '"><tr><td valign="middle" align="' . $align . '">' . $saved_text . $inner_html . '</td></tr></table>';

	}

	// Alignment.
	$replace = 'table.wp-image-center td';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->align = 'center';
	}

	$replace = 'table.wp-image-left td';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->align = 'left';
	}

	$replace = 'table.wp-image-right td';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->align = 'right';
	}

	/* Separator */
	$replace = '.wp-block-separator';
	foreach( $output->find( $replace ) as $key => $element ) {
		$hr_bg = '#dddddd';
		$height = 1;
		$width = '100%';
		$classes = $element->class;
		$results = [];
		$styles = explode(';', $element->style );
		foreach ($styles as $style) {
			$properties = explode(':', $style);
			if (2 === count($properties)) {
				$results[trim($properties[0])] = trim($properties[1]);
			}
		}
		if ( isset( $results[ 'color' ] ) ) {
			$hr_bg = $results[ 'color' ];
		}
		if ( strstr( $classes, 'thick' ) ) {
			$height = 3;
		}
		if ( strstr( $classes, 'is-short' ) ) {
			$width = 40;
		}
		$element->outertext = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ngl-table"><tr><td>
			<table width="' . $width . '" border="0" cellpadding="0" cellspacing="0" class="ngl-table"><tr><td style="background: ' . $hr_bg . ';padding: 0;height: ' . $height . 'px;"></td></tr></table>
		</td></tr></table>';
	}

	// Kill permalink arrows.
	$replace = 'img.ngl-metadata-permalink-arrow';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->outertext = '';
	}

	$output->save();

	return ( string ) $output;

}

/**
 * Fix wrong td widths.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output_hook7', 6, 3 );
function newsletterglue_generated_html_output_hook7( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html, true, false );

	$replace = '#template_inner > .ngl-table-columns .root-tr';
	$count = 0;
	$width = 0;
	foreach( $output->find( $replace ) as $key => $element ) {
		foreach( $element->find( 'td' ) as $a => $b ) {
			if ( $b->parent()->tag == 'tr' && strstr( $b->parent()->class, 'root-tr' ) ) {
				$count = $count + 1;
				$width = $width + $b->width;
			}
		}
		if ( $width < 600 ) {
			$new_width = 600 / $count;
			foreach( $element->find( 'td' ) as $a => $b ) {
				if ( $b->parent()->tag == 'tr' && strstr( $b->parent()->class, 'root-tr' ) ) {
					$b->width = $new_width;
				}
			}
		}
	}

	$output->save();

	return ( string ) $output;

}

/**
 * Get image widths.
 */
function newsletterglue_get_image_width_by_td( $td, $threshold = 600 ) {

	$count = count( $td->parent()->children() );
	if ( $td->style ) {
		$s = $td->style;
		$results = [];
		$styles = explode(';', $s);

		foreach ($styles as $style) {
			$properties = explode(':', $style);
			if (2 === count($properties)) {
				$results[trim($properties[0])] = trim($properties[1]);
			}
		}
		if ( isset( $results[ 'width' ] ) ) {
			$width = $results[ 'width' ];
		} else {
			$width = '100%';
		}
	} else {
		$width = '100%';
	}
	$clean_width = str_replace( '%', '', $width );
	$clean_width = str_replace( 'px', '', $clean_width );
	if ( ! is_numeric( $clean_width ) ) {
		return false;
	}

	$image_width = ( $clean_width / 100.00 ) * ( $threshold - ( 20 * $count ) - 20 );

	return $image_width;
}

/**
 * Add logo image.
 */
function newsletterglue_add_logo() {

	$id 			= get_option( 'newsletterglue_logo_id' );
	$logo 			= wp_get_attachment_url( $id );
	$data  			= wp_get_attachment_image_src( $id, 'full' );
	$width 			= $data[1];
	$height 		= $data[2];
	$logo_url		= get_option( 'newsletterglue_logo_url' );
	$logo_position 	= get_option( 'newsletterglue_position_logo' );

	$max_logo_w     = newsletterglue_get_theme_option( 'max_logo_w' );

	if ( $max_logo_w && $width > $max_logo_w ) {
		$ratio = $width / $height;
		$n_width = $max_logo_w;
		$n_height = ceil( $max_logo_w / $ratio );
	} else {
		$n_width = $width;
		$n_height = $height;
	}

	if ( ! $logo_position ) {
		$logo_position = 'center';
	}

	if ( $logo ) {
		if ( esc_url( $logo_url ) ) {
			return '<div class="ngl-logo ngl-logo-' . $logo_position . '"><a href="' . esc_url( $logo_url ) . '" target="_blank" style="display: inline-block;" class="logo"><img class="logo-image" data-w="' . $n_width . '" data-h="'. $n_height . '" src="' . esc_url( $logo ) . '" /></a></div>';
		} else {
			return '<div class="ngl-logo ngl-logo-' . $logo_position . '"><img class="logo-image" data-w="' . $n_width . '" data-h="'. $n_height . '" src="' . esc_url( $logo ) . '" /></div>';
		}
	}

	return null;

}

/**
 * Add masthead image.
 */
function newsletterglue_add_masthead_image( $post, $position = 'below' ) {

	$post_id 	= $post->ID;
	$data 		= get_post_meta( $post_id, '_newsletterglue', true );
	$use_image 	= isset( $data[ 'add_featured' ] ) ? sanitize_text_field( $data[ 'add_featured' ] ) : get_option( 'newsletterglue_add_featured' );

	$link_featured_image = get_option( 'newsletterglue_link_featured' );

	// Use of featured image.
	if ( $use_image ) {
		$url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		if ( $url ) {
			if ( $link_featured_image && $link_featured_image === 'yes' ) {
				return '<div class="ngl-masthead ngl-masthead-' . $position . '"><a href="' . get_permalink( $post_id ) . '" class="masthead-link"><img src="' . $url . '" class="masthead" /></a></div>';
			} else {
				return '<div class="ngl-masthead ngl-masthead-' . $position . '"><img src="' . $url . '" class="masthead" /></div>';
			}
		}
	}

	return '';
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
 * Get theme option.
 */
function newsletterglue_get_theme_option( $id = '', $theme = null ) {

	// Check if no theme was provided.
	if ( ! $theme ) {
		$theme = get_option( 'newsletterglue_theme' );
	}

	// Get theme option.
	if ( isset( $theme[ $id ] ) ) {
		
		if ( empty( $theme[ $id ] ) ) {
			if ( in_array( $id, array( 'email_bg', 'container_bg', 'btn_border' ) ) ) {
				return 'transparent';
			}
			if ( in_array( $id, array( 'h1_colour', 'h2_colour', 'h3_colour', 'h4_colour', 'h5_colour', 'h6_colour', 'p_colour', 'a_colour' ) ) ) {
				return 'inherit';
			}
			if ( in_array( $id, array( 'btn_bg' ) ) ) {
				return '#32373c';
			}
			if ( in_array( $id, array( 'btn_colour' ) ) ) {
				return '#fff';
			}
		}

		return $theme[ $id ];
	}

	// Get default value.
	$default = newsletterglue_get_theme_default( $id );

	return $default ? $default : false;
}

/**
 * Get a default value for a theme option.
 */
function newsletterglue_get_theme_default( $key ) {

	$keys = array(
		'h1_colour'					=> '#333333',
		'h2_colour'					=> '#333333',
		'h3_colour'					=> '#333333',
		'h4_colour'					=> '#333333',
		'h5_colour'					=> '#333333',
		'h6_colour'					=> '#333333',
		'p_colour'					=> '#666666',
		'h1_size'					=> 32,
		'h2_size'					=> 28,
		'h3_size'					=> 24,
		'h4_size'					=> 22,
		'h5_size'					=> 20,
		'h6_size'					=> 18,
		'p_size'					=> 16,
		'h1_align'					=> 'left',
		'h2_align'					=> 'left',
		'h3_align'					=> 'left',
		'h4_align'					=> 'left',
		'h5_align'					=> 'left',
		'h6_align'					=> 'left',
		'p_align'					=> 'left',
		'email_bg'					=> '#f9f9f9',
		'container_bg'				=> '#ffffff',
		'accent'					=> '#0088A0',
		'a_colour'					=> '#0088A0',
		'btn_bg'					=> '#0088A0',
		'btn_colour'				=> '#ffffff',
		'btn_radius'				=> 0,
		'btn_border'				=> '#0088A0',
		'btn_width'					=> 150,
		'container_padding1'		=> 0,
		'container_padding2'		=> 0,
		'container_margin1'			=> 10,
		'container_margin2'			=> 10,
		'max_logo_w'				=> 0,
		'mobile_max_logo_w'			=> 0,
		// Mobile.
		'mobile_h1_size'			=> 28,
		'mobile_h2_size'			=> 24,
		'mobile_h3_size'			=> 22,
		'mobile_h4_size'			=> 20,
		'mobile_h5_size'			=> 18,
		'mobile_h6_size'			=> 16,
		'mobile_p_size'				=> 16,
		'mobile_container_padding1' => 0,
		'mobile_container_padding2' => 0,
		'mobile_container_margin1' 	=> 10,
		'mobile_container_margin2' 	=> 10,
		'mobile_btn_width'			=> 150,
	);

	return isset( $keys[ $key ] ) ? $keys[ $key ] : '';
}

/**
 * Max logo width.
 */
function ngl_get_max_logo_width( $mobile = false ) {
	$var = $mobile ? 'mobile_max_logo_w' : 'max_logo_w';
	$max = newsletterglue_get_theme_option( $var );

	return $max ? $max . 'px' : '100%';
}

/**
 * Email fonts.
 */
function newsletterglue_get_email_fonts() {

	$fonts = array(
		'arial'				=> 'Arial',
		'helvetica'			=> 'Helvetica',
		'times_new_roman'	=> 'Times New Roman',
		'verdana'			=> 'Verdana',
		'courier_new'		=> 'Courier New',
		'courier'			=> 'Courier',
		'tahoma'			=> 'Tahoma',
		'georgia'			=> 'Georgia',
		'palatino'			=> 'Palatino',
		'trebuchet_ms'		=> 'Trebuchet MS',
		'geneva'			=> 'Geneva',
	);

	$fonts = apply_filters( 'newsletterglue_get_email_fonts', $fonts );

	asort( $fonts );

	return array_merge( array( 0 => __( 'Not set', 'newsletter-glue' ) ), $fonts );
}

/**
 * Get font name.
 */
function newsletterglue_get_font_name( $font = '' ) {

	$fonts = newsletterglue_get_email_fonts();

	return apply_filters( 'newsletterglue_get_font_name', isset( $fonts[ $font ] ) ? $fonts[ $font ] : '', $font );
}

/**
 * Update the review button start time.
 */
function newsletterglue_review_button_start() {

	$review = get_option( 'newsletterglue_review_activates_on' );

	if ( ! $review ) {
		$two_weeks = time() + ( 60 * 60 * 24 * 14 );
		update_option( 'newsletterglue_review_activates_on', $two_weeks );
	}

}
add_action( 'init', 'newsletterglue_review_button_start' );

/**
 * Get post types.
 */
function newsletterglue_get_post_types() {

	$post_types  = get_post_types();
	$unsupported = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'scheduled-action', 'newsletterglue', 'ngl_pattern' );

	if ( is_array( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( ! in_array( $post_type, apply_filters( 'newsletterglue_unsupported_post_types', $unsupported ) ) ) {
				$types[ $post_type ] = $object->labels->name;
			}
		}
	}

	return apply_filters( 'newsletterglue_get_post_types', $types );
}

/**
 * Get post types.
 */
function newsletterglue_content_estimated_reading_time( $content = '', $words_per_minute = 150 ) {

	$clean_content	= strip_shortcodes( $content );
	$clean_content	= strip_tags( $clean_content );
	$word_count		= str_word_count( $clean_content );
	$time 			= ceil( $word_count / $words_per_minute );

	$output = sprintf( __( '%s mins', 'newsletter-glue' ), $time );

	return $output;

}

/**
 * Add theme designer css.
 */
function newsletterglue_add_theme_designer_css() {

	// If theme designer css is disabled.
	if ( get_option( 'newsletterglue_disable_plugin_css' ) == 1 ) {
		return;
	}

	if ( newsletterglue_get_theme_option( 'font' ) ) {
		$email_font = "'" . newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . "', Arial, Helvetica, sans-serif";
	} else {
		$email_font = "Arial, Helvetica, sans-serif";
	}

	$align = newsletterglue_get_theme_option( 'p_align' );
?>

.ExternalClass {width:100%;}

.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	line-height: 100%;
}

body {
	mso-line-height-rule: exactly;
	line-height: 150%;
	-webkit-text-size-adjust: none;
	-ms-text-size-adjust: none;
	margin: 0;
	padding: 0;
	background: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>;
}

body, #wrapper, #template_inner {
	font-family: Arial, Helvetica, sans-serif;
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
}

#template_inner p,
#template_inner div {
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
}

#template_inner .ngl-article div {
	color: inherit;
}

span.yshortcuts { color: #000; background-color:none; border:none;}
span.yshortcuts:hover,
span.yshortcuts:active,
span.yshortcuts:focus {color: #000; background-color:none; border:none;}

table {
	mso-table-lspace: 0pt;
	mso-table-rspace: 0pt;
}

.ngl-table td {
	padding: 10px 20px;
}

.ngl-table-masthead td {
	padding: 10px 20px;
}

.ngl-table-inline td {
	padding: 10px 20px;
}

.ngl-table-inline td a {
	display: inline-block;
}

.ngl-table-inline.align-center td td {
	padding: 0 8px !important;
}

.ngl-table-inline.align-left td td {
	padding: 0 12px 0 0 !important;
}

.ngl-quote p {
	margin-bottom: 15px;
}

.ngl-quote p:last-child {
	margin-bottom: 0;
}

.ngl-table-inline.align-right td td {
	padding: 0 0 0 12px !important;
}

.ngl-table-ngl-credits td {
	padding: 20px;
}

.ngl-table-caption td {
	padding-top: 0 !important;
	text-align: center;
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ) - 3 ; ?>px;
	opacity: 0.7;
}

.ngl-table-callout .wp-block-newsletterglue-callout td {
	padding: 0;
}

.ngl-table-callout .wp-block-newsletterglue-callout .ngl-callout-content > table td {
	padding-bottom: 10px;
}

.ngl-table-callout .wp-block-newsletterglue-callout .ngl-callout-content > table:last-child td {
	padding-bottom: 0;
}

.ngl-table-posts > tr > td {
	padding: 10px 20px;
}

.ngl-article-img-full td {
	padding: 0;
}

a.ngl-metadata-permalink {
	color: inherit !important;
}

.ngl-table-article td {
	padding: 20px 10px;
}

.ngl-table-article td:first-child {
	padding-left: 20px;
}

.ngl-table-article td:last-child {
	padding-right: 20px;
}

.ngl-table-posts-pure .ngl-table-article td:first-child {
	padding-left: 0;
}

.ngl-table-posts-pure .ngl-table-article td:last-child {
	padding-right: 0;
}

.ngl-article-featured img {
	max-width: 100% !important;
	height: auto;
}

.ngl-table-posts-colored .ngl-article-mob-wrap {
	padding: 20px;
	margin: 0 0 10px;
}

.ngl-table-posts-colored .ngl-article {
	margin: 0 0 10px;
}

.ngl-table-posts-colored div.ngl-article-img-full {
	padding: 20px !important;
}

.ngl-table-posts-pure .ngl-article-mob-wrap {
	margin: 0 0 20px;
}

<?php
	$sizes = get_option( 'newsletterglue_theme_sizes' );
	if ( ! empty( $sizes ) ) {
		foreach( $sizes as $key => $size ) {
			$slug 	= $size->slug;
			$value 	= $size->size . 'px';
			echo ".has-$slug-font-size { font-size: $value !important; }";
		}
	}

	$colors = get_option( 'newsletterglue_theme_colors' );
	if ( ! empty( $colors ) ) {
		foreach( $colors as $key => $color ) {
			$slug 	= $color->slug;
			$color 	= $color->color;
			echo ".has-$slug-color, a.has-$slug-color { color: $color !important; }";
			echo ".has-$slug-background-color { background-color: $color !important; }";
			echo "a.has-$slug-background-color { background-color: $color !important; }";
			echo "a.has-$slug-background-color { border-color: $color !important; }";
			echo ".wp-block-button.is-style-outline a.has-$slug-background-color { color: $color !important; }";
		}
	}
?>

p.has-text-color * {
	color: inherit !important;
}

.ngl-table-ngl-unsubscribe td {
	border-top: 1px solid #eee;
	padding: 20px 100px;
}

.ngl-table-columns {
	table-layout: fixed;
}

a {
	color: #2A5DB0;
	text-decoration: underline;
}

hr {
	margin: 0;
    height: 1px;
	background-color: #ddd;
	color: #ddd;
    font-size: 0;
    border: 0;
}

#wrapper {
	background: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>;
	padding: 0;
	margin-top: <?php echo absint( newsletterglue_get_theme_option( 'container_margin1' ) ); ?>px;
	margin-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_margin2' ) ); ?>px;
	<?php if ( newsletterglue_get_theme_option( 'font' ) ) : ?>
	font-family: <?php echo $email_font; ?>;
	<?php endif; ?>
	<?php if ( ! newsletterglue_get_theme_option( 'font' ) && ( isset( $_GET[ 'preview_email' ] ) || isset( $_GET[ 'view_newsletter' ] ) ) ) : ?>
	font-family: Arial, Helvetica, sans-serif;
	<?php endif; ?>
}

#template_inner {
	background: <?php echo newsletterglue_get_theme_option( 'container_bg' ); ?>;
	box-sizing: border-box;
	padding-left: 0;
	padding-right: 0;
	padding-top: <?php echo absint( newsletterglue_get_theme_option( 'container_padding1' ) ); ?>px;
	padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_padding2' ) ); ?>px;
}

h1, h2, h3, h4, h5, h6 {
	color: black;
	padding: 0 !important;
	margin: 0;
	line-height: 120%;
}

p {
	mso-line-height-rule: exactly;
	line-height: 150%;
}

img.wp-image {
	height: auto;
}

.wp-block-columns {
	margin: 0 !important;
}

.wp-block-columns h1,
.wp-block-columns h2,
.wp-block-columns h3,
.wp-block-columns h4,
.wp-block-columns h5,
.wp-block-columns h6 {
	margin-top: 0 !important;
}

h1 { font-size: <?php echo newsletterglue_get_theme_option( 'h1_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h1_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h1_align' ); ?>; }
h2 { font-size: <?php echo newsletterglue_get_theme_option( 'h2_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h2_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h2_align' ); ?>; }
h3 { font-size: <?php echo newsletterglue_get_theme_option( 'h3_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h3_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h3_align' ); ?>; }
h4 { font-size: <?php echo newsletterglue_get_theme_option( 'h4_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h4_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h4_align' ); ?>; }
h5 { font-size: <?php echo newsletterglue_get_theme_option( 'h5_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h5_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h5_align' ); ?>; }
h6 { font-size: <?php echo newsletterglue_get_theme_option( 'h6_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h6_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h6_align' ); ?>; }

h1 a.ngl-title-to-post {
	text-decoration: none;
	color: <?php echo newsletterglue_get_theme_option( 'h1_colour' ); ?> !important;
}

p, ul, ol {
	padding: 0;
	margin: 0;
}

ul, ol {
	margin-left: 20px !important;
}

<?php if ( $align && $align != 'left' ) { ?>
p, ul, ol, li {
	text-align: <?php echo newsletterglue_get_theme_option( 'p_align' ); ?>;
}
<?php } ?>

blockquote {
	margin: 0;
}

blockquote p {
	text-align: inherit !important;
}

a {
	color: <?php echo newsletterglue_get_theme_option( 'a_colour' ); ?> !important;
}

figure {
	margin: 0;
	width: auto !important;
}

figcaption {
	font-size: 14px;
	opacity: 0.7;
	margin-top: 10px;
}

#template_inner img {
	max-width: 100%;
	display: block;
}

h1 img,
h2 img,
h3 img,
h4 img,
h5 img,
h6 img,
p img {
	margin: auto;
	display: inline-block;
}

ul.blocks-gallery-grid {
	list-style-type: none;
}

#template_inner .wp-block-table table {
	width: 100%;
	border: 0;
	border-collapse: collapse;
}

#template_inner .wp-block-table td {
	border: 1px solid #eee;
	padding: 10px;
}

#template_inner td table img {
	margin: 0;
}

#template_inner td.ngl-td-auto {
	border: 0;
	font-size: inherit !important;
}

p.ngl-credits,
p.ngl-unsubscribe {
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ) - 2 ; ?>px;
	text-align: center;
	color: #707070 !important;
}

p.ngl-credits a,
p.ngl-unsubscribe a {
	color: #707070 !important;
	text-decoration: underline;
}

.wp-block-buttons .wp-block-button {
	display: inline-block !important;
	padding: 0 !important;
}

.wp-block-button.aligncenter,
.wp-block-buttons.aligncenter,
.wp-block-calendar {
	text-align: center;
}

.aligncenter img {
	margin-left: auto !important;
	margin-right: auto !important;
}

.wp-block-button__link {
	mso-hide: all;
	display: inline-block;
	text-align: center;
	box-sizing: border-box;
	padding: 11px 20px;
	text-decoration: none;
	color: <?php echo newsletterglue_get_theme_option( 'btn_colour' ); ?> !important;
	min-width: <?php echo (int) newsletterglue_get_theme_option( 'btn_width' ); ?>px !important;
	border-width: 1px;
	border-style: solid;
	border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px;
}

.wp-block-button__link.has-background {

}

.wp-block-button__link:not(.has-background) {
	background-color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?> !important;
	border: 1px solid <?php echo newsletterglue_get_theme_option( 'btn_border' ); ?> !important;
}

.wp-block-button.wp-block-button__width-100 {
	width: 100% !important;
	padding: 0 !important;
}

.wp-block-button.wp-block-button__width-100 .wp-block-button__link {
	width: 100% !important;
}

.wp-block-button.is-style-outline .wp-block-button__link {
	background-color: transparent !important;
	border-width: 2px !important;
	padding: 10px 24px;
}

.wp-block-column img {
	width: 100% !important;
	max-width: 100% !important;
	max-height: 100% !important;
	height: auto !important;
}

.ngl-hide-in-email {
	display: none !important;
	visibility: hidden !important;
}

#template_inner img.logo-image {
	margin: 0 !important;
	display: block !important;
}

.is-content-justification-left td { text-align: left; }
.is-content-justification-center td { text-align: center; }
.is-content-justification-right td { text-align: right; }

.wp-block-buttons.is-content-justification-left .wp-block-button,
.wp-block-buttons .wp-block-button {
	margin-right: 10px;
	margin-left: 0;
}

.wp-block-buttons.is-content-justification-center .wp-block-button {
	margin: 0 10px;
}

.wp-block-buttons.is-content-justification-right .wp-block-button {
	margin-left: 10px;
	margin-right: 0;
}

.ngl-table-has-text-align-left td { text-align: left !important; }
.ngl-table-has-text-align-center td { text-align: center !important; }
.ngl-table-has-text-align-right td { text-align: right !important; }

.has-text-align-left { text-align: left !important; }
.has-text-align-center { text-align: center !important; }
.has-text-align-right { text-align: right !important; }

.ngl-table-ngl-embed-social > tr > td {
	padding: 20px;
}

.ngl-embed-meta td {
	padding: 20px !important;
}

@media only screen and (max-width:642px) {

	#template_table {
		width: 95% !important;
		max-width: 95% !important;
	}

	#template_inner img {
		max-width: 100% !important;
		width: auto;
	}

	#template_inner {
		width: auto;
		padding-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding1' ) ); ?>px !important;
		padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding2' ) ); ?>px !important;
	}

	#wrapper {
		margin-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin1' ) ); ?>px !important;
		margin-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin2' ) ); ?>px !important;
	}

	#template_inner .wp-block-button__link {
		min-width: <?php echo (int) newsletterglue_get_theme_option( 'mobile_btn_width' ); ?>px !important;
	}

	#template_inner img.logo-image {
		max-width: <?php echo ngl_get_max_logo_width( true ); ?> !important;
		height: auto !important;
	}

	body, #wrapper, #template_inner, p.ngl-credits, p.ngl-unsubscribe {
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px !important;
	}

	h1 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h1_size' ); ?>px !important; }
	h2 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h2_size' ); ?>px !important; }
	h3 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h3_size' ); ?>px !important; }
	h4 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h4_size' ); ?>px !important; }
	h5 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h5_size' ); ?>px !important; }
	h6 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h6_size' ); ?>px !important; }

	.ngl-table-columns td.column {
		display: block !important;
		float: none !important;
		width: 100% !important;
		clear: both !important;
		box-sizing: border-box !important;
		padding-bottom: 10px !important;
	}

	.ngl-table-ngl-unsubscribe td {
		padding: 20px !important;
	}

}

	<?php
}
add_action( 'newsletterglue_email_styles', 'newsletterglue_add_theme_designer_css', 10 );

/**
 * Add preview text CSS.
 */
function newsletterglue_add_preview_text_css() {
	?>
	.ngl-preview-text {
		display: none;
		font-size: 1px;
		line-height: 1px;
		max-height: 0px;
		max-width: 0px;
		opacity: 0;
		overflow: hidden;
		mso-hide: all;
		font-family: sans-serif;
	}
	<?php
}
add_action( 'newsletterglue_email_styles', 'newsletterglue_add_preview_text_css', 20 );

/**
 * Add custom CSS.
 */
function newsletterglue_add_custom_css() {

	echo wp_strip_all_tags( get_option( 'newsletterglue_css' ) );

}
add_action( 'newsletterglue_add_custom_styles', 'newsletterglue_add_custom_css', 100 );

/**
 * Remove a div by class from html.
 */
function newsletterglue_remove_div( $html, $class ) {

	if ( ! $html ) {
		return $html;
	}

    $dom = new \DOMDocument();

	libxml_use_internal_errors( true );

    $dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8') );

	libxml_clear_errors();

    $finder = new \DOMXPath( $dom );

    $nodes = $finder->query( "//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]" );

    foreach ($nodes as $node) {
        $node->parentNode->removeChild( $node );
    }

    return $dom->saveHTML();
}

/**
 * Get tier.
 */
function newsletterglue_get_tier() {

	$tier = false;

	if ( ! get_option( 'newsletterglue_pro_license' ) ) {
		return false;
	}

	$data = get_option( 'newsletterglue_license_info' );

	if ( ! isset( $data->price_id ) ) {
		return false;
	}

	switch( $data->price_id ) {
		case 5 :
			$tier = 'friends';
		break;
		case 4 :
			$tier = 'founding';
		break;
		case 3 :
			$tier = 'writer';
		break;
		case 2 :
			$tier = 'publisher';
		break;
		case 1 :
			$tier = 'agency';
		break;
	}

	return $tier;

}

/**
 * Duplicate a custom post or item.
 */
function newsletterglue_duplicate_item( $post = null, $post_id = 0 ) {
	global $wpdb;

	$args = array(
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'post_author'    => $post->post_author,
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_name'      => $post->post_name,
		'post_parent'    => $post->post_parent,
		'post_password'  => $post->post_password,
		'post_status'    => $post->post_status,
		'post_title'     => sprintf( __( 'Copy of %s', 'newsletter-glue' ), $post->post_title ),
		'post_type'      => $post->post_type,
		'to_ping'        => $post->to_ping,
		'menu_order'     => $post->menu_order
	);

	/*
	 * insert the post by wp_insert_post() function
	 */
	$new_post_id = wp_insert_post( $args );

	/*
	 * get all current post terms ad set them to the new post draft
	 */
	$taxonomies = get_object_taxonomies($post->post_type);
	foreach ($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
		wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
	}
	 
	/*
	 * duplicate all post meta just in two SQL queries
	 */
	$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
	if (count($post_meta_infos)!=0) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		foreach ($post_meta_infos as $meta_info) {
			$meta_key = $meta_info->meta_key;
			if( $meta_key == '_wp_old_slug' ) continue;
			$meta_value = addslashes($meta_info->meta_value);
			$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
		}
		$sql_query.= implode(" UNION ALL ", $sql_query_sel);
		$wpdb->query($sql_query);
	}

	// Unsend the cloned newsletter.
	$meta = get_post_meta( $new_post_id, '_newsletterglue', true );
	if ( ! empty( $meta ) && isset( $meta[ 'sent' ] ) ) {
		unset( $meta[ 'sent' ] );
		update_post_meta( $new_post_id, '_newsletterglue', $meta );
	}

	delete_post_meta( $new_post_id, '_ngl_results' );

	delete_post_meta( $new_post_id, '_ngl_core_pattern' );

}

/**
 * Get rgb from hex.
 */
function newsletterglue_rgb_from_hex( $color ) {
	$color = str_replace( '#', '', $color );
	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
	$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

	$rgb      = array();
	$rgb['R'] = hexdec( $color[0] . $color[1] );
	$rgb['G'] = hexdec( $color[2] . $color[3] );
	$rgb['B'] = hexdec( $color[4] . $color[5] );

	return $rgb;
}

/**
 * Darker hex color.
 */
function newsletterglue_hex_darker( $color, $factor = 30 ) { 
	$base = newsletterglue_rgb_from_hex( $color ); 
	$color = '#'; 
 
	foreach ( $base as $k => $v ) { 
		$amount = $v / 100; 
		$amount = round( $amount * $factor ); 
		$new_decimal = $v - $amount; 
 
		$new_hex_component = dechex( $new_decimal ); 
		if ( strlen( $new_hex_component ) < 2 ) { 
			$new_hex_component = "0" . $new_hex_component; 
		} 
		$color .= $new_hex_component; 
	}

	return $color; 
}
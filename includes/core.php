<?php
/**
 * Misc Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

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

	if ( ! defined( 'NGL_IN_EMAIL' ) ) {
		define( 'NGL_IN_EMAIL', true );
	}

	$data 			= get_post_meta( $post->ID, '_newsletterglue', true );
	$preview_text 	= isset( $data[ 'preview_text' ] ) ? esc_attr( $data[ 'preview_text' ] ) : '';

	$position = get_option( 'newsletterglue_position_featured' );
	if ( ! $position ) {
		$position = 'below';
	}

	// Blog title.
	$show_title 	= get_option( 'newsletterglue_add_title' );
	$link_to_post 	= get_option( 'newsletterglue_link_title' );

	// Get the email template including css tags.
	$html = newsletterglue_get_email_template( $post, $subject, $app );

	// Remove auto embed.
	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

	$the_content = '';

	// Add preview text to email.
	if ( ! empty( $preview_text ) ) {
		$the_content .= '<div class="ngl-preview-text">' . $preview_text . '</div>';
	}

	// Add logo.
	$the_content .= newsletterglue_add_logo();

	$title = isset( $post ) && isset( $post->post_title ) ? $post->post_title : $subject;

	// Link to title.
	if ( $link_to_post === 'yes' ) {
		$title = '<a href="' . get_permalink( $post->ID ) . '" class="ngl-title-to-post" target="_blank">' . $title . '</a>';
	}

	// Masthead and heading
	if ( $position == 'above' ) {
		$the_content .= newsletterglue_add_masthead_image( $post, 'above' );
		if ( $show_title !== 'no' ) {
			$the_content .= '<h1>' . $title . '</h1>';
		}
	} else {
		if ( $show_title !== 'no' ) {
			$the_content .= '<h1>' . $title . '</h1>';
		}
		$the_content .= newsletterglue_add_masthead_image( $post, 'below' );
	}

	// Post content.
	$the_content .= apply_filters( 'the_content', $post->post_content );

	// Credits.
	if ( get_option( 'newsletterglue_credits' ) ) {
		$the_content .= '<p class="ngl-credits">' . sprintf( __( 'Seamlessly sent by %s', 'newsletter-glue' ), '<a href="https://wordpress.org/plugins/newsletter-glue/">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ) . '</p>';
	}

	// Allow 3rd party to customize content tag.
	$the_content = apply_filters( 'newsletterglue_email_content_' . $app, $the_content, $post, $subject );
	$the_content = apply_filters( 'newsletterglue_email_content', $the_content, $post, $subject, $app );

	// Process content tags.
	$html = str_replace( '{title}', $subject, $html );
	$html = str_replace( '{content}', $the_content, $html );
	$html = str_replace( '{post_permalink}', get_permalink( $post->ID ), $html );
	$html = str_replace( '{post_permalink_preview}', add_query_arg( 'preview_email', $post->ID, get_permalink( $post->ID ) ), $html );
	$html = preg_replace( '/<!--(.*)-->/Uis', '', $html );

	$html = apply_filters( 'newsletterglue_generate_content', $html, $post );

	// Inline CSS.
	$emogrifier_class = '\\Pelago\\Emogrifier';
	if ( ! class_exists( $emogrifier_class ) ) {
		include_once NGL_PLUGIN_DIR . 'includes/libraries/class-emogrifier.php';
	}
	try {
		$emogrifier = new $emogrifier_class( $html );
		$html    	= $emogrifier->emogrify();
	} catch ( Exception $e ) {

	}

	// Fixes emogrifier encoding bugs.
	$html = str_replace( array( '%7B', '%7D', '%24', '%5B', '%5D' ), array( '{', '}', '$', '[', ']' ), $html );
	$html = str_replace( '@media only screen and (max-width:642px) {', '@media only screen and (max-width:642px) {' . "\r\n", $html );

	$html = wp_encode_emoji( $html );

	return apply_filters( 'newsletterglue_generated_html_output', $html, $post->ID, $app );

}

/**
 * Fix most email client issues here.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_generated_html_output', 50, 3 );
function newsletterglue_generated_html_output( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html );

	// Force image width.
	$replace = 'figure.wp-block-image img';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( ! $element->width ) {
			$element->width 	= 600;
			$element->style = $element->style . 'display: block; max-width: 100%; min-width: 100px; width: 100%;margin-bottom:0 !important;';
		}
	}

	// Force image width/height attributes.
	$replace = '.wp-block-column img';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( ! $element->width ) {
			$element->width 	= '100%';
			$element->height 	= 'auto';
		}
	}

	// Add columns wrapper as a table.
	$replace = 'figure.wp-block-image';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<div style="margin-bottom:25px;">' . $element->innertext . '</div>';
	}

	// Output column.
	$replace = '.wp-block-columns .wp-block-column';
	foreach( $output->find( $replace ) as $key => $element ) {

		$col_count = count( $output->find( $replace, $key )->parent()->find( 'div.wp-block-column' ) );

		$width = '';

		// Has style.
		if ( $output->find( $replace, $key )->style ) {
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
				$width = 'width: ' . $results[ 'flex-basis' ] . ';';
			} else {
				if ( $col_count == 2 ) {
					$width = 'width: 50%;';
				} else if ( $col_count == 3 ) {
					$width = 'width: 33.33%;';
				} else {
					$width = 'width: 100%;';
				}
			}
		}

		$valign = 'top';

		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-center' ) ) {
			$valign = 'middle';
		}
		if ( strstr( $output->find( $replace, $key )->outertext, 'is-vertically-aligned-bottom' ) ) {
			$valign = 'bottom';
		}

		$clean_width 	= str_replace( 'width: ', '', $width );
		$clean_width 	= str_replace( ';', '', $clean_width );
		$clean_width 	= trim( $clean_width );
		$dec 			= str_replace( '%', '', $clean_width ) / 100.00;
		$image_width 	= $dec * 600;

		$element->innertext = str_replace( 'width="100%"', 'width="' . absint( $image_width - 20 ) . '"', $element->innertext );
		$element->innertext = str_replace( 'width="600"', 'width="' . absint( $image_width - 20 ) . '"', $element->innertext );

		if ( $width == 'width: 100%;' ) {
			$padding = 0;
		} else {
			$padding = '20px;';
		}

		$output->find( $replace, $key )->outertext = '<td style="' . $width . 'vertical-align: ' . $valign . ';" valign="' . $valign . '">' . $element->innertext . '</td><td width="14" style="border:0;padding:0;margin:0;width:14px;">&nbsp;</td>';
	}

	// Add columns wrapper as a table.
	$replace = '.wp-block-columns';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->innertext = '<table class="ngl-table-index" border="0" width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed;border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0; margin-bottom: 0 !important;"><tr>' . $element->innertext . '</tr></table>';
	}

	// Change all figures.
	$replace = 'figure.wp-block-table';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<div style="margin-bottom: 25px;">' . $element->innertext . '</div>';
	}

	// Convert metadata to table.
	$replace = '.ngl-embed-metadata';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<table border="0" width="100%" cellpadding="20" cellspacing="0" style="table-layout: fixed;border-collapse:collapse;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0; margin-bottom: 0 !important;margin:0 !important;"><tr><td width="50%" align="left" valign="top" style="vertical-align: top;margin:0 !important;">' . $element->outertext . '</td>';
	}

	$replace = '.ngl-embed-icon';
	foreach( $output->find( $replace ) as $key => $element ) {
		$output->find( $replace, $key )->outertext = '<td width="50%" align="right" valign="top" style="vertical-align: top;margin:0 !important;">' . $element->outertext . '</td></tr></table>';
	}

	// Quotes.
	$replace = 'cite';
	foreach( $output->find( $replace ) as $key => $element ) {
		$element->style = $element->style . ';font-weight: bold;font-size: 14px;font-style:normal;';
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
		$output->find( $replace, $key )->outertext = '<div style="margin-bottom: 25px;margin-left: 0;border-' . $align . ': 5px solid #eee;padding-' . $align . ': 25px;' . $style . '">' . $element->innertext . '</div>';
	}

	$output->save();

	return ( string ) preg_replace( '/\>\s+\</m', '><', $output );
}

/**
 * This removes empty TD cells at end of content.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_fix_empty_td_cells', 60, 3 );
function newsletterglue_fix_empty_td_cells( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html );

	$replace = '.wp-block-columns table';
	foreach( $output->find( $replace ) as $key => $element ) {
		if ( strstr( $element->find( 'td', -1 )->innertext, '&nbsp;' ) ) {
			$element->find( 'td', -1 )->outertext = '';
		}
	}

	$output->save();

	return ( string ) preg_replace( '/\>\s+\</m', '><', $output );
}

/**
 * Correct td cell widths.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_fix_td_widths', 70, 3 );
function newsletterglue_fix_td_widths( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html );

	$replace = '.ngl-table-index > tr > td';
	foreach( $output->find( $replace ) as $key => $element ) {
		$td_count = count( $element->parent()->children() );
		if ( $td_count == 1 ) {
			$element->style = str_replace( 'width: 50%', 'width: 100%', $element->style );
			foreach( $element->find( '.wp-block-column img' ) as $a => $b ) {
				$b->width = 600;
			}
		}
	}

	$output->save();

	return ( string ) preg_replace( '/\>\s+\</m', '><', $output );
}

/**
 * Make sure image widths and height are accurate.
 */
add_filter( 'newsletterglue_generated_html_output', 'newsletterglue_fix_images_dom', 80, 3 );
function newsletterglue_fix_images_dom( $html, $post_id, $app ) {

	$output = new simple_html_dom();
	$output->load( $html );

	$replace = '#template_inner td';
	$width = '100%';
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
		if ( isset( $results[ 'width' ] ) ) {
			$width = $results[ 'width' ];
		}
		foreach( $element->find( 'img' ) as $a => $b ) {
			if ( strstr( $b->class, 'wp-image-' ) ) {
				$width 		= trim( $width );
				$width 		= str_replace( '%', '', $width ) / 100.00;
				$width 		= $width * 600;
				if ( $width == '100%' ) {
					$b->width = 600;
				} else {
					$b->width	= absint( $width ) - 20;
				}
				$b->height  = '';
			}
		}
	}

	$output->save();

	return ( string ) preg_replace( '/\>\s+\</m', '><', $output );

}

/**
 * Add logo image.
 */
function newsletterglue_add_logo() {

	$logo			= get_option( 'newsletterglue_logo' );
	$logo_url		= get_option( 'newsletterglue_logo_url' );
	$logo_position 	= get_option( 'newsletterglue_position_logo' );

	if ( ! $logo_position ) {
		$logo_position = 'center';
	}

	if ( $logo ) {
		if ( esc_url( $logo_url ) ) {
			return '<div class="ngl-logo ngl-logo-' . $logo_position . '"><a href="' . esc_url( $logo_url ) . '" target="_blank"><img src="' . esc_url( $logo ) . '" /></a></div>';
		} else {
			return '<div class="ngl-logo ngl-logo-' . $logo_position . '"><img src="' . esc_url( $logo ) . '" /></div>';
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

	// Use of featured image.
	if ( $use_image ) {
		$url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		if ( $url ) {
			return '<div class="ngl-masthead ngl-masthead-' . $position . '"><img src="' . $url . '"></div>';
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
		'p_colour'					=> '#333333',
		'h1_size'					=> 32,
		'h2_size'					=> 28,
		'h3_size'					=> 24,
		'h4_size'					=> 22,
		'h5_size'					=> 20,
		'h6_size'					=> 18,
		'p_size'					=> 18,
		'h1_align'					=> 'left',
		'h2_align'					=> 'left',
		'h3_align'					=> 'left',
		'h4_align'					=> 'left',
		'h5_align'					=> 'left',
		'h6_align'					=> 'left',
		'p_align'					=> 'left',
		'email_bg'					=> '#ffffff',
		'container_bg'				=> '#ffffff',
		'a_colour'					=> '#0088A0',
		'btn_bg'					=> '#0088A0',
		'btn_colour'				=> '#ffffff',
		'btn_radius'				=> 0,
		'btn_border'				=> '#0088A0',
		'btn_width'					=> 200,
		'container_padding1'		=> 20,
		'container_margin'			=> 0,
		// Mobile.
		'mobile_h1_size'			=> 28,
		'mobile_h2_size'			=> 24,
		'mobile_h3_size'			=> 22,
		'mobile_h4_size'			=> 20,
		'mobile_h5_size'			=> 18,
		'mobile_h6_size'			=> 16,
		'mobile_p_size'				=> 16,
		'mobile_container_margin' 	=> 0,
		'mobile_container_padding1' => 0,
		'mobile_btn_width'			=> 200,
	);

	return isset( $keys[ $key ] ) ? $keys[ $key ] : '';
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
	$unsupported = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'scheduled-action' );

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

	$email_font = newsletterglue_get_font_name( newsletterglue_get_theme_option( 'font' ) ) . ", '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', Arial, 'Helvetica', 'sans-serif'";

?>

.ExternalClass {width:100%;}

.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	line-height: 100%;
}

body {
	line-height: 150%;
	-webkit-text-size-adjust: none;
	-ms-text-size-adjust: none;
	margin: 0;
	padding: 0;
	background: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>;
}

body, #wrapper {
	color: #fff;
	font-family: Arial, Helvetica, sans-serif;
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
}

span.yshortcuts { color: #000; background-color:none; border:none;}
span.yshortcuts:hover,
span.yshortcuts:active,
span.yshortcuts:focus {color: #000; background-color:none; border:none;}

table td {
	border-collapse: collapse;
}

p {margin:0; padding:0; margin-bottom:0;}

h1, h2, h3, h4, h5, h6 {
	color: black; 
	line-height: 100%; 
}

a, a:link {
	color:#2A5DB0;
	text-decoration: underline;
}

blockquote p {
	text-align: inherit !important;
}

#wrapper {
	background: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>;
	padding: 0;
	margin-top: <?php echo absint( newsletterglue_get_theme_option( 'container_margin' ) ); ?>px;
	margin-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_margin' ) ); ?>px;
	<?php if ( newsletterglue_get_theme_option( 'font' ) ) : ?>
	font-family: <?php echo $email_font; ?>;
	<?php endif; ?>
	<?php if ( ! newsletterglue_get_theme_option( 'font' ) && ( isset( $_GET[ 'preview_email' ] ) || isset( $_GET[ 'view_newsletter' ] ) ) ) : ?>
	font-family: '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', Arial, 'Helvetica', 'sans-serif';
	<?php endif; ?>
}

#template_inner {
	<?php
		if ( newsletterglue_get_theme_option( 'email_bg' ) != newsletterglue_get_theme_option( 'container_bg' ) ) {
		?>
	padding-left: 20px;
	padding-right: 20px;
		<?php } ?>
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
	background: <?php echo newsletterglue_get_theme_option( 'container_bg' ); ?>;
	box-sizing: border-box;
	padding-top: <?php echo absint( newsletterglue_get_theme_option( 'container_padding1' ) ); ?>px;
	padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'container_padding1' ) ); ?>px;
	<?php if ( newsletterglue_get_theme_option( 'font' ) ) : ?>
	font-family: <?php echo $email_font; ?>;
	<?php endif; ?>
	<?php if ( ! newsletterglue_get_theme_option( 'font' ) && ( isset( $_GET[ 'preview_email' ] ) || isset( $_GET[ 'view_newsletter' ] ) ) ) : ?>
	font-family: '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', Arial, 'Helvetica', 'sans-serif';
	<?php endif; ?>
}

#template_inner * {
	max-width: 100% !important;
}

h1, h2, h3, h4, h5, h6 {
	padding: 0 !important;
	margin: 20px 0 15px 0;
	line-height: 150%;
	mso-line-height-rule: exactly;
}

h1 {
	margin-bottom: 30px !important;
}

.wp-block-columns {
	margin-bottom: 25px !important;
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

p, ul, ol {
	padding: 0;
	margin: 0 0 25px;
	font-size: 18px;
	line-height: 150%;
	mso-line-height-rule: exactly;
}

p {
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
	text-align: <?php echo newsletterglue_get_theme_option( 'p_align' ); ?>;
}

ul, ol, li {
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
	text-align: <?php echo newsletterglue_get_theme_option( 'p_align' ); ?>;
}

ul, ol {
	margin-left: 40px !important;
}

a {
	color: <?php echo newsletterglue_get_theme_option( 'a_colour' ); ?> !important;
}

figure {
	margin: 0 0 25px;
}

figcaption {
	text-align: center;
}

#template_inner img {
	max-width: 100%;
	margin: 0 auto 25px auto;
	display: block;
	height: auto;
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

#template_inner td table {
	table-layout: auto;
	width: 100%;
	border-collapse: collapse;
	border: 0;
}

#template_inner table.has-fixed-layout {
	table-layout: fixed !important;
}

#template_inner td table td {
	padding: 10px;
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	border: 1px solid #eee;
}

#template_inner td table img {
	margin: 0;
}

#template_inner td.ngl-td-clean {
	border: 0;
	padding: 0;
	width: 100%;
	font-size: inherit !important;
}

#template_inner td.ngl-td-auto {
	border: 0;
	padding: 0;
	font-size: inherit !important;
}

#template_inner table.ngl-table-tiny {
	border: 0 !important;
	table-layout: auto;
	width: auto;
}

#template_inner td.ngl-td-tiny {
	border: 0;
	padding: 0;
	width: auto !important;
	font-size: inherit;
}

#template_inner table.ngl-table-clean {
	border: 0;
}

p.ngl-credits,
p.ngl-unsubscribe {
	font-size: 13px;
	text-align: center;
	color: #999 !important;
	margin: 20px 0 20px 0 !important;
}

p.ngl-credits a,
p.ngl-unsubscribe a {
	color: #999 !important;
	text-decoration: underline;
}

p.ngl-unsubscribe {
	margin-top: 50px !important;
	padding-top: 20px !important;
	border-top: 1px solid #eee !important;
}

.ngl-masthead {
	padding: 0;
}

.ngl-masthead-above {
	padding-top: 25px;
}

.wp-block-buttons.is-content-justification-center {
	text-align: center;
}

.wp-block-buttons.is-content-justification-right {
	text-align: right;
}

.wp-block-buttons {
	margin: 0 0 25px;
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

.wp-block-button__link {
	display: inline-block;
	text-align: center;
	box-sizing: border-box;
	padding: 12px 24px;
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	text-decoration: none;
	background-color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?> !important;
	color: <?php echo newsletterglue_get_theme_option( 'btn_colour' ); ?> !important;
	min-width: <?php echo (int) newsletterglue_get_theme_option( 'btn_width' ); ?>px !important;
	border: 1px solid <?php echo newsletterglue_get_theme_option( 'btn_border' ); ?> !important;
	border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px;
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
	color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?> !important;
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

.ngl-logo {
	padding: 20px 0;
}

.ngl-logo-center {
	text-align: center;
}

.ngl-logo-left {
	text-align: left;
}

.ngl-logo-right {
	text-align: right;
}

.ngl-logo-full {
	text-align: center;
}

#template_inner .ngl-logo img {
	margin: 0 !important;
	display: inline-block !important;
	max-height: 100px;
	width: auto;
}

.has-text-align-center { text-align: center !important; }
.has-text-align-left { text-align: left !important; }
.has-text-align-right { text-align: right !important; }

@media only screen and (max-width:642px) {

	body, #wrapper {
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px;
	}

	#template_inner {
		width: auto;
		padding-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding1' ) ); ?>px !important;
		padding-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_padding1' ) ); ?>px !important;
	}

	td.ngl-td-spacer {
		width: 14px;
	}

	#wrapper {
		margin-top: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin' ) ); ?>px !important;
		margin-bottom: <?php echo absint( newsletterglue_get_theme_option( 'mobile_container_margin' ) ); ?>px !important;
	}

	h1 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h1_size' ); ?>px !important; }
	h2 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h2_size' ); ?>px !important; }
	h3 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h3_size' ); ?>px !important; }
	h4 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h4_size' ); ?>px !important; }
	h5 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h5_size' ); ?>px !important; }
	h6 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h6_size' ); ?>px !important; }

	p, ul, ol, li {
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px !important;
	}

	p.ngl-credits,
	p.ngl-unsubscribe {
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px !important;
	}

	#template_inner img {
		width: auto;
		height: auto;
	}

	.wp-block-button__link {
		min-width: <?php echo (int) newsletterglue_get_theme_option( 'mobile_btn_width' ); ?>px !important;
	}

	#template_inner .ngl-logo img {
		max-height: 60px !important;
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
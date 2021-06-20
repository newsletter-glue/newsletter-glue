<?php
/**
 * AJAX Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get AJAX URL
*/
function newsletterglue_get_ajax_url() {
	$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

	$current_url = newsletterglue_get_current_page_url();
	$ajax_url    = admin_url( 'admin-ajax.php', $scheme );

	if ( preg_match( '/^https/', $current_url ) && ! preg_match( '/^https/', $ajax_url ) ) {
		$ajax_url = preg_replace( '/^http/', 'https', $ajax_url );
	}

	return apply_filters( 'newsletterglue_get_ajax_url', $ajax_url );
}

/**
 * Update a merge tag.
 */
function newsletterglue_update_merge_tag() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$id 	= isset( $_REQUEST[ 'id' ] ) ? sanitize_title( $_REQUEST[ 'id' ] ) : '';
	$value 	= isset( $_REQUEST[ 'value' ] ) ? wp_kses_post( $_REQUEST[ 'value' ] ) : '';

	if ( empty( $id ) ) {
		wp_die( -1 );
	}

	$tags = get_option( 'newsletterglue_merge_tag_fallbacks' );

	if ( empty( $tags ) ) {
		$tags = array(
			$id	=> $value
		);
	} else {
		$tags[ $id ] = $value;
	}

	update_option( 'newsletterglue_merge_tag_fallbacks', $tags );

	die();

}
add_action( 'wp_ajax_newsletterglue_update_merge_tag', 'newsletterglue_update_merge_tag' );
add_action( 'wp_ajax_nopriv_newsletterglue_update_merge_tag', 'newsletterglue_update_merge_tag' );

/**
 * Save default theme colors.
 */
function newsletterglue_save_default_colors() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$colors = isset( $_REQUEST[ 'colors' ] ) ? $_REQUEST[ 'colors' ] : '';

	if ( empty( $colors ) ) {
		wp_die( -1 );
	}

	$colors = json_decode( stripslashes( $colors ) );

	delete_option( 'newsletterglue_theme_colors' );
	update_option( 'newsletterglue_theme_colors', $colors );

	print_r( $colors );

	die();

}
add_action( 'wp_ajax_newsletterglue_save_default_colors', 'newsletterglue_save_default_colors' );
add_action( 'wp_ajax_nopriv_newsletterglue_save_default_colors', 'newsletterglue_save_default_colors' );

/**
 * Get newsletter log.
 */
function newsletterglue_ajax_get_log() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	if ( empty( $post_id ) ) {
		wp_die( -1 );
	}

	$post 		= get_post( $post_id );
	$results 	= newsletterglue_get_past_campaigns( $post_id );
	?>
	<div class="ngl-modal">

	<a href="#" class="ngl-modal-close"><i class="dashicons dashicons-no-alt"></i></a>

	<div class="ngl-modal-content">

		<div class="ngl-modal-title">
			<a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
		</div>

		<table class="wp-list-table widefat fixed striped posts ngl-table-log">

			<thead>
				<tr>
					<td scope="col" class="ngl_subject"><?php esc_html_e( 'Subject line', 'newsletter-glue' ); ?></td>
					<td scope="col" class="ngl_status"><?php esc_html_e( 'Newsletter status', 'newsletter-glue' ); ?></td>
					<td scope="col" class="ngl_datetime"><?php esc_html_e( 'Time, Date', 'newsletter-glue' ); ?></td>
				</tr>
			</thead>

			<tbody>
				<?php if ( ! empty( $results ) ) : ?>
				<?php foreach( $results as $time => $data ) : if ( ! isset( $data['type'] ) ) continue; ?>
				<?php
					$campaign_time = get_date_from_gmt( date( 'Y-m-d H:i:s', $time ), 'G:i, Y/m/d' );
					if ( ! empty( $data[ 'type' ] ) && $data[ 'type' ] == 'schedule' ) {
						$campaign_time = get_the_time( 'G:i, Y/m/d', $post->ID );
					}
				?>
				<tr>
					<td class="ngl_subject"><?php echo esc_html( $data[ 'subject' ] ); ?></td>
					<td class="ngl_status">
						<?php
							$text = '';
							if ( $data['type'] == 'error' ) {
								$text .= '<span class="ngl-state ngl-error">' . esc_html( $data[ 'message' ] ) . '</span>';
							}
							if ( $data['type'] == 'success' ) {
								$text .= '<span class="ngl-state ngl-success">' . esc_html( $data[ 'message' ] ) . '</span>';
							}
							if ( $data['type'] == 'neutral' ) {
								$text .= '<span class="ngl-state ngl-neutral">' . esc_html( $data[ 'message' ] ) . '</span>';
							}
							if ( $data['type'] == 'schedule' ) {
								$text .= '<span class="ngl-state ngl-schedule">' . esc_html( $data[ 'message' ] ) . '</span>';
							}
							if ( isset( $data['help'] ) && ! empty( $data[ 'help' ] ) ) {
								$text .= '<span class="ngl-error"><a href="' . esc_url( $data[ 'help' ] ) . '">' . esc_html__( 'Get help', 'newsletter-glue' ) . '</a></span>';
							}
							$text .= '</span>';
							echo $text;
						?>
					</td>
					<td class="ngl_datetime"><?php echo $campaign_time; ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>

		</table>

	</div>

	</div>
	<?php

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_get_log', 'newsletterglue_ajax_get_log' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_log', 'newsletterglue_ajax_get_log' );

/**
 * Get newsletter state.
 */
function newsletterglue_ajax_get_newsletter_state() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	if ( empty( $post_id ) ) {
		wp_die( -1 );
	}

	$post 		= get_post( $post_id );

	?>
	<div class="ngl-msg-contain">
		<div class="ngl-top-msg">
			<svg xmlns="http://www.w3.org/2000/svg" width="84.915" height="30.634" viewBox="0 0 84.915 30.634"><defs><style>.aa{fill:#fff;}.aa,.bb,.cc{stroke-linecap:round;}.bb,.cc{fill:none;stroke:#17a4c6;}.cc{stroke-width:1.5px;}.dd,.ee{stroke:none;}.ee{fill:#17a4c6;}</style></defs><g transform="translate(-1853.555 -917)"><g class="aa" transform="translate(1883.125 917)"><path class="dd" d="M 44.14653396606445 30.13400459289551 L 1.79953408241272 30.13400459289551 C 1.416154026985168 30.13400459289551 0.9730140566825867 29.90056419372559 0.6968440413475037 29.55311393737793 C 0.5326249599456787 29.34651947021484 0.452938586473465 29.12885856628418 0.4805371463298798 28.96538734436035 L 9.875864028930664 1.981033325195312 C 10.29516506195068 0.9845572710037231 10.83353710174561 0.5000042319297791 11.52135372161865 0.5000042319297791 L 54.34173583984375 0.5000042319297791 C 54.4747428894043 0.5000042319297791 54.70968246459961 0.51701420545578 54.78880310058594 0.6309542059898376 C 54.87077331542969 0.7490141987800598 54.88469314575195 1.072754144668579 54.67790222167969 1.627304196357727 L 54.67537307739258 1.634084224700928 L 54.67304229736328 1.640934228897095 L 45.47742462158203 28.66435432434082 C 45.07284927368164 29.74922943115234 44.72415161132812 30.13400459289551 44.14653396606445 30.13400459289551 Z"/><path class="ee" d="M 11.52135467529297 1.000003814697266 C 11.35103225708008 1.000003814697266 10.83535766601562 1.000059127807617 10.34272766113281 2.160739898681641 L 0.9790191650390625 29.05428695678711 C 1.023708343505859 29.23724555969238 1.408954620361328 29.63400459289551 1.799522399902344 29.63400459289551 L 44.14653396606445 29.63400459289551 C 44.37565612792969 29.63400459289551 44.61389923095703 29.54671669006348 45.00654602050781 28.49604606628418 L 54.1996955871582 1.479864120483398 L 54.20435333251953 1.466163635253906 L 54.20941543579102 1.452604293823242 C 54.28692626953125 1.24473762512207 54.32093811035156 1.09759521484375 54.33520889282227 1.000003814697266 L 11.52135467529297 1.000003814697266 M 11.52135467529297 3.814697265625e-06 L 54.34173583984375 3.814697265625e-06 C 55.33694458007812 3.814697265625e-06 55.56301498413086 0.6847438812255859 55.14638519287109 1.802003860473633 L 45.94853210449219 28.83200454711914 C 45.58733367919922 29.80260467529297 45.14174270629883 30.63400459289551 44.14653396606445 30.63400459289551 L 1.799522399902344 30.63400459289551 C 0.8043136596679688 30.63400459289551 -0.1876869201660156 29.6317138671875 -0.00246429443359375 28.83200454711914 L 9.40875244140625 1.802003860473633 C 9.856613159179688 0.7298240661621094 10.5261344909668 3.814697265625e-06 11.52135467529297 3.814697265625e-06 Z"/></g><line class="bb" x1="17.119" y1="12.614" transform="translate(1893.484 918.352)"/><line class="bb" x1="27.03" y2="12.614" transform="translate(1910.604 918.352)"/><g transform="translate(1841.805 919.885)"><line class="cc" x2="32" transform="translate(12.5)"/><line class="cc" x2="24" transform="translate(17.5 6.925)"/><line class="cc" x2="12" transform="translate(26.5 15.003)"/><line class="cc" x2="4" transform="translate(32.5 21.928)"/></g></g></svg>
			<?php if ( newsletterglue_is_post_scheduled( $post->ID ) ) { ?>
			<span class="ngl-newsletter-sent ngl-muted"><?php _e( 'Your email newsletter is scheduled.', 'newsletter-glue' ); ?></span>
			<?php } else { ?>
			<span class="ngl-newsletter-sent ngl-muted"><?php _e( 'Your email newsletter is on its way!', 'newsletter-glue' ); ?></span>
			<?php } ?>
		</div>

		<div class="ngl-top-msg-view">
			<a href="#ngl-status-log" data-post-id="<?php echo absint( $post->ID ); ?>"><?php _e( 'View status log', 'newsletter-glue' ); ?></a>
			<?php
				if ( newsletterglue_is_post_scheduled( $post->ID ) ) {
					echo '<a href="#" class="ngl-reset-newsletter-pre">' . __( 'Unschedule', 'newsletter-glue' ) . '</a>';
					echo '<div class="ngl-unschedule-confirm is-hidden">
								<span class="ngl-unschedule-confirm-msg">' . __( 'Newsletter won’t be sent when post is published.', 'newsletter-glue' ) . '</span>
								<span class="ngl-unschedule-confirm-actions">
									<a href="#" class="ngl-reset-newsletter" data-post-id="' . absint( $post->ID ) . '">' . __( 'Confirm unschedule', 'newsletter-glue' ) . '</a>
									<a href="#" class="ngl-unschedule-undo">' . __( 'Go back', 'newsletter-glue' ) . '</a>
								</span>
							</div>
						';
				} else {
					echo '<a href="#" class="ngl-reset-newsletter" data-post-id="' . absint( $post->ID ) . '">' . __( 'Send another newsletter', 'newsletter-glue' ) . '</a>';
				}
			?>
		</div>
	</div>

	<div class="ngl-top-msg-right">
		<?php do_action( 'newsletterglue_common_action_hook' ); ?>
		<?php echo newsletterglue_get_review_button_html( 'post' ); ?>
		<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank" class="ngl-get-help"><i class="question circle outline icon"></i><?php echo __( 'Get help', 'newsletter-glue' ); ?></a>
	</div>
	<?php
	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_get_newsletter_state', 'newsletterglue_ajax_get_newsletter_state' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_newsletter_state', 'newsletterglue_ajax_get_newsletter_state' );

/**
 * Save a block.
 */
function newsletterglue_ajax_save_block() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$block_id 	= isset( $_POST[ 'id' ] ) ? sanitize_text_field( $_POST[ 'id' ] ) : '';
	$block		= str_replace( 'newsletterglue_block_', '', $block_id );
	$classname 	= 'NGL_Block_' . ucfirst( $block );

	if ( class_exists( $classname ) ) {
		$instance = new $classname;
		wp_send_json( call_user_func( array( $instance, 'save_settings' ) ) );
	}

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_save_block', 'newsletterglue_ajax_save_block' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_block', 'newsletterglue_ajax_save_block' );

/**
 * Use all blocks.
 */
function newsletterglue_ajax_use_all_blocks() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	$blocks 	= newsletterglue_get_blocks();

	foreach( $blocks as $block_id => $params ) {
		$use_blocks[ $block_id ] = 'yes';
	}

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_use_all_blocks', 'newsletterglue_ajax_use_all_blocks' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_use_all_blocks', 'newsletterglue_ajax_use_all_blocks' );

/**
 * Disable all blocks.
 */
function newsletterglue_ajax_disable_all_blocks() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	$blocks 	= newsletterglue_get_blocks();

	foreach( $blocks as $block_id => $params ) {
		$use_blocks[ $block_id ] = 'no';
	}

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_disable_all_blocks', 'newsletterglue_ajax_disable_all_blocks' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_disable_all_blocks', 'newsletterglue_ajax_disable_all_blocks' );

/**
 * Switch block use.
 */
function newsletterglue_ajax_use_block() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$use_blocks = get_option( 'newsletterglue_use_blocks' );
	
	if ( ! $use_blocks ) {
		$use_blocks = array();
	}

	$id = sanitize_text_field( $_POST[ 'id' ] );
	$value = sanitize_text_field( $_POST[ 'value' ] );

	$use_blocks[ $id ] = $value;

	update_option( 'newsletterglue_use_blocks', $use_blocks );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_use_block', 'newsletterglue_ajax_use_block' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_use_block', 'newsletterglue_ajax_use_block' );

/**
 * API connection.
 */
function newsletterglue_ajax_connect_api() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	// Get app.
	$app = isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->add_integration();

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_connect_api', 'newsletterglue_ajax_connect_api' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_connect_api', 'newsletterglue_ajax_connect_api' );

/**
 * Remove API Integration.
 */
function newsletterglue_ajax_remove_api() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	// Get app.
	$app = isset( $_POST['app'] ) ? sanitize_text_field( $_POST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->remove_integration();

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_remove_api', 'newsletterglue_ajax_remove_api' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_api', 'newsletterglue_ajax_remove_api' );

/**
 * Reset a sent newsletter.
 */
function newsletterglue_ajax_reset_newsletter() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	if ( $post_id ) {
		newsletterglue_reset_newsletter( $post_id );
	}

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_reset_newsletter', 'newsletterglue_ajax_reset_newsletter' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_reset_newsletter', 'newsletterglue_ajax_reset_newsletter' );

/**
 * Removes a notice.
 */
function newsletterglue_ajax_remove_notice() {
	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$key = isset( $_REQUEST[ 'key' ] ) ? absint( $_REQUEST[ 'key' ] ) : '';

	newsletterglue_remove_notice( $key, sanitize_text_field( $_REQUEST[ 'key' ]) );

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_remove_notice', 'newsletterglue_ajax_remove_notice' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_notice', 'newsletterglue_ajax_remove_notice' );

/**
 * Removes an upgrade notice.
 */
function newsletterglue_ajax_remove_upgrade_notice() {

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	update_option( 'newsletterglue_pro_upgrade_dismiss', 'yes' );

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_remove_upgrade_notice', 'newsletterglue_ajax_remove_upgrade_notice' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_upgrade_notice', 'newsletterglue_ajax_remove_upgrade_notice' );

/**
 * Removes an editor notice.
 */
function newsletterglue_ajax_remove_editor_notice() {

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	update_option( 'newsletterglue_pro_editor_notice', 'yes' );

	die();

}
add_action( 'wp_ajax_newsletterglue_ajax_remove_editor_notice', 'newsletterglue_ajax_remove_editor_notice' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_remove_editor_notice', 'newsletterglue_ajax_remove_editor_notice' );

/**
 * Test a newsletter.
 */
function newsletterglue_ajax_test_email() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$post_id = isset( $_REQUEST[ 'post_id' ] ) ? absint( $_REQUEST[ 'post_id' ] ) : '';

	// Save newsletter data.
	newsletterglue_save_data( $post_id, newsletterglue_sanitize( $_REQUEST ) );

	// Send it.
	$response = newsletterglue_send( $post_id, true );

	wp_send_json( $response );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_test_email', 'newsletterglue_ajax_test_email' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_test_email', 'newsletterglue_ajax_test_email' );

/**
 * Verify email address.
 */
function newsletterglue_ajax_verify_email() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$email 		= isset( $_REQUEST[ 'email' ] ) ? $_REQUEST[ 'email' ] : '';
	$app 		= isset( $_REQUEST[ 'app' ] ) ? sanitize_text_field( $_REQUEST['app'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	$result 	= $api->verify_email( $email );

	wp_send_json( $result );

}
add_action( 'wp_ajax_newsletterglue_ajax_verify_email', 'newsletterglue_ajax_verify_email' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_verify_email', 'newsletterglue_ajax_verify_email' );

/**
 * Save Theme Setting.
 */
function newsletterglue_ajax_save_theme_setting() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$id 	= isset( $_REQUEST[ 'id' ] ) ? sanitize_text_field( $_REQUEST[ 'id' ] ) : '';
	$value 	= isset( $_REQUEST['value'] ) ? sanitize_text_field( trim( $_REQUEST['value'] ) ) : '';

	if ( $id == 'newsletterglue_logo_url' ) {
		update_option( 'newsletterglue_logo_url', esc_url( $value ) );
		die();
	}

	if ( $id == 'ngl_add_logo' ) {
		if ( $value ) {
			update_option( 'newsletterglue_add_logo', 'yes' );
		} else {
			update_option( 'newsletterglue_add_logo', 'no' );
		}
		die();
	}

	if ( $id == 'ngl_add_title' ) {
		if ( $value ) {
			update_option( 'newsletterglue_add_title', 'yes' );
		} else {
			update_option( 'newsletterglue_add_title', 'no' );
		}
		die();
	}

	if ( $id == 'ngl_link_title' ) {
		if ( $value ) {
			update_option( 'newsletterglue_link_title', 'yes' );
		} else {
			update_option( 'newsletterglue_link_title', 'no' );
		}
		die();
	}

	if ( $id == 'ngl_link_featured' ) {
		if ( $value ) {
			update_option( 'newsletterglue_link_featured', 'yes' );
		} else {
			update_option( 'newsletterglue_link_featured', 'no' );
		}
		die();
	}

	if ( strstr( $id, 'ngl_' ) ) {
		$key = str_replace( 'ngl_', '', $id );
		update_option( 'newsletterglue_' . $key, $value );
		die();
	}

	// Misc theme options.
	$theme  = get_option( 'newsletterglue_theme' );

	if ( $id ) {
		$theme[ $id ] = $value;
	}

	update_option( 'newsletterglue_theme', $theme );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_save_theme_setting', 'newsletterglue_ajax_save_theme_setting' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_theme_setting', 'newsletterglue_ajax_save_theme_setting' );

/**
 * Reset Theme.
 */
function newsletterglue_ajax_reset_theme() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	delete_option( 'newsletterglue_theme' );
	delete_option( 'newsletterglue_logo' );
	delete_option( 'newsletterglue_logo_id' );
	delete_option( 'newsletterglue_credits' );
	delete_option( 'newsletterglue_position_logo' );
	delete_option( 'newsletterglue_position_featured' );
	delete_option( 'newsletterglue_add_title' );
	delete_option( 'newsletterglue_add_logo' );
	delete_option( 'newsletterglue_link_title' );
	update_option( 'newsletterglue_add_featured', 1 );

	if ( ! newsletterglue_is_free_version() ) {
	?>
	<div class="ngl-theme">

		<div class="ngl-theme-reset">
			<div class="ngl-theme-reset-status">
				<span class="ngl-process is-hidden is-waiting">
					<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
					<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
				</span>
				<span class="ngl-process is-hidden is-valid">
					<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
					<span class="ngl-process-text"><strong><?php _e( 'Saved', 'newsletter-glue' ); ?></strong></span>
				</span>
			</div>
			<div class="ngl-theme-reset-link"><?php _e( 'Reset to default style', 'newsletter-glue' ); ?></div>
			<div class="ngl-theme-reset-confirm is-hidden"><?php _e( 'Confirm reset (you can&rsquo;t undo after this)', 'newsletter-glue' ); ?></div>
			<div class="ngl-theme-reset-btns is-hidden"><a href="#" class="ngl-theme-reset-do"><?php _e( 'Reset', 'newsletter-glue' ); ?></a><span>|</span><a href="#" class="ngl-theme-reset-back"><?php _e( 'Go back', 'newsletter-glue' ); ?></a></div>
		</div>

		<div class="ngl-theme-preview">

			<?php include_once( NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-theme-preview.php' ); ?>

		</div>

		<div class="ngl-theme-panel">

			<?php include_once( NGL_PLUGIN_DIR . 'includes/admin/settings/views/settings-theme-panel.php' ); ?>

		</div>

	</div>
	<?php
	}

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_reset_theme', 'newsletterglue_ajax_reset_theme' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_reset_theme', 'newsletterglue_ajax_reset_theme' );

/**
 * Save Field.
 */
function newsletterglue_ajax_save_field() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$id 	= isset( $_REQUEST[ 'id' ] ) ? str_replace( 'ngl_', '', wp_kses_post( $_REQUEST[ 'id' ] ) ) : '';

	if ( $id == 'custom_css' ) {
		$value = isset( $_REQUEST['value'] ) ? nl2br( $_REQUEST['value'] ) : '';
	} else {
		$value 	= isset( $_REQUEST['value'] ) ? wp_kses_post( trim( $_REQUEST['value'] ) ) : '';
	}

	$value  = urldecode( $value );

	$app 	= newsletterglue_default_connection();

	$options = get_option( 'newsletterglue_options' );
	$result  = array();

	$single_options = apply_filters( 'newsletterglue_single_options', array( 'header_image', 'header_image_pos', 'credits', 'post_types', 'disable_plugin_css' ) );

	if ( in_array( $id, $single_options ) ) {

		update_option( 'newsletterglue_' . $id, $value );

	} else if ( $id == 'custom_css' ) {

		update_option( 'newsletterglue_css', $value );

	} else if ( $id == 'from_email' ) {

		include_once newsletterglue_get_path( $app ) . '/init.php';

		$classname 	= 'NGL_' . ucfirst( $app );
		$api		= new $classname();
		$result 	= $api->verify_email( $value );

		if ( isset( $result['success'] ) || $result === true ) {
			$options[ $app ][ $id ] = $value;
		}

	} else if ( $id == 'accent' ) {
		$theme = get_option( 'newsletterglue_theme' );
		$theme[ 'accent' ] 	    = $value;
		$theme[ 'a_colour' ] 	= $value;
		$theme[ 'btn_bg' ] 		= $value;
		$theme[ 'btn_border' ] 	= $value;
		update_option( 'newsletterglue_theme', $theme );
	} else {

		if ( ! in_array( $id, array( 'from_name' ) ) && empty( $value ) ) {
			$options[ $app ][ $id ] = '';
		}

		if ( trim( $value ) ) {

			$options[ $app ][ $id ] = $value;

		} else {

			if ( in_array( $id, array( 'from_name' ) ) ) {
				$result[ 'failed' ] = __( 'This cannot be empty', 'newsletter-glue' );
			}

		}

	}

	update_option( 'newsletterglue_options', $options );

	// We did an action here.
	if ( ! get_option( 'newsletterglue_did_action' ) ) {
		update_option( 'newsletterglue_did_action', 'yes' );
	}

	wp_send_json( $result );

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_save_field', 'newsletterglue_ajax_save_field' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_field', 'newsletterglue_ajax_save_field' );

/**
 * Save image selection.
 */
function newsletterglue_ajax_save_image() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$option_id 		= isset( $_REQUEST[ 'elem_id' ] ) ? sanitize_text_field( $_REQUEST[ 'elem_id' ] ) : '';
	$attachment_id 	= isset( $_REQUEST[ 'id' ] ) ? absint( $_REQUEST[ 'id' ] ) : '';

	if ( $attachment_id ) {

		$url = wp_get_attachment_url( $attachment_id );

		// No URL.
		if ( ! $url ) {

			delete_option( $option_id );
			delete_option( $option_id . '_id' );

			wp_send_json_error();

		}

		$data = array(
			'id'		=> $attachment_id,
			'url'		=> $url,
			'filename'	=> basename( $url ),
			'html'		=> '<a href="#" target="_blank" class="ngl-image-trigger">' . basename( $url ) . '</a><a href="' . esc_url( $url ) . '" target="_blank" class="ngl-image-icon"><i class="arrow right icon"></i></a><a href="#" class="ngl-image-remove">' . __( 'remove', 'newsletter-glue' ) . '</a>',
		);

		update_option( $option_id, $url );
		update_option( $option_id . '_id' , $attachment_id );

		wp_send_json_success( $data );

	}

	delete_option( $option_id );
	delete_option( $option_id . '_id' );

	wp_send_json_error();

}
add_action( 'wp_ajax_newsletterglue_ajax_save_image', 'newsletterglue_ajax_save_image' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_save_image', 'newsletterglue_ajax_save_image' );

/**
 * Get onboarding settings.
 */
function newsletterglue_ajax_get_onboarding_settings() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$app 	= isset( $_REQUEST['api'] ) ? sanitize_text_field( $_REQUEST['api'] ) : '';

	if ( ! in_array( $app, array_keys( newsletterglue_get_supported_apps() ) ) ) {
		wp_die( -1 );
	}

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	if ( method_exists( $api, 'connect' ) ) {
		$api->connect();
	}

	include_once newsletterglue_get_path( $app ) . '/onboarding.php';

	die();
}
add_action( 'wp_ajax_newsletterglue_ajax_get_onboarding_settings', 'newsletterglue_ajax_get_onboarding_settings' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_onboarding_settings', 'newsletterglue_ajax_get_onboarding_settings' );

/**
 * When user clicks the review button.
 */
function newsletterglue_clicked_review_button() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	update_option( 'newsletterglue_review_button_expired', 'yes' );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_clicked_review_button', 'newsletterglue_clicked_review_button' );
add_action( 'wp_ajax_nopriv_newsletterglue_clicked_review_button', 'newsletterglue_clicked_review_button' );

/**
 * Get tags.
 */
function newsletterglue_ajax_get_tags() {

	check_ajax_referer( 'newsletterglue-ajax-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( -1 );
	}

	$app 		= isset( $_REQUEST['app'] ) ? sanitize_text_field( $_REQUEST['app'] ) : '';
	$audience 	= isset( $_REQUEST['audience'] ) ? sanitize_text_field( $_REQUEST['audience'] ) : '';

	include_once newsletterglue_get_path( $app ) . '/init.php';

	$classname 	= 'NGL_' . ucfirst( $app );
	$api		= new $classname();
	if ( method_exists( $api, 'connect' ) ) {
		$api->connect();
	}

	$api->get_segments_html( $audience );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_ajax_get_tags', 'newsletterglue_ajax_get_tags' );
add_action( 'wp_ajax_nopriv_newsletterglue_ajax_get_tags', 'newsletterglue_ajax_get_tags' );

/**
 * Send deactivation feedback.
 */
function newsletterglue_deactivate() {

	check_ajax_referer( 'newsletterglue-deactivate-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( 'No cheating, huh!' );
	}

	$email         = get_option( 'admin_email' );
	$_reason       = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
	$reason_detail = sanitize_text_field( wp_unslash( $_POST['reason_detail'] ) );
	$feedback	   = sanitize_text_field( wp_unslash( $_POST['feedback'] ) );
	$reason        = '';

	if ( $_reason == '1' ) {
		$reason = 'I only needed the plugin for a short period';
	} elseif ( $_reason == '2' ) {
		$reason = 'I found a better plugin';
	} elseif ( $_reason == '3' ) {
		$reason = 'The plugin broke my site';
	} elseif ( $_reason == '4' ) {
		$reason = 'The plugin suddenly stopped working';
	} elseif ( $_reason == '5' ) {
		$reason = 'I no longer need the plugin';
	} elseif ( $_reason == '6' ) {
		$reason = 'It\'s a temporary deactivation. I\'m just debugging an issue.';
	} elseif ( $_reason == '7' ) {
		$reason = 'Other';
	}

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Deactivate',
        'reason'  			=> $reason,
        'reason_detail'		=> $reason_detail,
		'feedback'			=> $feedback,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
		'is_pro'			=> ! newsletterglue_is_free_version(),
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_deactivate', 'newsletterglue_deactivate' );
add_action( 'wp_ajax_nopriv_newsletterglue_deactivate', 'newsletterglue_deactivate' );

/**
 * Send connection feedback.
 */
function newsletterglue_send_feedback() {

	check_ajax_referer( 'newsletterglue-feedback-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( 'No cheating, huh!' );
	}

	$software		= sanitize_text_field( wp_unslash( $_POST['_software'] ) );
	$details       	= sanitize_text_field( wp_unslash( $_POST['_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Feedback',
        'name'  			=> $name,
        'details'			=> $details,
		'software'			=> $software,
		'plugin_name' 		=> 'Newsletter Glue',
		'is_pro'			=> ! newsletterglue_is_free_version(),
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_send_feedback', 'newsletterglue_send_feedback' );
add_action( 'wp_ajax_nopriv_newsletterglue_send_feedback', 'newsletterglue_send_feedback' );

/**
 * Send a bug report.
 */
function newsletterglue_bug_report() {

	check_ajax_referer( 'newsletterglue-bug-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( __( 'No cheating, huh!', 'newsletter-glue' ) );
	}

	$details       	= sanitize_text_field( wp_unslash( $_POST['_bug_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_bug_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_bug_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Bug',
        'name'  			=> $name,
        'details'			=> $details,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
		'is_pro'			=> ! newsletterglue_is_free_version(),
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_bug_report', 'newsletterglue_bug_report' );
add_action( 'wp_ajax_nopriv_newsletterglue_bug_report', 'newsletterglue_bug_report' );

/**
 * Send a feature request.
 */
function newsletterglue_feature_request() {

	check_ajax_referer( 'newsletterglue-feature-nonce', 'security' );

	if ( ! current_user_can( 'manage_newsletterglue' ) ) {
		wp_die( __( 'No cheating, huh!', 'newsletter-glue' ) );
	}

	$details       	= sanitize_text_field( wp_unslash( $_POST['_feature_details'] ) );
	$name       	= sanitize_text_field( wp_unslash( $_POST['_feature_name'] ) );
	$email         	= sanitize_email( wp_unslash( $_POST[ '_feature_email' ] ) );

	$fields = array(
        'email' 			=> $email,
        'website' 			=> get_site_url(),
        'action' 			=> 'Feature',
        'name'  			=> $name,
        'details'			=> $details,
        'blog_language'     => get_bloginfo( 'language' ),
        'wordpress_version' => get_bloginfo( 'version' ),
        'php_version'       => PHP_VERSION,
        'plugin_version'    => NGL_VERSION,
        'plugin_name' 		=> 'Newsletter Glue',
		'is_pro'			=> ! newsletterglue_is_free_version(),
	);

	$response = wp_remote_post( NGL_FEEDBACK_SERVER, array(
		'method'      => 'POST',
		'timeout'     => 5,
		'httpversion' => '1.0',
		'blocking'    => false,
		'headers'     => array(),
		'body'        => $fields,
	) );

	wp_die();

}
add_action( 'wp_ajax_newsletterglue_feature_request', 'newsletterglue_feature_request' );
add_action( 'wp_ajax_nopriv_newsletterglue_feature_request', 'newsletterglue_feature_request' );
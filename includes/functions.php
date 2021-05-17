<?php
/**
 * Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode: Archive.
 */
function newsletterglue_archive( $atts ) {
	ob_start();

	if ( ! empty( $atts[ 'sortby' ] ) && $atts[ 'sortby' ] == 'latest' ) {
		$newsletters = get_posts(
			array(
				'posts_per_page' 	=> -1,
				'post_type' 		=> 'newsletterglue',
				'orderby'			=> 'date',
				'order'				=> 'desc',
				'post_status'		=> 'publish',
			)
		);
		if ( $newsletters ) {
			?>
			<?php foreach( $newsletters as $newsletter ) { ?>
			<h3><a href="<?php echo get_permalink( $newsletter->ID ); ?>"><?php echo get_the_title( $newsletter->ID ); ?></a> &mdash; <?php echo get_the_date( 'M j, Y', $newsletter ); ?></h3>
			<?php }
		}
		return ob_get_clean();
	}

	$terms = get_terms( array(
		'taxonomy'		=> 'ngl_newsletter_cat',
		'hide_empty'	=> true,
		'orderby'		=> 'term_id',
		'order'			=> 'asc'
	) );

	if ( $terms ) {
	?>
	<div class="newsletterglue-archive">
		<?php foreach( $terms as $term ) { ?>
		<div class="newsletterglue-archive--category" style="margin-bottom: 30px;">
			<h3 class="newsletterglue-archive--category-name"><a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_html( $term->name ); ?></h3>
			<ul class="newsletterglue-archive--list">
				<?php
				$newsletters = get_posts(
					array(
						'posts_per_page' 	=> -1,
						'post_type' 		=> 'newsletterglue',
						'post_status'		=> 'publish',
						'tax_query' 		=> array(
							array(
								'taxonomy' 	=> 'ngl_newsletter_cat',
								'field' 	=> 'term_id',
								'terms' 	=> $term->term_id,
							)
						)
					)
				);
				foreach( $newsletters as $newsletter ) { ?>
				<li><a href="<?php echo get_permalink( $newsletter->ID ); ?>"><?php echo get_the_title( $newsletter->ID ); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
	</div>
	<?php
	}

	return ob_get_clean();
}
add_shortcode( 'newsletterglue_archive', 'newsletterglue_archive' );

/**
 * Creates the admin menu links.
 */
function newsletterglue_get_supported_apps() {

	$apps = array(
		'activecampaign'	=> __( 'ActiveCampaign', 'newsletter-glue' ),
		'campaignmonitor'	=> __( 'Campaign Monitor', 'newsletter-glue' ),
		'getresponse'		=> __( 'GetResponse', 'newsletter-glue' ),
		'mailchimp'			=> __( 'Mailchimp', 'newsletter-glue' ),
		'mailerlite'		=> __( 'MailerLite', 'newsletter-glue' ),
		'sendinblue'		=> __( 'Sendinblue', 'newsletter-glue' ),
		'sendy'				=> __( 'Sendy', 'newsletter-glue' ),
	);

	return apply_filters( 'newsletterglue_get_supported_apps', $apps );

}

/**
 * Get app name (Service, or API name)
 */
function newsletterglue_get_name( $app ) {

	$apps = newsletterglue_get_supported_apps();

	return isset( $apps[ $app ] ) ? $apps[ $app ] : '';

}

/**
 * Checks if app is integrated.
 */
function newsletterglue_inactive_app( $app ) {

	$apps = get_option( 'newsletterglue_integrations' );

	return ! isset( $apps[ $app ] ) ? true : false;

}

/**
 * Get the current page URL
 */
function newsletterglue_get_current_page_url() {
	global $wp;

	if ( get_option( 'permalink_structure' ) ) {

		$base = trailingslashit( home_url( $wp->request ) );

	} else {

		$base = add_query_arg( $wp->query_string, '', trailingslashit( home_url( $wp->request ) ) );
		$base = remove_query_arg( array( 'post_type', 'name' ), $base );

	}

	$scheme = is_ssl() ? 'https' : 'http';
	$uri    = set_url_scheme( $base, $scheme );

	if ( is_front_page() ) {
		$uri = home_url( '/' );
	}

	$uri = apply_filters( 'newsletterglue_get_current_page_url', $uri );

	return $uri;

}

/**
 * Update the campaign result data.
 */
function newsletterglue_add_campaign_data( $post_id, $subject = '', $result = '', $id = '' ) {

	$results   = ( array ) get_post_meta( $post_id, '_ngl_results', true );
	$time      = time();

	// Remove any scheduled events.
	if ( isset( $result[ 'type' ] ) && $result[ 'type' ] === 'schedule' ) {
		foreach( $results as $key => $data ) {
			if ( isset( $data[ 'type' ] ) && $data[ 'type' ] === 'schedule' ) {
				unset( $results[ $key ] );
			}
		}
	}

	if ( $subject ) {
		$result[ 'subject' ] = $subject;
	}

	if ( $id ) {
		$result[ 'campaign_id' ] = $id;
	}

	// Add the result to post meta.
	if ( $result ) {

		$results[ $time ] = $result;

		update_post_meta( $post_id, '_ngl_results', $results );
		update_post_meta( $post_id, '_ngl_last_result', $result );

		// Store this as notice.
		if ( isset( $result['type'] ) && $result['type'] === 'error' ) {

			$result[ 'post_id' ] = $post_id;
			$result[ 'time' ]    = $time;

			newsletterglue_add_notice( $result );

		}

	}

}

/**
 * Get option.
 */
function newsletterglue_get_option( $option_id = '', $app = '' ) {

	$options = get_option( 'newsletterglue_options' );

	if ( isset( $options[ $app ][ $option_id ] ) ) {
		return $options[ $app ][ $option_id ];
	}

	return false;
}

/**
 * Get default from name.
 */
function newsletterglue_get_default_from_name() {

	$user_id 	= get_current_user_id();
	$first_name = get_user_meta( $user_id, 'first_name', true );
	$last_name  = get_user_meta( $user_id, 'last_name', true );
	$site_name  = get_bloginfo( 'name' );

	if ( $first_name && $last_name ) {
		$from_name = $first_name . ' ' . $last_name;
	} else if ( $first_name ) {
		$from_name = $first_name;
	} else {
		$from_name = $site_name;
	}

	return apply_filters( 'newsletterglue_get_default_from_name', $from_name );
}

/**
 * Get application url.
 */
function newsletterglue_get_url( $app ) {

	$path = NGL_PLUGIN_URL . 'includes/integrations/' . $app;

	// Allow this path to be modified using WordPress filters.
	return apply_filters( 'newsletterglue_get_url', $path, $app );

}

/**
 * Get application path.
 */
function newsletterglue_get_path( $app ) {

	$path = NGL_PLUGIN_DIR . 'includes/integrations/' . $app;

	// Allow this path to be modified using WordPress filters.
	return apply_filters( 'newsletterglue_get_path', $path, $app );
}

/**
 * Get onboarding post.
 */
function newsletterglue_get_onboarding_post() {
	ob_start();

	include( 'admin/views/welcome.php' );

	return ob_get_clean();
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 */
function newsletterglue_sanitize( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'newsletterglue_sanitize', $var );
	} else {
		return is_scalar( $var ) ? wp_kses_post( $var ) : $var;
	}
}
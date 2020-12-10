<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$hide = false;

if ( ! isset( $settings->sent ) ) {
	$hide = true;
}

if ( get_post_meta( $post->ID, '_ngl_future_send', true ) ) {
	$hide = false;
}

?>

<div class="ngl-metabox ngl-send ngl-mb-mailchimp <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<input type="hidden" name="ngl_app" id="ngl_app" value="mailchimp" />

	<?php $api->show_send_option(); ?>

	<div class="ngl-metabox-if-checked is-hidden">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>

	<div class="ngl-metabox-flex">
	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Audience', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$audience = '';
				if ( isset( $settings->audience ) ) {
					$audience = $settings->audience;
				} else {
					$audience = newsletterglue_get_option( 'audience', 'mailchimp' );
					if ( ! $audience ) {
						if ( $defaults->audiences ) {
							$keys = array_keys( $defaults->audiences );
							$audience = $keys[0];
						}
					}
				}

				newsletterglue()::$the_lists = $defaults->audiences;
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_audience',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'is-required',
					'options'		=> $the_lists,
					'default'		=> $audience,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex ngl-metabox-segment">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->segment ) ) {
					$segment = $settings->segment;
				} else {
					$segment = newsletterglue_get_option( 'segment', 'mailchimp' );
				}
				if ( ! $segment ) {
					$segment = '_everyone';
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segment',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/audience/" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="external alternate icon"></i></a>' ),
					'options'		=> $audience ? $api->get_segments( $audience ) : '',
					'default'		=> $segment,
				) );
			?>
		</div>
		<?php echo $api->show_loading(); ?>
	</div>
	</div>

	<?php $api->show_from_options( $settings, $defaults, $post ); ?>

	<?php $api->show_schedule_and_image_options( $settings, $defaults, $post ); ?>

	<?php $api->show_states( $post ); ?>

	<?php $api->show_test_email( $settings, $defaults, $post ); ?>

	</div>

</div>
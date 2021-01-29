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

<div class="ngl-metabox ngl-send <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<input type="hidden" name="ngl_app" id="ngl_app" value="activecampaign" />

	<?php $api->show_send_option(); ?>

	<div class="ngl-metabox-if-checked is-hidden">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Lists', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->lists ) ) {
					$lists = $settings->lists;
				} else {
					$lists = newsletterglue_get_option( 'lists', $app );
				}

				newsletterglue()::$the_lists = $api->get_lists();
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'options'		=> $the_lists,
					'default'		=> is_array( $lists ) ? $lists : explode( ',', $lists ),
					'multiple'		=> true,
					'placeholder'	=> __( 'None selected', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<?php $api->show_from_options( $settings, $defaults, $post ); ?>

	<?php $api->show_schedule_and_image_options( $settings, $defaults, $post ); ?>

	<?php $api->show_states( $post ); ?>

	<?php $api->show_test_email( $settings, $defaults, $post ); ?>

	</div>

</div>
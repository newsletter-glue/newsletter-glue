<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden" data-screen="4">

	<div class="ngl-boarding-logo">
		<div class="ngl-logo"><img src="<?php echo NGL_PLUGIN_URL . '/assets/images/top-bar-logo.svg'; ?>" /></div>
	</div>

	<div class="ngl-boarding-step"><?php _e( 'Step 2 of 3', 'newsletter-glue' ); ?></div>

	<h3 style="max-width:100%;"><?php _e( 'Now, let&rsquo;s select your default groups and segments.', 'newsletter-glue' ); ?>
		<span><?php _e( 'You can always change this later on in the settings.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Groups', 'newsletter-glue' ); ?>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$groups  = newsletterglue_get_option( 'groups', $app );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_groups',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $api->get_groups(),
					'default'		=> explode( ',', $groups ),
					'multiple'		=> true,
					'placeholder'	=> __( 'None selected', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$segments = newsletterglue_get_option( 'segments', $app );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://app.mailerlite.com/subscribers/segments" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ),
					'options'		=> $api->get_segments(),
					'default'		=> explode( ',', $segments ),
					'class'			=> 'ngl-ajax',
					'multiple'		=> true,
					'placeholder'	=> __( 'None selected', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>

	<div class="ngl-boarding-next disabled"><span class="material-icons">arrow_forward</span><span class="ngl-boarding-next-text"><?php _e( 'next', 'newsletter-glue' ); ?></span></div>
	<div class="ngl-boarding-prev"><span class="material-icons">arrow_back</span><span class="ngl-boarding-prev-text"><?php _e( 'prev', 'newsletter-glue' ); ?></span></div>

</div>

<?php $api->load_last_onboarding_screen(); ?>
<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden" data-screen="4">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<div class="ngl-boarding-step"><?php _e( 'Step 2 of 3', 'newsletter-glue' ); ?></div>

	<h3 style="max-width:100%;"><?php _e( 'Now, let&rsquo;s select your default groups and segments.', 'newsletter-glue' ); ?>
		<span><?php _e( 'You can always change this later on in the settings.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Groups', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$groups  = newsletterglue_get_option( 'groups', 'mailerlite' );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_groups',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $api->get_groups(),
					'default'		=> explode( ',', $groups ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Everyone', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$segments = newsletterglue_get_option( 'segments', 'mailerlite' );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
					'options'		=> $api->get_segments(),
					'default'		=> explode( ',', $segments ),
					'class'			=> 'ngl-ajax',
					'multiple'		=> true,
					'placeholder'	=> __( 'Everyone', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>

	<div class="ngl-boarding-next disabled"><span class="material-icons">arrow_forward</span><span class="ngl-boarding-next-text"><?php _e( 'next', 'newsletter-glue' ); ?></span></div>
	<div class="ngl-boarding-prev"><span class="material-icons">arrow_back</span><span class="ngl-boarding-prev-text"><?php _e( 'prev', 'newsletter-glue' ); ?></span></div>

</div>

<div class="ngl-boarding alt is-hidden" data-screen="5">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<div class="ngl-boarding-step"><?php _e( 'Step 3 of 3', 'newsletter-glue' ); ?></div>

	<h3 style="max-width:100%;"><?php _e( 'Last step! Personalise your emails.', 'newsletter-glue' ); ?>
		<span><?php _e( 'You can always change this later on in the settings.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'From name', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_name',
					'helper'		=> __( 'Your subscribers will see this name in their inbox.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'from_name', 'mailerlite' ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

	<div class="ngl-settings ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'From email', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$email = newsletterglue_get_option( 'from_email', 'mailerlite' );
				if ( ! $email ) {
					$email = get_option( 'admin_email' );
				}
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_email',
					'helper'		=> __( 'Subscribers will see and reply to this email address.', 'newsletter-glue' ),
					'value'			=> $email,
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

	<div class="ngl-boarding-next disabled"><span class="material-icons">arrow_forward</span><span class="ngl-boarding-next-text"><?php _e( 'next', 'newsletter-glue' ); ?></span></div>
	<div class="ngl-boarding-prev"><span class="material-icons">arrow_back</span><span class="ngl-boarding-prev-text"><?php _e( 'prev', 'newsletter-glue' ); ?></span></div>

</div>
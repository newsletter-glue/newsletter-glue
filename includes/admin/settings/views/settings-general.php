<?php
/**
 * Settings General.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_from_name"><?php esc_html_e( 'From name', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_name',
					'helper'		=> __( 'Your subscribers will see this name in their inboxes.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'from_name', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_from_email"><?php esc_html_e( 'From email', 'newsletter-glue' ); ?></label>
			<?php $api->email_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$verify = ! $api->has_email_verify() ? 'no-support-verify' : '';
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_email',
					'helper'		=> __( 'Subscribers will see and reply to this email address.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'from_email', $app ),
					'class'			=> 'ngl-ajax ' . $verify,
				) );
			?>
			<?php if ( ! $api->has_email_verify() ) { ?>
			<div class="ngl-helper">
				<?php echo sprintf( __( 'Only use verified email addresses. %s', 'newsletter-glue' ), '<a href="' . $api->get_email_verify_help() . '" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ); ?>
			</div>
			<?php } ?>
		</div>
	</div>

</div>

<?php do_action( 'newsletterglue_edit_more_settings', $app, $api->get_settings(), true ); ?>
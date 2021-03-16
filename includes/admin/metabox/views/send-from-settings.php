<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_from_name"><?php esc_html_e( 'From name', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_name',
					'helper'		=> __( 'Your subscribers will see this name in their inboxes.', 'newsletter-glue' ),
					'value'			=> isset( $settings->from_name ) ? $settings->from_name : $defaults->from_name,
					'class'			=> 'is-required',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_from_email"><?php esc_html_e( 'From email', 'newsletter-glue' ); ?></label>
			<?php $this->email_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$verify = ! $this->has_email_verify() ? 'no-support-verify' : '';
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_email',
					'helper'		=> __( 'Subscribers will see and reply to this email address.', 'newsletter-glue' ),
					'value'			=> isset( $settings->from_email ) ? $settings->from_email : $defaults->from_email,
					'class'			=> 'is-required ' . $verify,
				) );
			?>
			<?php if ( ! $this->has_email_verify() ) { ?>
			<div class="ngl-helper">
				<?php echo sprintf( __( 'Only use verified email addresses. %s', 'newsletter-glue' ), '<a href="' . $this->get_email_verify_help() . '" target="_blank">' . __( 'Learn more', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ); ?>
			</div>
			<?php } ?>
		</div>
	</div>

</div>
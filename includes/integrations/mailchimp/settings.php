<?php
/**
 * Mailchimp.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Audience', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$audience  = newsletterglue_get_option( 'audience', $app );
				$audiences = $api->get_audiences();
				if ( ! $audience ) {
					$audience = array_keys( $audiences );
					$audience = $audience[0];
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_audience',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $audiences,
					'default'		=> $audience,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex ngl-metabox-segment">
		<div class="ngl-metabox-header">
			<label for="ngl_segment"><?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?></label>
			<?php echo $api->show_loading(); ?>
		</div>
		<div class="ngl-field">
			<?php

				$segment = newsletterglue_get_option( 'segment', $app );

				if ( ! $segment ) {
					$segment = '_everyone';
				}

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segment',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/audience/" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="external alternate icon"></i></a>' ),
					'options'		=> $api->get_segments( $audience ),
					'default'		=> $segment,
					'class'			=> 'ngl-ajax',
				) );

			?>
		</div>
	</div>

</div>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'From name', 'newsletter-glue' ); ?>
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
			<?php esc_html_e( 'From email', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_from_email',
					'helper'		=> __( 'Subscribers will see and reply to this email address.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'from_email', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

</div>

<?php $api->show_global_settings(); ?>
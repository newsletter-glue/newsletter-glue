<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-no-connection">

	<div class="ngl-metabox-flex alt4">
		<div class="ngl-metabox-msg is-notice"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-settings&tab=connect' ) ); ?>" target="_blank"><?php _e( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' ); ?></a></div>
	</div>

	<div class="ngl-metabox-if-checked ngl-metabox-placeholder">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>

	<div class="ngl-metabox-flex">
	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_audience"><?php esc_html_e( 'Audience', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_audience',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'options'		=> array(),
					'default'		=> '',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex ngl-metabox-segment">
		<div class="ngl-metabox-header">
			<label for="ngl_segment"><?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segment',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/audience/" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ),
					'options'		=> array(),
					'default'		=> '',
				) );
			?>
		</div>
	</div>
	</div>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>

<div class="ngl-metabox ngl-metabox-flex alt3 ngl-unready">

	<div class="ngl-metabox-flex ngl-metabox-flex-toggle">

		<div class="ngl-field ngl-field-master">
			<input type="hidden" name="ngl_double_confirm" id="ngl_double_confirm" value="no" />
			<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
			<label for="ngl_send_newsletter"><?php _e( 'Send as newsletter', 'newsletter-glue' ); ?> <span class="ngl-field-master-help"><?php _e( '(when post is published/updated)', 'newsletter-glue' ); ?></span></label>
		</div>

	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-not-ready is-hidden">
			<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready. Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
		</div>
	</div>

</div>
<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox">

	<div class="ngl-metabox-flex alt4">
		<div class="ngl-metabox-msg is-notice"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ngl-connect' ) ); ?>" target="_blank"><?php _e( 'Start by connecting your email software &#x21C4;', 'newsletter-glue' ); ?></a></div>
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
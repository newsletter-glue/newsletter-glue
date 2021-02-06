<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-send ngl-mb-mailchimp">

	<input type="hidden" name="ngl_app" id="ngl_app" value="<?php echo esc_attr( $app ); ?>" />

	<div class="ngl-metabox-if-checked">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>

	<div class="ngl-metabox-flex">
	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_audience"><?php esc_html_e( 'Audience', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$audience = '';
				if ( isset( $settings->audience ) ) {
					$audience = $settings->audience;
				} else {
					$audience = newsletterglue_get_option( 'audience', $app );
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
			<label for="ngl_segment"><?php esc_html_e( 'Segment / tag', 'newsletter-glue' ); ?></label>
			<?php echo $api->show_loading(); ?>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->segment ) ) {
					$segment = $settings->segment;
				} else {
					$segment = newsletterglue_get_option( 'segment', $app );
				}
				if ( ! $segment ) {
					$segment = '_everyone';
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segment',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://admin.mailchimp.com/audience/" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ),
					'options'		=> $audience ? $api->get_segments( $audience ) : '',
					'default'		=> $segment,
				) );
			?>
		</div>
	</div>
	</div>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>
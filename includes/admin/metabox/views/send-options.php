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
			<?php esc_html_e( 'Send now or save for later', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$schedule = isset( $settings->schedule ) ? $settings->schedule : 'immediately';
				if ( isset( $settings->sent ) ) {
					$schedule = 'immediately';
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_schedule',
					'options'		=> $this->get_schedule_options(),
					'default'		=> $schedule,
					'legacy'		=> true,
					'class'			=> 'is-required',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Featured image', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">

			<?php
				$add_featured = isset( $settings->add_featured ) ? $settings->add_featured : $defaults->add_featured;
			?>

			<label class="ngl-metabox-cb">
				<input type="checkbox" name="ngl_add_featured" id="ngl_add_featured" value="1" <?php checked( 1, $add_featured ); ?> />
				<span><?php echo __( 'Add featured image to the top of this newsletter.<br />Ideal image width: 1200px' ); ?></span>
			</label>

		</div>
	</div>

</div>
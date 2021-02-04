<?php
/**
 * Campaign Monitor.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'Lists', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				$lists  = newsletterglue_get_option( 'lists', $app );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $api->get_lists(),
					'default'		=> explode( ',', $lists ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select list(s)', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_segments"><?php esc_html_e( 'Segments', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php

				$segments = newsletterglue_get_option( 'segments', $app );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
					'options'		=> $api->get_segments(),
					'default'		=> explode( ',', $segments ),
					'class'			=> 'ngl-ajax',
					'multiple'		=> true,
					'placeholder'	=> __( 'Select segment(s)', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>

</div>
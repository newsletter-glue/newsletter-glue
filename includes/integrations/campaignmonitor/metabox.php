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
			<label for="ngl_lists"><?php esc_html_e( 'Lists', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->lists ) ) {
					$lists = $settings->lists;
				} else {
					$lists = newsletterglue_get_option( 'lists', $app );
				}

				newsletterglue()::$the_lists = $api->get_lists();
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'options'		=> $the_lists,
					'default'		=> is_array( $lists ) ? $lists : explode( ',', $lists ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select list(s)', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_segments"><?php esc_html_e( 'Segments', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->segments ) ) {
					$segments = $settings->segments;
				} else {
					$segments = newsletterglue_get_option( 'segments', $app );
				}

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
					'options'		=> $api->get_segments(),
					'default'		=> is_array( $segments ) ? $segments : explode( ',', $segments ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select segment(s)', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>

</div>
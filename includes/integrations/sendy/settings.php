<?php
/**
 * Sendy.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'List ID(s)', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_lists',
					'helper'		=> __( 'Comma-separated list of lists IDs. List ID is hashed, alpha-numeric.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'lists', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_brand"><?php esc_html_e( 'Brand ID', 'newsletter-glue' ); ?></label>
			<?php $api->input_verification_info(); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_brand',
					'helper'		=> __( 'You can find the numeric brand ID in your Sendy dashboard.', 'newsletter-glue' ),
					'value'			=> newsletterglue_get_option( 'brand', $app ),
					'class'			=> 'ngl-ajax',
				) );
			?>
		</div>
	</div>

</div>
<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Subject', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_subject',
					'class'			=> 'js-limit is-required',
					'helper'		=> __( 'Short, catchy subject lines get more opens.', 'newsletter-glue' ),
					'value'			=> isset( $settings->subject ) ? $settings->subject : $defaults->subject,
				) );
			?>
		</div>

	</div>

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Preview text', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_preview_text',
					'class'			=> '',
					'helper'		=> '',
					'value'			=> isset( $settings->preview_text ) ? $settings->preview_text : $defaults->preview_text,
				) );
			?>
		</div>

	</div>

</div>
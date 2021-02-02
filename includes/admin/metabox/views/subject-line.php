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
			<label for="ngl_subject"><?php esc_html_e( 'Subject', 'newsletter-glue' ); ?></label>
		</div>

		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_subject',
					'class'			=> 'is-required',
					'helper'		=> __( 'Short, catchy subject lines get more opens.', 'newsletter-glue' ),
					'value'			=> isset( $settings->subject ) ? $settings->subject : $defaults->subject,
				) );
			?>
		</div>

	</div>

	<div class="ngl-metabox-flex">

		<div class="ngl-metabox-header">
			<label for="ngl_preview_text"><?php esc_html_e( 'Preview text', 'newsletter-glue' ); ?></label>
		</div>

		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_preview_text',
					'helper'		=> __( 'Snippet of text that appears after your subject in subscribers\' inboxes.', 'newsletter-glue' ),
					'value'			=> isset( $settings->preview_text ) ? $settings->preview_text : $defaults->preview_text,
				) );
			?>
		</div>

	</div>

</div>
<?php
/**
 * ConvertKit.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Tag', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$tags = $api->get_tags();
				$tag  = newsletterglue_get_option( 'tag', $app );
				if ( ! $tag ) {
					$tag = '_everyone';
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_tag',
					'legacy'		=> true,
					'helper'		=> __( 'A specific group of subscribers.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $tags,
					'default'		=> $tag,
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
					'helper'		=> __( 'Your subscribers will see this name in their inbox.', 'newsletter-glue' ),
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

<?php $api->display_credits_setting(); ?>
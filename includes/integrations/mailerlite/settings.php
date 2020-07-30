<?php
/**
 * MailerLite.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Groups', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$groups  = newsletterglue_get_option( 'groups', $app );

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_groups',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'ngl-ajax',
					'options'		=> $api->get_groups(),
					'default'		=> explode( ',', $groups ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Everyone', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Segments', 'newsletter-glue' ); ?>
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
					'placeholder'	=> __( 'Everyone', 'newsletter-glue' ),
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
					'class'			=> 'ngl-ajax ngl-donotverify',
				) );
			?>
		</div>
	</div>

</div>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Sent by Newsletter Glue', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field ngl-credits">
			<label>
				<input type="checkbox" name="ngl_credits" id="ngl_credits" value="1" class="ngl-ajax" <?php checked( 1, get_option( 'newsletterglue_credits' ) ); ?> />
				<strong>🎉 <?php _e( 'Promote us, and we&rsquo;ll promote you back:', 'newsletter-glue' ); ?></strong>
			</label>

			<span><?php _e( 'Check this box to add the words<br />"Seamlessly sent by Newsletter Glue"<br />to the bottom of your newsletter.<br />Don&rsquo;t worry, it&rsquo;s small.', 'newsletter-glue' ); ?></span>
			<span><?php printf( __( 'Then, %s and we&rsquo;ll feature your newsletter.<br />%s', 'newsletter-glue' ), '<a href="https://ctt.ac/A25aM" target="_blank">' . __( 'let us know', 'newsletter-glue' ) . '</a>',
				'<a href="https://docs.memberhero.pro/article/5-sent-by" target="_blank" class="ngl-lighter">' . __( 'Learn more.', 'newsletter-glue' ) . '</a>' ); ?></span>

		</div>
	</div>

</div>
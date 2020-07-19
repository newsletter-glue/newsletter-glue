<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-send <?php if ( isset( $settings->sent ) ) echo 'is-hidden'; ?>">

	<input type="hidden" name="ngl_provider" id="ngl_provider" value="convertkit" />

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Send as newsletter', 'newsletter-glue' ); ?>
		</div>

		<div class="ngl-field">
			<label>
				<input type="checkbox" name="ngl_send_newsletter" id="ngl_send_newsletter" value="1" />
				<?php _e( 'Send this blog post as an email newsletter. (expand options)', 'newsletter-glue' ); ?>
			</label>
		</div>
	</div>

	<div class="ngl-metabox-if-checked is-hidden">

	<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Tag', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$tag = '';
				if ( isset( $settings->tag ) ) {
					$tag = $settings->tag;
				} else {
					if ( $defaults->tags ) {
						$keys = array_keys( $defaults->tags );
						$tag = $keys[0];
					}
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_tag',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'is-required',
					'options'		=> $defaults->tags,
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
					'value'			=> isset( $settings->from_name ) ? $settings->from_name : $defaults->from_name,
					'class'			=> 'is-required',
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
					'value'			=> isset( $settings->from_email ) ? $settings->from_email : $defaults->from_email,
					'class'			=> 'is-required',
				) );
			?>
		</div>
	</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header ngl-metabox-header-c">
			<?php esc_html_e( 'Subject', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_subject',
					'placeholder'	=> __( 'Example: Issue #3 It&rsquo;s raining cats, dogs, and skateboards', 'newsletter-glue' ),
					'class'			=> 'large js-limit is-required',
					'helper'		=> __( 'Short, catchy subject lines get more opens.', 'newsletter-glue' ),
					'value'			=> isset( $settings->subject ) ? $settings->subject : $defaults->subject,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Schedule to send', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$schedule = isset( $settings->schedule ) ? $settings->schedule : 'immediately';
				if ( isset( $settings->sent ) ) {
					$schedule = 'immediately';
				}
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_schedule',
					'options'		=> $api->get_schedule_options(),
					'default'		=> $schedule,
					'legacy'		=> true,
					'class'			=> 'is-required',
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex ngl-ready is-hidden">

		<?php if ( $post->post_status != 'publish' ) : ?>
		<div class="ngl-metabox-msg"><?php _e( 'Your email is ready to publish.' ,'newsletter-glue' ); ?></div>
		<?php endif; ?>

		<?php if ( $post->post_status == 'publish' ) : ?>
		<div class="ngl-metabox-msg"><?php _e( 'Your email is ready to publish. <strong>Update</strong> this post to send it.' ,'newsletter-glue' ); ?></div>
		<?php endif; ?>

	</div>

	<div class="ngl-metabox-flex ngl-not-ready is-hidden">
		<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready! Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Send test email to', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				newsletterglue_text_field( array(
					'id' 			=> 'ngl_test_email',
					'value'			=> isset( $settings->test_email ) ? $settings->test_email : $defaults->test_email,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex no-padding">
		<div class="ngl-metabox-header">
		&nbsp;
		</div>
		<div class="ngl-field">
			<div class="ngl-action">
				<button class="ui primary button ngl-test-email ngl-is-default" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Send test now', 'newsletter-glue' ); ?></button>
				<button class="ui primary button ngl-test-email ngl-alt ngl-is-sending" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><i class="sync alternate icon"></i><?php esc_html_e( 'Sending...', 'newsletter-glue' ); ?></button>
				<button class="ui primary button ngl-test-email ngl-alt ngl-is-valid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Sent!', 'newsletter-glue' ); ?></button>
				<button class="ui primary button ngl-test-email ngl-alt ngl-is-invalid" data-post_id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Could not send', 'newsletter-glue' ); ?></button>
			</div>
			<div class="ngl-action-link is-hidden">
				<a href="#" class="ngl-link ngl-retest"><?php esc_html_e( 'Start again', 'newsletter-glue' ); ?></a>
			</div>
		</div>
		<div class="ngl-test-result ngl-is-valid is-hidden">

		</div>
		<div class="ngl-test-result ngl-is-invalid is-hidden">

		</div>
	</div>

	</div>

</div>
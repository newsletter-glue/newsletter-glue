<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

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

	<div class="ngl-metabox-header">&nbsp;</div>

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
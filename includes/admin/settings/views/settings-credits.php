<?php
/**
 * Setting: Credits.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

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
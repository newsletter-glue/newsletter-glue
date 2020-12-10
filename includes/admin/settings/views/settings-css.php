<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ui large header">

	<?php esc_html_e( 'Custom CSS', 'newsletter-glue' ); ?>

	<div class="sub header"><?php echo wp_kses_post( sprintf( __( 'Add custom CSS to all your newsletters. %s', 'newsletter-glue' ), '<a href="https://docs.newsletterglue.com/article/13-custom-css-email" target="_blank">' . __( 'Learn more.', 'newsletter-glue' ) . '</a>' ) ); ?></div>

</div>

<div class="ngl-metabox">

	<div class="ngl-metabox-flex ngl-metabox-block ngl-ajax-field">

		<span class="ngl-process ngl-ajax is-hidden is-waiting">
			<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
			<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
		</span>

		<span class="ngl-process ngl-ajax is-hidden is-valid">
			<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
			<span class="ngl-process-text"><?php _e( 'Saved', 'newsletter-glue' ); ?></span>
		</span>

		<div class="ngl-field">
			<textarea name="ngl_custom_css" id="ngl_custom_css" class="widefat ngl-textarea" placeholder="<?php echo esc_attr_e( 'Enter custom CSS here...', 'newsletter-glue' ); ?>"><?php echo wp_strip_all_tags( get_option( 'newsletterglue_css' ), array() ); ?></textarea>
		</div>

	</div>

	<h3><?php _e( 'Advanced', 'newsletter-glue' ); ?></h3>

	<div class="ngl-metabox-flex">
		<div class="ngl-field">
			<?php
			newsletterglue_setting_checkbox(
				'ngl_disable_plugin_css',
				'',
				__( '<strong>Strip Newsletter Glue&rsquo;s default CSS from your newsletters.</strong><br />This also removes all styling chosen in Newsletter Theme Designer and newsletter blocks. Only custom CSS in the above box will be inserted into your newsletter.', 'newsletter-glue' ),
				get_option( 'newsletterglue_disable_plugin_css' )
			);
			?>
		</div>
	</div>

</div>
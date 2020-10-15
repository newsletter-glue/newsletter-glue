<?php
/**
 * Blocks UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$defaults = get_option( 'newsletterglue_block_show_hide_content' );
if ( ! $defaults ) {
	$defaults = array(
		'showemail'	=> true,
		'showblog'	=> false,
	);
}

?>

<form action="" class="ngl-popup-settings" data-block="<?php echo esc_attr( $block_id ); ?>">

	<a href="#" class="ngl-popup-close"><span class="dashicons dashicons-no-alt"></span></a>

	<div class="ngl-popup-header">
		<?php _e( 'Show/hide content', 'newsletter-glue' ); ?>
		<span><?php _e( 'Customise how this block shows up in the post editor.', 'newsletter-glue' ); ?></span>
	</div>

	<div class="ngl-popup-field-header"><?php _e( 'Show/hide - newsletter block', 'newsletter-glue' ); ?></div>

	<div class="ngl-popup-field">
		<label for="showblog">
			<input type="checkbox" id="showblog" name="showblog" value="yes" <?php if ( $defaults['showblog'] ) echo 'checked' ?> >
			<span class="ngl-block-use-switch"></span>
			<span class="ngl-block-use-label"><?php _e( 'Show in blog post', 'newsletter-glue' ); ?></span>
		</label>
	</div>

	<div class="ngl-popup-field">
		<label for="showemail">
			<input type="checkbox" id="showemail" name="showemail" value="yes" <?php if ( $defaults['showemail'] ) echo 'checked' ?> >
			<span class="ngl-block-use-switch"></span>
			<span class="ngl-block-use-label"><?php _e( 'Show in email newsletter', 'newsletter-glue' ); ?></span>
		</label>
	</div>

</form>
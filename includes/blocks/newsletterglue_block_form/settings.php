<?php
/**
 * Blocks UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$defaults = get_option( 'newsletterglue_block_form' );
if ( ! $defaults ) {
	$defaults = array(
		'show_in_blog' 	=> true,
		'show_in_email' => true,
	);
}

?>

<form action="" class="ngl-popup-settings" data-block="<?php echo esc_attr( $block_id ); ?>">

	<a href="#" class="ngl-popup-close"><span class="dashicons dashicons-no-alt"></span></a>

	<div class="ngl-popup-header">
		<?php _e( 'Subscriber form', 'newsletter-glue' ); ?>
		<span><?php _e( 'Customise how this block shows up in the post editor.', 'newsletter-glue' ); ?></span>
	</div>

	<div class="ngl-popup-field-header"><?php _e( 'Show/hide block', 'newsletter-glue' ); ?></div>

	<div class="ngl-popup-field">
		<label for="<?php echo esc_attr( $block_id ); ?>_show_in_blog">
			<input type="checkbox" id="<?php echo esc_attr( $block_id ); ?>_show_in_blog" name="<?php echo esc_attr( $block_id ); ?>_show_in_blog" value="yes" <?php if ( $defaults['show_in_blog'] ) echo 'checked' ?> >
			<span class="ngl-block-use-switch"></span>
			<span class="ngl-block-use-label"><?php _e( 'Show in blog post', 'newsletter-glue' ); ?></span>
		</label>
	</div>

	<div class="ngl-popup-field">
		<label for="<?php echo esc_attr( $block_id ); ?>_show_in_email">
			<input type="checkbox" id="<?php echo esc_attr( $block_id ); ?>_show_in_email" name="<?php echo esc_attr( $block_id ); ?>_show_in_email" value="yes" <?php if ( $defaults['show_in_email'] ) echo 'checked' ?> >
			<span class="ngl-block-use-switch"></span>
			<span class="ngl-block-use-label"><?php _e( 'Show in email newsletter', 'newsletter-glue' ); ?></span>
		</label>
	</div>

</form>
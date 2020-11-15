<?php
/**
 * Embed.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-embed" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
	<div class="ngl-embed-input">
		<input type="text" name="ngl_embed_url" id="ngl_embed_url" placeholder="<?php echo __( 'Add link here', 'newsletter-glue' ); ?>" />
	</div>

	<div class="ngl-embed-support">
		<span><?php _e( 'works for', 'newlsetter-glue' ); ?></span>
		<?php foreach( $supported_embeds as $embed ) : ?>
		<span><?php echo $embed[ 'icon' ]; ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="ngl-embed-content">
		<?php echo newsletterglue_get_embed( $block_id ); ?>
	</div>

</div>
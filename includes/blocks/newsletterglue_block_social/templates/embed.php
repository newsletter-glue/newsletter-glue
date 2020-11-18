<?php
/**
 * Embed.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-embed <?php if ( ! is_admin() && ! defined( 'REST_REQUEST' ) ) echo 'ngl-embed-frontend'; ?>" data-block-id="<?php echo esc_attr( $block_id ); ?>">

	<?php if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) : ?>
	<div class="ngl-embed-input">
		<input type="text" name="ngl_embed_url" id="ngl_embed_url" placeholder="<?php echo __( 'Paste link here', 'newsletter-glue' ); ?>" value="<?php echo $url; ?>" />
	</div>

	<div class="ngl-embed-support">
		<span><?php _e( 'works for', 'newlsetter-glue' ); ?></span>
		<?php foreach( $supported_embeds as $embed ) : ?>
		<span><?php echo $embed[ 'icon' ]; ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="ngl-embed-content">
		<?php if ( ! is_admin() && ! defined( 'REST_REQUEST' ) ) : ?>
		<?php echo $html; ?>
		<?php endif; ?>
	</div>

</div>
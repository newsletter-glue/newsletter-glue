<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex ngl-ready is-hidden">

	<?php if ( $post->post_status != 'publish' ) : ?>
	<div class="ngl-metabox-cols">
		<div class="ngl-metabox-msg"><?php _e( 'Your email is ready to publish.' ,'newsletter-glue' ); ?></div>
		<div class="ngl-metabox-optin">
			<label>
				<input type="checkbox" name="ngl_double_optin" id="ngl_double_optin" value="1" checked />
				<?php _e( 'Send email when post is published.', 'newsletter-glue' ); ?>
			</label>
			<?php if ( newsletterglue_get_count() < 3 ) : ?>
			<span class="ngl-new-tag"><?php _e( 'New', 'newsletter-glue' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( $post->post_status == 'publish' ) : ?>
	<div class="ngl-metabox-cols">
		<div class="ngl-metabox-msg"><?php _e( 'Your email is ready to publish.' ,'newsletter-glue' ); ?></div>
		<div class="ngl-metabox-optin">
			<label>
				<input type="checkbox" name="ngl_double_optin" id="ngl_double_optin" value="1" />
				<?php _e( 'Send email when post is updated.', 'newsletter-glue' ); ?>
			</label>
			<?php if ( newsletterglue_get_count() < 3 ) : ?>
			<span class="ngl-new-tag"><?php _e( 'New', 'newsletter-glue' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

</div>

<div class="ngl-metabox-flex ngl-not-ready is-hidden">
	<div class="ngl-metabox-msg is-error"><?php _e( 'Almost ready! Just fill in the blank red boxes.' ,'newsletter-glue' ); ?></div>
</div>
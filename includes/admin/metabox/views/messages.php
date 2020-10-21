<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

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
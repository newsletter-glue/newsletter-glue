<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-boarding alt is-hidden ngl-boarding-completed" data-screen="6">

	<div class="ngl-boarding-logo">
		<img src="<?php echo NGL_PLUGIN_URL . '/assets/images/logo-grey.png'; ?>" alt="" />
	</div>

	<p style="font-size: 36px;">🎉</p>

	<h3><?php _e( 'Set up complete!', 'newsletter-glue' ); ?>
		<span><?php _e( 'Here&rsquo;s how you can quickly send a post to your subscribers.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-video">
		<iframe width="560" height="315" src="https://www.youtube.com/embed/0LiLb3KKarE?controls=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>

	<h3>
		<span style="margin-top: 24px;"><?php _e( 'Now it&rsquo;s your turn.', 'newsletter-glue' ); ?></span>
		<span><?php _e( 'Try it yourself in this demo post we&rsquo;ve created.', 'newsletter-glue' ); ?></span>
	</h3>

	<div class="ngl-boarding-btn ngl-btn">
		<a href="<?php echo add_query_arg( 'ngl_onboarding', 'complete' ); ?>" class="ui primary button ngl-boarding-complete"><?php _e( 'Try demo post', 'newsletter-glue' ); ?></a>
	</div>

</div>
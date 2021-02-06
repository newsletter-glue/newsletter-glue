<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-metabox-flex alt3 alt5 ngl-reset <?php if ( $hide ) echo 'is-hidden'; ?>">

	<div class="ngl-top-msg">
		<svg xmlns="http://www.w3.org/2000/svg" width="64.006" height="44.253" viewBox="0 0 64.006 44.253"><defs><style>.a,.c{fill:#fff;}.a,.b,.c{stroke:#707070;}.a,.b{stroke-linecap:round;}.b,.f{fill:none;}.d{fill:#0088a0;}.e{stroke:none;}</style></defs><g transform="translate(-1931 -424)"><g class="a" transform="translate(1931 424)"><rect class="e" width="51" height="34" rx="2"/><rect class="f" x="0.5" y="0.5" width="50" height="33" rx="1.5"/></g><line class="b" x1="22.672" y1="19.184" transform="translate(1933.616 426.616)"/><line class="b" y1="19.184" x2="22.672" transform="translate(1956.289 426.616)"/><g transform="translate(1968.15 441.398)"><g class="c" transform="translate(0.85 0.602)"><circle class="e" cx="12.5" cy="12.5" r="12.5"/><circle class="f" cx="12.5" cy="12.5" r="12"/></g><path class="d" d="M27.418,13.99A13.428,13.428,0,1,1,13.99.563,13.428,13.428,0,0,1,27.418,13.99ZM12.437,21.1,22.4,11.138a.866.866,0,0,0,0-1.225L21.174,8.687a.866.866,0,0,0-1.225,0l-8.125,8.125L8.031,13.019a.866.866,0,0,0-1.225,0L5.581,14.244a.866.866,0,0,0,0,1.225L11.212,21.1a.866.866,0,0,0,1.225,0Z" transform="translate(-0.563 -0.563)"/></g></g></svg>
		<?php _e( 'You’ve sent this post as a newsletter before.', 'newsletter-glue' ); ?>
	</div>

	<div class="ngl-top-msg-view">
		<a href="#ngl-status-log" data-post-id="<?php echo absint( $post->ID ); ?>"><?php _e( 'View status log', 'newsletter-glue' ); ?></a>
	</div>

	<div class="ngl-top-msg-right">
		<?php echo newsletterglue_get_review_button_html( 'post' ); ?>
		<a href="https://docs.newsletterglue.com/article/11-email-delivery" target="_blank"><i class="question circle outline icon"></i><?php echo __( 'Get help', 'newsletter-glue' ); ?></a>
	</div>

</div>
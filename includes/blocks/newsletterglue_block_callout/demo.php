<?php
/**
 * Blocks UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-popup-demo" data-block="<?php echo esc_attr( $block_id ); ?>">

	<a href="#" class="ngl-popup-close"><span class="dashicons dashicons-no-alt"></span></a>

	<div class="ngl-popup-header alt"><?php _e( 'Author byline', 'newsletter-glue' ); ?></div>

	<div class="ngl-popup-video">
		<iframe width="560" height="315" src="https://www.youtube.com/embed/E3o9Or7i1J0?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>

</div>
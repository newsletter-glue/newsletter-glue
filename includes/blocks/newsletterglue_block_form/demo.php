<?php
/**
 * Blocks UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-popup-demo" data-block="<?php echo esc_attr( $block_id ); ?>">

	<a href="#" class="ngl-popup-close"><span class="dashicons dashicons-no-alt"></span></a>

	<div class="ngl-popup-header alt"><?php echo $this->get_label(); ?></div>

	<div class="ngl-popup-video">
		<iframe width="560" height="315" src="https://www.youtube.com/embed/hgtGahGC4bM?autoplay=1&modestbranding=1&autohide=1&showinfo=0&controls=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>

</div>
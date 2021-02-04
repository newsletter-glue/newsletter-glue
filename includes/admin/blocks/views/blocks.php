<?php
/**
 * Blocks UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl ngl-wrap ngl-blocks">

	<div class="ui large header">
		<?php esc_html_e( 'Newsletter Block Manager', 'newsletter-glue' ); ?>
		<div class="sub header"><?php esc_html_e( 'Manage all your newsletter blocks here.', 'newsletter-glue' ); ?></div>
		<div class="sub header header2"><?php esc_html_e( 'Disabling blocks doesn&rsquo;t affect content you&rsquo;ve already published.', 'newsletter-glue' ); ?></div>
	</div>

	<div class="ngl-block-container">

		<div class="ngl-block-global">
			<a href="#" class="ngl-block-useall"><?php _e( 'Use all available', 'newsletter-glue' ); ?></a>
			<a href="#" class="ngl-block-disableall"><?php _e( 'Disable all', 'newsletter-glue' ); ?></a>
		</div>

		<?php
			foreach( $blocks as $block_id => $params ) :
				$classname = ucfirst( str_replace( 'newsletterglue_block_', 'NGL_Block_', $block_id ) );
				if ( ! class_exists( $classname ) ) {
					continue;
				}
				$block = new $classname;
		?>
		<div class="ngl-block <?php echo ( $block->use_block() === 'yes' ) ? 'ngl-block-used' : 'ngl-block-unused'; ?> <?php echo ( $block->is_pro && newsletterglue_is_free_version() ) ? 'ngl-block-locked' : ''; ?>" data-block="<?php echo esc_attr( $block_id ); ?>">

			<div class="ngl-block-top">
				<?php echo $block->get_icon_svg(); ?>
				<a href="#" class="ngl-block-demo"><?php _e( 'See demo', 'newsletter-glue' ); ?></a>
			</div>

			<div class="ngl-block-title"><?php echo esc_html( $block->get_label() ); ?></div>
			<div class="ngl-block-desc"><?php echo esc_html( $block->get_description() ); ?></div>

			<?php if ( $block->is_pro && newsletterglue_is_free_version() ) : ?>
			<div class="ngl-block-upgrade">
				<a href="https://newsletterglue.com/pricing/" target="_blank"><?php _e( 'Upgrade to unlock &rarr;', 'newsletter-glue' ); ?></a>
			</div>
			<?php else : ?>
			<div class="ngl-block-defaults"><a href="#"><?php _e( 'Set defaults', 'newsletter-glue' ); ?></a></div>
			<div class="ngl-block-use">
				<label for="<?php echo esc_attr( $block_id ); ?>">
					<span class="ngl-block-use-label"><?php _e( 'Use block', 'newsletter-glue' ); ?></span>
					<input type="checkbox" id="<?php echo esc_attr( $block_id ); ?>" name="<?php echo esc_attr( $block_id ); ?>" value="yes" <?php echo $block->use_block() === 'yes' ? 'checked' : ''; ?> >
					<span class="ngl-block-use-switch"></span>
				</label>
			</div>
			<?php endif; ?>

			<?php
				$block->load_settings();
				$block->load_demo();
			?>

		</div>
		<?php endforeach; ?>

	</div>

</div>

<div class="ngl-popup-overlay">

	<div class="ngl-popup-panel">

	</div>

</div>
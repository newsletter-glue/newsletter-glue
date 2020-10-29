<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ui large header">

	<?php esc_html_e( 'Custom post types', 'newsletter-glue' ); ?>

	<div class="sub header"><?php echo __( 'Newsletter Glue will be enabled for the custom post types you select here.', 'newsletter-glue' ); ?></div>

</div>

<div class="ngl-metabox">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<?php esc_html_e( 'Custom post types', 'newsletter-glue' ); ?>
		</div>
		<div class="ngl-field">
			<?php
				$saved = get_option( 'newsletterglue_post_types' );
				newsletterglue_select_field( array(
					'id' 			=> 'ngl_post_types',
					'legacy'		=> true,
					'class'			=> 'ngl-ajax ngl-long-dropdown',
					'options'		=> newsletterglue_get_post_types(),
					'default'		=> $saved ? explode( ',', $saved ) : array_keys( newsletterglue_get_post_types() ),
					'multiple'		=> true,
					'placeholder'	=> __( 'Select post types...', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

</div>
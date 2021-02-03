<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox ngl-send <?php if ( ! $hide ) echo 'is-hidden'; ?>">

	<input type="hidden" name="ngl_app" id="ngl_app" value="<?php echo esc_attr( $app ); ?>" />

	<div class="ngl-metabox-if-checked">

	<?php $api->show_subject( $settings, $defaults, $post ); ?>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-flex">
			<div class="ngl-metabox-header">
				<label for="ngl_lists"><?php esc_html_e( 'Lists', 'newsletter-glue' ); ?></label>
			</div>
			<div class="ngl-field">
				<?php
					if ( isset( $settings->lists ) ) {
						$lists = $settings->lists;
					} else {
						$lists = newsletterglue_get_option( 'lists', $app );
					}

					newsletterglue()::$the_lists = $api->get_lists();
					$the_lists = newsletterglue()::$the_lists;

					newsletterglue_select_field( array(
						'id' 			=> 'ngl_lists',
						'legacy'		=> true,
						'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
						'options'		=> $the_lists,
						'default'		=> is_array( $lists ) ? $lists : explode( ',', $lists ),
						'multiple'		=> true,
						'placeholder'	=> __( 'All contact lists', 'newsletter-glue' ),
					) );
				?>
			</div>
		</div>
		<div class="ngl-metabox-flex">
		</div>
	</div>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>
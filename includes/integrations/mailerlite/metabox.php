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
			<label for="ngl_groups"><?php esc_html_e( 'Groups', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->groups ) ) {
					$groups = $settings->groups;
				} else {
					$groups = newsletterglue_get_option( 'groups', $app );
				}

				newsletterglue()::$the_lists = $api->get_groups();
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_groups',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'options'		=> $the_lists,
					'default'		=> is_array( $groups ) ? $groups : explode( ',', $groups ),
					'multiple'		=> true,
					'placeholder'	=> __( 'None selected', 'newsletter-glue' ),
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_segments"><?php esc_html_e( 'Segments', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				if ( isset( $settings->segments ) ) {
					$segments = $settings->segments;
				} else {
					$segments = newsletterglue_get_option( 'segments', $app );
				}

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_segments',
					'legacy'		=> true,
					'helper'		=> sprintf( __( 'A specific group of subscribers. %s', 'newsletter-glue' ), '<a href="https://app.mailerlite.com/subscribers/segments" target="_blank">' . __( 'Create segment', 'newsletter-glue' ) . ' <i class="arrow right icon"></i></a>' ),
					'options'		=> $api->get_segments(),
					'default'		=> is_array( $segments ) ? $segments : explode( ',', $segments ),
					'multiple'		=> true,
					'placeholder'	=> __( 'None selected', 'newsletter-glue' ),
				) );

			?>
		</div>
	</div>
	</div>

	<?php $api->show_settings( $settings, $defaults, $post ); ?>

	</div>

</div>
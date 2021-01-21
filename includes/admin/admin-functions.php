<?php
/**
 * Admin Functions.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin menu links.
 */
function newsletterglue_get_screen_ids() {

	$screen_ids = array();
	$screen_id  = sanitize_title( __( 'Newsletter Glue', 'newsletter-glue' ) );

	$post_types  = get_post_types();
	$unsupported = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block' );

	if ( is_array( $post_types ) ) {
		foreach( $post_types as $post_type ) {
			if ( ! in_array( $post_type, apply_filters( 'newsletterglue_unsupported_post_types', $unsupported ) ) ) {
				$screen_ids[] = $post_type;
				$screen_ids[] = 'edit-' . $post_type;
			}
		}
	}

	$screen_ids[] = $screen_id . '_page_ngl-connect';
	$screen_ids[] = $screen_id . '_page_ngl-settings';
	$screen_ids[] = $screen_id . '_page_ngl-blocks';

	return apply_filters( 'newsletterglue_screen_ids', $screen_ids );
}

/**
 * Plugin action links.
 */
function newsletterglue_plugin_action_links( $links ) {

	$links[ 'settings' ] = '<a href="' . admin_url( 'admin.php?page=ngl-settings' ) . '">' . esc_html__( 'Settings', 'newsletter-glue' ) . '</a>';

	return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename( NGL_PLUGIN_FILE ), 'newsletterglue_plugin_action_links', 10, 1 );

/**
 * Add deactivate modal layout.
 */
function newsletterglue_deactivate_modal() {
	global $pagenow;

	if ( 'plugins.php' !== $pagenow ) {
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/deactivate.php';

}
add_action( 'admin_footer', 'newsletterglue_deactivate_modal' );

/**
 * Send feedback regarding new connections.
 */
function newsletterglue_feedback_modal() {

	global $pagenow;

	if ( 'admin.php' !== $pagenow ) {
		return;
	}

	if ( ! isset( $_GET[ 'page' ] ) || $_GET[ 'page' ] != 'ngl-connect' ) {
		return;
	}

	require_once NGL_PLUGIN_DIR . 'includes/admin/feedback.php';

}
add_action( 'admin_footer', 'newsletterglue_feedback_modal' );

/**
 * Show support bar modals.
 */
function newsletterglue_support_bar_modals() {

	global $pagenow;

	if ( 'admin.php' !== $pagenow ) {
		return;
	}

	if ( ! isset( $_GET[ 'page' ] ) ) {
		return;
	}

	if ( $_GET['page'] === 'ngl-connect' || $_GET['page'] === 'ngl-settings' || $_GET['page'] === 'ngl-blocks' ) {
		require_once NGL_PLUGIN_DIR . 'includes/admin/bug-report.php';
		require_once NGL_PLUGIN_DIR . 'includes/admin/request-feature.php';
	}

}
add_action( 'admin_footer', 'newsletterglue_support_bar_modals' );

/**
 * Setting: Heading.
 */
function newsletterglue_setting_heading( $heading, $desc = '' ) {
	if ( strstr( $heading, 'Font size' ) ) {
		$mob_heading = __( 'Font size', 'newsletter-glue' );
	} else {
		$mob_heading = $heading;
	}
	?>
	<h2 class="ngl-desktop">
		<?php echo esc_html( $heading ); ?>
		<?php if ( $desc ) { ?>
		<span><?php echo wp_kses_post( $desc ); ?></span>
		<?php } ?>
	</h2>

	<h2 class="ngl-mobile">
		<?php echo esc_html( $mob_heading ); ?>
		<?php if ( $desc ) { ?>
		<span><?php echo wp_kses_post( $desc ); ?></span>
		<?php } ?>
	</h2>
	<?php
}

/**
 * Show save state.
 */
function newsletterglue_show_save_text() {
	?>
	<span class="ngl-process is-hidden is-waiting">
		<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
		<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
	</span>
	<span class="ngl-process is-hidden is-valid">
		<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
		<span class="ngl-process-text"><strong><?php _e( 'Saved', 'newsletter-glue' ); ?></strong></span>
	</span>
	<?php
}

/**
 * Setting: Dropdown.
 */
function newsletterglue_setting_dropdown( $id = '', $title = '', $options = array(), $helper = '', $option = null ) {
	$selected = newsletterglue_get_theme_option( $id );
	if ( strstr( $id, 'ngl_' ) ) {
		$selected = $option;
	}
	?>
	<div class="components-base-control ngl-desktop" data-option="<?php echo esc_attr( $id ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div>
				<div class="ui selection dropdown ngl-theme-input">
					<input type="hidden" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $selected ); ?>">
					<div class="default text"><?php echo esc_html( $selected ); ?></div>
					<i class="dropdown icon"></i>
					<div class="menu">
						<?php foreach( $options as $key => $value ) { ?>
						<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
							<?php if ( $id == 'ngl_position_logo' ) { ?>
								<img class="ui avatar image" src="<?php echo NGL_PLUGIN_URL . 'assets/images/' . $key . '.png'; ?>" style="width:12px;height:12px;margin-top:0;">
							<?php } ?>
							<?php echo esc_html( $value ); ?>
						</div>
						<?php } ?>
					</div>
				</div>

				<?php if ( $helper ) { ?>
				<p id="" class="components-base-control__help"><?php echo wp_kses_post( $helper ); ?></p>
				<?php } ?>
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Font colour and size.
 */
function newsletterglue_setting_colour_size( $id = '', $title = '' ) {
	$options = array(
		'left'		=> __( 'Align left', 'newsletter-glue' ),
		'center'	=> __( 'Align center', 'newsletter-glue' ),
		'right'		=> __( 'Align right', 'newsletter-glue' ),
	);

	$selected = newsletterglue_get_theme_option( $id . '_align' );

	?>
	<div class="components-base-control" data-option="<?php echo esc_attr( $id ); ?>_align">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-color ngl-desktop">
				<input type="text" class="ngl-theme-input ngl-color-field" value='<?php echo newsletterglue_get_theme_option( $id . '_colour' ); ?>' data-option="<?php echo esc_attr( $id ); ?>_colour" />
			</div>
			<div>
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" type="number" min="1" value="<?php echo newsletterglue_get_theme_option( $id . '_size' ); ?>" data-option="<?php echo esc_attr( $id ); ?>_size" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" type="number" min="1" value="<?php echo newsletterglue_get_theme_option( 'mobile_' . $id . '_size' ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>_size" >
			</div>
			<div class="ngl-alignment-container">
				<div class="ui selection dropdown ngl-theme-input">
					<input type="hidden" name="<?php echo esc_attr( $id ); ?>_align" id="<?php echo esc_attr( $id ); ?>_align" value="<?php echo esc_attr( $selected ); ?>">
					<div class="default text"><?php echo esc_html( $selected ); ?></div>
					<i class="dropdown icon"></i>
					<div class="menu">
						<?php foreach( $options as $key => $value ) { ?>
						<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
							<img class="ui avatar image" src="<?php echo NGL_PLUGIN_URL . 'assets/images/' . $key . '.png'; ?>" style="width:12px;height:12px;margin-top:0;">
							<?php echo esc_html( $value ); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Colour.
 */
function newsletterglue_setting_colour( $id = '', $title = '' ) {
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-color">
				<input type="text" class="ngl-theme-input ngl-color-field" value='<?php echo newsletterglue_get_theme_option( $id ); ?>' data-option="<?php echo esc_attr( $id ); ?>" />
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Text input.
 */
function newsletterglue_setting_text( $id = '', $title = '' ) {
	$class = 'ngl-' . str_replace( '_', '-', $id );
	?>
	<div class="components-base-control <?php echo esc_attr( $class ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="">
				<input class="components-text-control__input ngl-theme-input ngl-desktop" id="<?php echo esc_attr( $id ); ?>" type="text" value="<?php echo get_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
			</div>
			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Setting: Size.
 */
function newsletterglue_setting_size( $id = '', $title = '', $max = 999 ) {
	$class = 'ngl-' . str_replace( '_', '-', $id );
	?>
	<div class="components-base-control <?php echo esc_attr( $class ); ?>">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label" for="ngl_theme_<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-px">
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" id="ngl_theme_<?php echo esc_attr( $id ); ?>" type="number" min="0" max="<?php echo $max; ?>" value="<?php echo (int) newsletterglue_get_theme_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" id="ngl_theme_<?php echo esc_attr( $id ); ?>_mobile" type="number" min="0" max="<?php echo $max; ?>" value="<?php echo (int) newsletterglue_get_theme_option( 'mobile_' . $id ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>" >
				<span class="ngl-px">px</span>

				<?php if ( strstr( $id, 'container_padding' ) || strstr( $id, 'container_margin' ) ) { ?>
				<span class="ngl-px-helper">
					<?php _e( 'Top-bottom', 'newsletter-glue' ); ?>
				</span>
				<?php } ?>

			</div>

			<?php if ( strstr( $id, 'container_padding' ) ) { $id = 'container_padding2'; ?>
			<div class="ngl-theme-px">
				<input class="components-font-size-picker__number ngl-theme-input ngl-desktop" id="ngl_theme_<?php echo esc_attr( $id ); ?>" type="number" min="0" max="<?php echo $max; ?>" value="<?php echo (int) newsletterglue_get_theme_option( $id ); ?>" data-option="<?php echo esc_attr( $id ); ?>" >
				<input class="components-font-size-picker__number ngl-theme-input ngl-mobile" id="ngl_theme_<?php echo esc_attr( $id ); ?>_mobile" type="number" min="0" max="<?php echo $max; ?>" value="<?php echo (int) newsletterglue_get_theme_option( 'mobile_' . $id ); ?>" data-option="mobile_<?php echo esc_attr( $id ); ?>" >
				<span class="ngl-px">px</span>
				
				<?php if ( strstr( $id, 'container_padding' ) ) { ?>
				<span class="ngl-px-helper">
					<?php _e( 'Left-right', 'newsletter-glue' ); ?>
				</span>
				<?php } ?>
			</div>
			<?php } ?>

			<div>
				<?php newsletterglue_show_save_text(); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Add a setting checkbox.
 */
function newsletterglue_setting_checkbox( $id, $title, $text, $option = null, $not_boolean = false ) {

	if ( $not_boolean ) {
		$value = 'yes';
	} else {
		$value = 1;
	}
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-checkbox">
				<div class="ngl-theme-checkbox-state"><?php newsletterglue_show_save_text(); ?></div>
				<div class="ngl-theme-checkbox-input"><input type="checkbox" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="1" class="ngl-theme-input" <?php checked( $value, $option ); ?> data-option="<?php echo esc_attr( $id ); ?>" /></div>
				<div class="ngl-theme-checkbox-text"><?php echo wp_kses_post( $text ); ?></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Add an upload button.
 */
function newsletterglue_setting_upload( $id, $title ) {
	?>
	<div class="components-base-control ngl-desktop">
		<div class="components-base-control__field">
			<div>
				<label class="components-base-control__label"><?php echo esc_html( $title ); ?></label>
			</div>
			<div class="ngl-theme-upload" data-id="<?php echo esc_attr( $id ); ?>">
				<span class="ngl-theme-upload-button"><a href="#" class="ui button primary"><?php _e( 'Select image', 'newsletter-glue' ); ?></a></span>
				<span class="ngl-theme-upload-name">
					<?php
						if ( get_option( $id ) ) {
							$url = get_option( $id );
							echo '<a href="#" target="_blank" class="ngl-image-trigger">' . basename( $url ) . '</a><a href="' . esc_url( $url ) . '" target="_blank" class="ngl-image-icon"><i class="external alternate icon"></i></a><a href="#" class="ngl-image-remove">' . __( 'remove', 'newsletter-glue' ) . '</a>';
						} else {
							_e( 'No image selected', 'newsletter-glue' );
						}
					?>
				</span>
				<input type="hidden" name="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_url( get_option( $id ) ); ?>" />
				<input type="hidden" name="<?php echo esc_attr( $id ); ?>_id" id="<?php echo esc_attr( $id ); ?>_id" value="<?php echo absint( get_option( $id . '_id' ) ); ?>" />
			</div>
		</div>
	</div>
	<?php
}

/**
 * Review us button.
 */
function newsletterglue_get_review_button_html( $version = '' ) {
	$class = '';

	$eligible_time = newsletterglue_is_review_eligible();
	$opacity = 1.0;

	if ( ! $eligible_time ) {
		return;
	}

	if ( $eligible_time > ( 60 * 60 * 24 * 14 ) ) {
		$opacity = 0.7;
	}

	if ( $eligible_time > ( 60 * 60 * 24 * 21 ) ) {
		$opacity = 0.5;
	}

	if ( $eligible_time > ( 60 * 60 * 24 * 28 ) ) {
		$opacity = 0.3;
	}

	if ( $version == 'top-bar' ) {
		$class .= ' ngl-review-top';
	} else if ( $version == 'post' ) {
		$class .= ' ngl-review-post';
	}

	return '<div class="ngl-review ' . $class . '" style="opacity: ' . $opacity . '">
		<a href="https://wordpress.org/support/plugin/newsletter-glue/reviews/#new-post" target="_blank" class="ngl-review-link"><span>' . __( 'Like this plugin? Please review us', 'newsletter-glue' ) . '</span><i class="dashicons dashicons-star-filled"></i><i class="dashicons dashicons-star-filled"></i><i class="dashicons dashicons-star-filled"></i><i class="dashicons dashicons-star-filled"></i><i class="dashicons dashicons-star-filled"></i></a>
	</div>';

}

/**
 * Check if review button should be displayed.
 */
function newsletterglue_is_review_eligible() {

	// Already voted.
	if ( get_option( 'newsletterglue_review_button_expired' ) ) {
		return false;
	}

	$action = get_option( 'newsletterglue_did_action' );
	$time   = get_option( 'newsletterglue_review_activates_on' );

	if ( $action === 'yes' && $time && ( time() > $time ) ) {
		$diff   = time() - $time;
		$month  = ( 60 * 60 * 24 * 30 );
		if ( $diff > $month ) {
			update_option( 'newsletterglue_review_button_expired', 'yes' );
			return false;
		} else {
			return $diff;
		}
	}

	return false;

}
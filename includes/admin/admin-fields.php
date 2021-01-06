<?php
/**
 * Admin Fields.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Show a text field.
 */
function newsletterglue_text_field( $args ) {
	$type			= isset( $args['type'] ) ? $args['type'] : 'text';
	$id 			= isset( $args['id'] ) ? $args['id'] : '';
	$name			= isset( $args['name'] ) ? $args['name'] : $id;
	$label			= isset( $args['label'] ) ? $args['label'] : '';
	$placeholder	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$helper 		= isset( $args['helper'] ) ? $args['helper'] : '';
	$helper_right 	= isset( $args['helper_right'] ) ? $args['helper_right'] : '';
	$value 			= isset( $args['value'] ) ? $args['value'] : '';
	$class 			= isset( $args['class'] ) ? $args['class'] : '';
	?>
	<div class="ui input <?php echo strstr( $class, 'ngl-ajax' ) ? 'ngl-ajax-field' : ''; ?>">
		<?php if ( $label ) { ?>
		<div class="ngl-title">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		</div>
		<?php } ?>
		<input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>" class="<?php echo esc_attr( $class ); ?>" spellcheck="false" >

		<?php if ( strstr( $class, 'js-limit' ) ) { ?>
		<div class="ngl-limit">...</div>
		<?php } ?>

		<?php if ( strstr( $class, 'ngl-ajax' ) && ( $id != 'ngl_from_email' || strstr( $class, 'ngl-donotverify' ) ) ) { ?>
		<span class="ngl-process ngl-ajax is-hidden is-waiting">
			<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
			<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
		</span>

		<span class="ngl-process ngl-ajax is-hidden is-valid">
			<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
			<span class="ngl-process-text"><?php _e( 'Saved', 'newsletter-glue' ); ?></span>
		</span>

		<span class="ngl-process ngl-ajax is-hidden is-invalid">
			<span class="ngl-process-icon"><i class="material-icons">error_outline</i></span>
			<span class="ngl-process-text"></span>
		</span>
		<?php } ?>

		<?php if ( $id === 'ngl_from_email' && ! strstr( $class, 'ngl-donotverify' ) ) { ?>

			<span class="ngl-process <?php if ( strstr( $class, 'ngl-ajax' ) ) echo 'ngl-ajax'; ?> is-hidden is-waiting">
				<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
				<span class="ngl-process-text"><strong><?php _e( 'Verifying...', 'newsletter-glue' ); ?></strong></span>
			</span>

			<span class="ngl-process <?php if ( strstr( $class, 'ngl-ajax' ) ) echo 'ngl-ajax'; ?> is-hidden is-valid">
				<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
				<span class="ngl-process-text"></span>
			</span>

			<span class="ngl-process <?php if ( strstr( $class, 'ngl-ajax' ) ) echo 'ngl-ajax'; ?> is-hidden is-invalid">
				<span class="ngl-process-icon"><i class="material-icons">error_outline</i></span>
				<span class="ngl-process-text"></span>
			</span>

		<?php } ?>

	</div>
	<?php if ( $helper ) { ?>
	<div class="ngl-helper" <?php if ( $helper_right ) echo 'style="text-align:right;"'; ?> ><?php echo wp_kses_post( $helper ); ?></div>
	<?php
	}
}

/**
 * Show a select field.
 */
function newsletterglue_select_field( $args ) {

	$id 			= isset( $args['id'] ) ? $args['id'] : '';
	$class 			= isset( $args['class'] ) ? $args['class'] : '';
	$name			= isset( $args['name'] ) ? $args['name'] : $id;
	$label			= isset( $args['label'] ) ? $args['label'] : '';
	$placeholder	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	$helper 		= isset( $args['helper'] ) ? $args['helper'] : '';
	$helper_right 	= isset( $args['helper_right'] ) ? $args['helper_right'] : '';
	$options 		= isset( $args['options'] ) ? $args['options'] : '';
	$default 		= isset( $args['default'] ) ? $args['default'] : '';
	$legacy			= isset( $args['legacy'] ) ? $args['legacy'] : false;
	$has_icons		= isset( $args['has_icons'] ) ? true : false;
	$multiple		= isset( $args['multiple'] ) ? true : false;

	?>
	<div class="field <?php echo strstr( $class, 'ngl-ajax' ) ? 'ngl-ajax-field' : ''; ?>">

		<?php if ( $label ) { ?>
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php } ?>

		<?php if ( $legacy ) { ?>

		<select name="<?php echo esc_attr( $name ); ?><?php if ( $multiple ) echo '[]'; ?>" id="<?php echo esc_attr( $id ); ?>" class="ui dropdown <?php echo esc_attr( $class ); ?>" <?php if ( $multiple ) echo 'multiple=""'; ?> >
			<option value=""><?php echo esc_html( $placeholder ); ?></option>
			<?php if ( $options ) { ?>
				<?php foreach( $options as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php echo newsletterglue_selected( $key, $default ); ?>><?php echo esc_html( $value ); ?></option>
				<?php } ?>
			<?php } ?>
		</select>

		<?php } else { ?>

		<div class="ui selection dropdown <?php if ( $multiple ) echo 'multiple'; ?> <?php echo esc_attr( $class ); ?>">
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $default ); ?>">
			<div class="default text"><?php echo esc_html( $placeholder ); ?></div>
			<i class="dropdown icon"></i>
			<div class="menu">
				<?php foreach( $options as $key => $value ) { ?>
				<div class="item" data-value="<?php echo esc_attr( $key ); ?>">
					<?php if ( $has_icons ) { ?>
					<img class="ui avatar image" src="<?php echo newsletterglue_get_url( $key ); ?>/assets/icon.png">
					<?php } ?>
					<?php echo esc_html( $value ); ?>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php } ?>

		<?php if ( strstr( $class, 'ngl-ajax' ) ) { ?>
		<span class="ngl-process ngl-ajax is-hidden is-waiting">
			<span class="ngl-process-icon"><i class="sync alternate icon"></i></span>
			<span class="ngl-process-text"><strong><?php _e( 'Saving...', 'newsletter-glue' ); ?></strong></span>
		</span>

		<span class="ngl-process ngl-ajax is-hidden is-valid">
			<span class="ngl-process-icon"><i class="check circle outline icon"></i></span>
			<span class="ngl-process-text"><?php _e( 'Saved', 'newsletter-glue' ); ?></span>
		</span>
		<?php } ?>

		<?php if ( $helper ) { ?>
		<div class="ngl-helper" <?php if ( $helper_right ) echo 'style="text-align:right;"'; ?> ><?php echo wp_kses_post( $helper ); ?></div>
		<?php } ?>

	</div>
	<?php
}

/**
 * Returns if a value is among selected value(s).
 */
function newsletterglue_selected( $value, $selected ) {
	$output = '';
	if ( $selected ) {
		if ( ! is_array( $selected ) ) {
			if ( $value == $selected ) {
				$output = 'selected';
			}
		} else {
			if ( in_array( $value, $selected ) ) {
				$output = 'selected';
			}
		}
	}
	return $output;
}
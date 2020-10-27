<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<nav class="ngl-tab-wrapper nav-tab-wrapper">

	<?php foreach( newsletterglue_settings_tabs() as $key => $value ) : ?>

	<a href="<?php echo add_query_arg( 'tab', $key ); ?>" class="nav-tab <?php if ( $tab === $key ) echo 'nav-tab-active'; ?>"><?php echo esc_html( $value ); ?></a>

	<?php endforeach; ?>

</nav>

<div class="ngl ngl-wrap ngl-settings <?php if ( $app ) echo 'ngl-settings-' . esc_attr( $app ); ?>">

	<?php include( 'settings-' . $tab . '.php' ); ?>

	<?php if ( $app || $tab == 'css' || $tab == 'additional' ) : ?>

	<?php if ( $tab != 'theme' ) : ?>
	<div class="ngl-metabox ngl-metabox-flex ngl-metabox-flex2">
		<a href="#" class="ui button basic ngl-settings-save"><?php _e( 'Save', 'newsletter-glue' ); ?></a>
	</div>
	<?php endif; ?>

	<?php endif; ?>

	<?php echo newsletterglue_get_review_button_html(); ?>

</div>
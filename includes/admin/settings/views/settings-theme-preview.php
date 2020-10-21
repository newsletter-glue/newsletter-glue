<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$font_family = newsletterglue_get_theme_option( 'font' );

$position = get_option( 'newsletterglue_position_featured' );
if ( ! $position ) {
	$position = 'below';
}

$logo = get_option( 'newsletterglue_logo' );

$logo_position = get_option( 'newsletterglue_position_logo' );
if ( ! $logo_position ) {
	$logo_position = 'centre';
}

?>

<div class="ngl-email ngl-desktop" style="background-color: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>; <?php if ( $font_family ) : ?>font-family: <?php echo $font_family; ?>;<?php endif; ?>">

	<div class="ngl-email-container" style="margin-top: <?php echo (int) newsletterglue_get_theme_option( 'container_margin' ); ?>px;margin-bottom: <?php echo (int) newsletterglue_get_theme_option( 'container_margin' ); ?>px;padding: <?php echo (int) newsletterglue_get_theme_option( 'container_padding1' ); ?>px <?php echo (int) newsletterglue_get_theme_option( 'container_padding2' ); ?>px;background-color: <?php echo newsletterglue_get_theme_option( 'container_bg' ); ?>">
	
		<div class="ngl-email-logo ngl-desktop ngl-logo-<?php echo esc_attr( $logo_position ); ?> <?php if ( ! $logo ) echo 'is-hidden'; ?>">
			<img src="<?php echo esc_url( $logo ); ?>" alt="" />
		</div>

		<?php if ( $position == 'above' ) : ?>
		<div class="ngl-desktop ngl-masthead ngl-masthead-above"><img src="<?php echo newsletterglue()->assets_url(); ?>/email/header.jpg" alt="" /></div>
		<?php endif; ?>

		<h1 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h1_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h1_colour' ); ?>;"><?php _e( 'H1: This is a demo email newsletter', 'newsletter-glue' ); ?></h1>

		<?php if ( $position == 'below' ) : ?>
		<div class="ngl-desktop ngl-masthead ngl-masthead-below"><img src="<?php echo newsletterglue()->assets_url(); ?>/email/header.jpg" alt="" /></div>
		<?php endif; ?>

		<h2 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h2_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h2_colour' ); ?>;"><?php _e( 'H2: I made myself a snowball', 'newsletter-glue' ); ?></h2>

		<h3 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h3_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h3_colour' ); ?>;"><?php _e( 'H3: As perfect as could be.', 'newsletter-glue' ); ?></h3>

		<p class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;"><?php _e( 'I thought I&rsquo;d keep it as a pet<br />And let it sleep with me.<br />I made it some pajamas', 'newsletter-glue' ); ?></p>

		<h4 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h4_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h4_colour' ); ?>;"><?php _e( 'H4: And a pillow for its head.', 'newsletter-glue' ); ?></h4>

		<h5 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h5_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h5_colour' ); ?>;"><?php _e( 'H5: Then last night it ran away,', 'newsletter-glue' ); ?></h5>

		<h6 class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'h6_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h6_colour' ); ?>;"><?php _e( 'H6: But first it wet the bed.', 'newsletter-glue' ); ?></h6>

		<p class="ngl-desktop" style="font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;"><a href="#" style="color: <?php echo newsletterglue_get_theme_option( 'a_colour' ); ?> !important"><?php _e( 'Snowball, Shel Silverstein', 'newsletter-glue' ); ?></a></p>

		<div class="wp-block-button"><p><a class="wp-block-button__link" href="#" style="background-color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?>;border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px;border: 1px solid <?php echo newsletterglue_get_theme_option( 'btn_border' ); ?>;color: <?php echo newsletterglue_get_theme_option( 'btn_colour' ); ?> !important;width: <?php echo (int) newsletterglue_get_theme_option( 'btn_width' ); ?>px;"><?php _e( 'Read more', 'newsleter-glue' ); ?></a></p></div>

		<div class="ngl-credits is-hidden"><?php echo sprintf( __( 'Seamlessly sent by %s', 'newsletter-glue' ), '<a href="https://wordpress.org/plugins/newsletter-glue/">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ); ?></div>

	</div>

</div>

<div class="ngl-email ngl-mobile" style="background-color: <?php echo newsletterglue_get_theme_option( 'email_bg' ); ?>; <?php if ( $font_family ) : ?>font-family: <?php echo $font_family; ?>;<?php endif; ?>">

	<div class="ngl-email-container" style="margin-top: <?php echo (int) newsletterglue_get_theme_option( 'mobile_container_margin' ); ?>px;margin-bottom: <?php echo (int) newsletterglue_get_theme_option( 'mobile_container_margin' ); ?>px;padding: <?php echo (int) newsletterglue_get_theme_option( 'mobile_container_padding1' ); ?>px <?php echo (int) newsletterglue_get_theme_option( 'mobile_container_padding2' ); ?>px;background-color: <?php echo newsletterglue_get_theme_option( 'container_bg' ); ?>">

		<div class="ngl-email-logo ngl-mobile ngl-logo-<?php echo esc_attr( $logo_position ); ?> <?php if ( ! $logo ) echo 'is-hidden'; ?>">
			<img src="<?php echo esc_url( $logo ); ?>" alt="" />
		</div>

		<?php if ( $position == 'above' ) : ?>
		<div class="ngl-mobile ngl-masthead ngl-masthead-above"><img src="<?php echo newsletterglue()->assets_url(); ?>/email/header.jpg" alt="" /></div>
		<?php endif; ?>

		<h1 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h1_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h1_colour' ); ?>;"><?php _e( 'H1: This is a demo email newsletter', 'newsletter-glue' ); ?></h1>

		<?php if ( $position == 'below' ) : ?>
		<div class="ngl-mobile ngl-masthead ngl-masthead-below"><img src="<?php echo newsletterglue()->assets_url(); ?>/email/header.jpg" alt="" /></div>
		<?php endif; ?>

		<h2 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h2_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h2_colour' ); ?>;"><?php _e( 'H2: I made myself a snowball', 'newsletter-glue' ); ?></h2>

		<h3 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h3_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h3_colour' ); ?>;"><?php _e( 'H3: As perfect as could be.', 'newsletter-glue' ); ?></h3>

		<p class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;"><?php _e( 'I thought I&rsquo;d keep it as a pet<br />And let it sleep with me.<br />I made it some pajamas', 'newsletter-glue' ); ?></p>

		<h4 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h4_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h4_colour' ); ?>;"><?php _e( 'H4: And a pillow for its head.', 'newsletter-glue' ); ?></h4>

		<h5 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h5_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h5_colour' ); ?>;"><?php _e( 'H5: Then last night it ran away,', 'newsletter-glue' ); ?></h5>

		<h6 class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h6_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h6_colour' ); ?>;"><?php _e( 'H6: But first it wet the bed.', 'newsletter-glue' ); ?></h6>

		<p class="ngl-mobile" style="font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;"><a href="#" style="color: <?php echo newsletterglue_get_theme_option( 'a_colour' ); ?> !important"><?php _e( 'Snowball, Shel Silverstein', 'newsletter-glue' ); ?></a></p>

		<div class="wp-block-button"><p><a class="wp-block-button__link" href="#" style="background-color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?>;border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px;border: 1px solid <?php echo newsletterglue_get_theme_option( 'btn_border' ); ?>;color: <?php echo newsletterglue_get_theme_option( 'btn_colour' ); ?> !important;width: <?php echo (int) newsletterglue_get_theme_option( 'mobile_btn_width' ); ?>px;"><?php _e( 'Read more', 'newsleter-glue' ); ?></a></p></div>

		<div class="ngl-credits is-hidden"><?php echo sprintf( __( 'Seamlessly sent by %s', 'newsletter-glue' ), '<a href="https://wordpress.org/plugins/newsletter-glue/">' . __( 'Newsletter Glue', 'newsletter-glue' ) . '</a>' ); ?></div>

	</div>

</div>
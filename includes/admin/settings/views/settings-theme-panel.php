<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="components-panel__body is-opened">

	<div class="components-panel-inner">
	<?php
		newsletterglue_setting_heading( __( 'Email container', 'newsletter-glue' ) );

		newsletterglue_setting_colour( 'email_bg', __( 'Email background', 'newsletter-glue' ) );
		newsletterglue_setting_colour( 'container_bg', __( 'Email container', 'newsletter-glue' ) );
		newsletterglue_setting_size( 'container_padding1', __( 'Padding', 'newsletter-glue' ) );
		newsletterglue_setting_size( 'container_margin', __( 'Margin', 'newsletter-glue' ) );

		newsletterglue_setting_heading( __( 'Font size and colour', 'newsletter-glue' ) );

		newsletterglue_setting_dropdown(
			'font',
			__( 'Font', 'newsletter-glue' ),
			newsletterglue_get_email_fonts(),
			__( 'These basic fonts will work for most email clients.', 'newsletter-glue' )
		);

		newsletterglue_setting_colour_size( 'h1', __( 'H1', 'newsletter-glue' ) );
		newsletterglue_setting_colour_size( 'h2', __( 'H2', 'newsletter-glue' ) );
		newsletterglue_setting_colour_size( 'h3', __( 'H3', 'newsletter-glue' ) );

		echo '<a href="#" class="ngl-theme-more">' . __( 'H4, H5, H6...', 'newsletter-glue' ) . '</a>';

		echo '<div class="ngl-theme-hidden">';
		newsletterglue_setting_colour_size( 'h4', __( 'H4', 'newsletter-glue' ) );
		newsletterglue_setting_colour_size( 'h5', __( 'H5', 'newsletter-glue' ) );
		newsletterglue_setting_colour_size( 'h6', __( 'H6', 'newsletter-glue' ) );
		echo '<a href="#" class="ngl-theme-less">' . __( 'show less options', 'newsletter-glue' ) . '</a>';
		echo '</div>';

		newsletterglue_setting_colour_size( 'p', __( 'Paragraph', 'newsletter-glue' ) );

		newsletterglue_setting_colour( 'a_colour', __( 'Links', 'newsletter-glue' ) );

		newsletterglue_setting_heading( __( 'Buttons', 'newsletter-glue' ), __( 'Add this button when writing a post by typing <i>/buttons</i>', 'newsletter-glue' ) );

		newsletterglue_setting_colour( 'btn_bg', __( 'Button fill', 'newsletter-glue' ) );
		newsletterglue_setting_colour( 'btn_border', __( 'Button outline', 'newsletter-glue' ) );
		newsletterglue_setting_colour( 'btn_colour', __( 'Text', 'newsletter-glue' ) );

		newsletterglue_setting_size( 'btn_radius', __( 'Border radius', 'newsletter-glue' ), 30 );
		newsletterglue_setting_size( 'btn_width', __( 'Minimum button width', 'newsletter-glue' ) );

		echo '<div class="ngl-desktop">';
		newsletterglue_setting_heading( __( 'Newsletter logo', 'newsletter-glue' ) );

		newsletterglue_setting_upload( 'newsletterglue_logo', __( 'Add logo', 'newsletter-glue' ) );

		newsletterglue_setting_dropdown(
			'ngl_position_logo',
			__( 'Align', 'newsletter-glue' ),
			array(
				'left'		=> __( 'Align left', 'newsletter-glue' ),
				'center'	=> __( 'Align center', 'newsletter-glue' ),
				'right'		=> __( 'Align right', 'newsletter-glue' ),
				'full'		=> __( 'Full width', 'newsletter-glue' ),
			),
			null,
			get_option( 'newsletterglue_position_logo' ) ? get_option( 'newsletterglue_position_logo' ) : 'center'
		);

		newsletterglue_setting_heading( __( 'Featured image', 'newsletter-glue' ) );

		newsletterglue_setting_checkbox(
			'ngl_add_featured',
			__( 'Add featured image', 'newsletter-glue' ),
			__( 'Add featured image to the top of each newsletter.<br /><br />Ideal image width: 1200px', 'newsletter-glue' ),
			get_option( 'newsletterglue_add_featured' )
		);

		newsletterglue_setting_dropdown(
			'ngl_position_featured',
			__( 'Position featured image', 'newsletter-glue' ),
			array(
				'below'		=> __( 'Below headline', 'newsletter-glue' ),
				'above'		=> __( 'Above headline', 'newsletter-glue' ),
			),
			null,
			get_option( 'newsletterglue_position_featured' ) ? get_option( 'newsletterglue_position_featured' ) : 'below'
		);

		newsletterglue_setting_heading( __( 'Blog post title', 'newsletter-glue' ) );

		newsletterglue_setting_checkbox(
			'ngl_add_title',
			__( 'Add blog post title', 'newsletter-glue' ),
			__( 'Add blog post title to the top of each newsletter.<br /><br />Remove it for more control over your newsletter design.', 'newsletter-glue' ),
			get_option( 'newsletterglue_add_title' ) ? get_option( 'newsletterglue_add_title' ) : 'yes',
			true
		);

		echo '</div>';
	?>
	</div>

	<?php
		echo '<div class="ngl-theme-supportus ngl-desktop">';

		newsletterglue_setting_heading( __( 'Support us', 'newsletter-glue' ) );

		newsletterglue_setting_checkbox(
			'ngl_credits',
			__( 'Sent by', 'newsletter-glue' ),
			sprintf( __( 'Add "Seamlessly sent by Newsletter Glue" to the bottom of your newsletter.<br /><br />Then, %s, so we can say thank you!', 'newsletter-glue' ), '<a href="https://ctt.ac/A25aM" target="_blank">' . __( 'let us know on Twitter', 'newsletter-glue' ) . '</a>' ),
			get_option( 'newsletterglue_credits' )
		);

		echo '</div>';
	?>

</div>
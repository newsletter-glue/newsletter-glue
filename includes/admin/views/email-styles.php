<?php
/**
 * Email Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$font_family 	= newsletterglue_get_theme_option( 'font' );
$email_bg		= newsletterglue_get_theme_option( 'email_bg' );
$container_bg	= newsletterglue_get_theme_option( 'container_bg' );
$border_color 	= newsletterglue_get_theme_option( 'btn_border' );

?>

<html>
<head>

<?php do_action( 'newsletterglue_newsletter_head_tag' ); ?>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style type="text/css">

body {
	-webkit-text-size-adjust: 100%;
	line-height: 1.5;
}

#wrapper {
	background: <?php echo $email_bg; ?>;
	padding: 0;
	padding-top: <?php echo newsletterglue_get_theme_option( 'container_margin' ); ?>px;
	padding-bottom: <?php echo newsletterglue_get_theme_option( 'container_margin' ); ?>px;
	<?php if ( $font_family ) : ?>
	font-family: <?php echo newsletterglue_get_font_name( $font_family ); ?>;
	<?php endif; ?>
}

#template_inner {
	background: <?php echo $container_bg; ?>;
	box-sizing: border-box;
	padding: <?php echo newsletterglue_get_theme_option( 'container_padding1' ); ?>px <?php echo newsletterglue_get_theme_option( 'container_padding2' ); ?>px;
	<?php if ( $font_family ) : ?>
	font-family: <?php echo newsletterglue_get_font_name( $font_family ); ?>;
	<?php endif; ?>
}

h1, h2, h3, h4, h5, h6 {
	margin: 0 0 15px;
	padding-top: 20px;
	line-height: 1.5;
}

h1 { font-size: <?php echo newsletterglue_get_theme_option( 'h1_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h1_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h1_align' ); ?>; }
h2 { font-size: <?php echo newsletterglue_get_theme_option( 'h2_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h2_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h2_align' ); ?>; }
h3 { font-size: <?php echo newsletterglue_get_theme_option( 'h3_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h3_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h3_align' ); ?>; }
h4 { font-size: <?php echo newsletterglue_get_theme_option( 'h4_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h4_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h4_align' ); ?>; }
h5 { font-size: <?php echo newsletterglue_get_theme_option( 'h5_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h5_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h5_align' ); ?>; }
h6 { font-size: <?php echo newsletterglue_get_theme_option( 'h6_size' ); ?>px; color: <?php echo newsletterglue_get_theme_option( 'h6_colour' ); ?>; text-align: <?php echo newsletterglue_get_theme_option( 'h6_align' ); ?>; }

p, ul, ol {
	margin: 0 0 25px;
	font-size: 18px;
	line-height: 1.5;
}

p {
	font-size: <?php echo newsletterglue_get_theme_option( 'p_size' ); ?>px;
	color: <?php echo newsletterglue_get_theme_option( 'p_colour' ); ?>;
	text-align: <?php echo newsletterglue_get_theme_option( 'p_align' ); ?>;
}

a {
	color: <?php echo newsletterglue_get_theme_option( 'a_colour' ); ?> !important;
}

figure {
	margin: 0 0 25px;
}

img {
	max-width: 100%;
	margin: 0 auto 25px auto;
	display: block;
	height: auto;
}

h1 img,
h2 img,
h3 img,
h4 img,
h5 img,
h6 img,
p img {
	margin: auto;
	display: inline-block;
}

ul.blocks-gallery-grid {
	list-style-type: none;
}

#template_body td table {
	table-layout: fixed;
	width: 100%;
	border-collapse: collapse;
	border: 1px solid #dbdbdb;
}

#template_body td table td {
	width: 50%;
	padding: 10px;
	font-size: 16px;
	border: 1px solid #dcd7ca;
}

#template_body td table img {
	margin: 0;
}

.wp-block-columns .wp-block-column {
	display: inline-block;
	width: 45%;
	vertical-align: top;
	padding-right: 2%;
}

p.ngl-credits {
	font-size: 13px;
	text-align: center;
	color: #999 !important;
	padding-top: 70px;
}

p.ngl-credits a {
	color: #999 !important;
	text-decoration: underline;
}

.ngl-masthead {
	padding: 0;
}

.ngl-masthead-above {
	padding-top: 25px;
}

.wp-block-button.aligncenter,
.wp-block-buttons.aligncenter,
.wp-block-calendar {
	text-align: center;
}

.wp-block-button {
	padding: 0 0 25px;
}

.wp-block-button__link {
	display: inline-block;
	text-align: center;
	box-sizing: border-box;
	padding: 12px 24px;
	font-size: 16px;
	text-decoration: none;
	background-color: <?php echo newsletterglue_get_theme_option( 'btn_bg' ); ?> !important;
	color: <?php echo newsletterglue_get_theme_option( 'btn_colour' ); ?> !important;
	min-width: <?php echo (int) newsletterglue_get_theme_option( 'btn_width' ); ?>px !important;
	border: 1px solid <?php echo $border_color; ?> !important;
	border-radius: <?php echo (int) newsletterglue_get_theme_option( 'btn_radius' ); ?>px !important;
}

.ngl-hide-in-email {
	display: none !important;
	visibility: hidden !important;
}

.ngl-logo {
	padding: 20px 0;
}

.ngl-logo-center {
	text-align: center;
}

.ngl-logo-left {
	text-align: left;
}

.ngl-logo-right {
	text-align: right;
}

.ngl-logo-full {
	text-align: center;
}

.ngl-logo img {
	margin: 0 !important;
	display: inline-block !important;
	max-height: 100px;
	width: auto;
}

@media only screen and (max-width:596px) {

	body {
		line-height: 1.6;
	}

	#wrapper {
		padding-top: <?php echo newsletterglue_get_theme_option( 'mobile_container_margin' ); ?>px;
		padding-bottom: <?php echo newsletterglue_get_theme_option( 'mobile_container_margin' ); ?>px;
	}

	#template_inner {
		padding: <?php echo newsletterglue_get_theme_option( 'mobile_container_padding1' ); ?>px <?php echo newsletterglue_get_theme_option( 'mobile_container_padding2' ); ?>px;
	}

	h1, h2, h3, h4, h5, h6 {
		line-height: 1.6;
	}

	h1 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h1_size' ); ?>px; }
	h2 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h2_size' ); ?>px; }
	h3 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h3_size' ); ?>px; }
	h4 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h4_size' ); ?>px; }
	h5 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h5_size' ); ?>px; }
	h6 { font-size: <?php echo newsletterglue_get_theme_option( 'mobile_h6_size' ); ?>px; }

	p, ul, ol {
		font-size: <?php echo newsletterglue_get_theme_option( 'mobile_p_size' ); ?>px;
		line-height: 1.6;
	}

	img {
		width: auto;
		height: auto;
	}

	.wp-block-button__link {
		min-width: <?php echo (int) newsletterglue_get_theme_option( 'mobile_btn_width' ); ?>px !important;
	}

	.ngl-logo img {
		max-height: 60px;
	}

}

{custom_css}

<?php do_action( 'newsletterglue_add_custom_styles' ); ?>

</style>

<?php do_action( 'newsletterglue_newsletter_closing_head_tag' ); ?>

</head>
<body>

<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">

	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="template_wrapper">
		<tr>
			<td align="center" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
								<tr>
									<td></td>
									<td width="600" id="template_inner">
									{content}
									</td>
									<td></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</div>

</body>
</html>
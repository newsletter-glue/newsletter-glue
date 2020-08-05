<?php
/**
 * Email Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style type="text/css">

body {
	-webkit-text-size-adjust: 100%;
	line-height: 1.5;
}

h1, h2, h3, h4, h5, h6 {
	margin: 0 0 30px;
	padding-top: 20px;
	line-height: 1.5;
}

h1 { font-size: 32px; }
h2 { font-size: 28px; }
h3 { font-size: 24px; }
h4 { font-size: 22px; }
h5 { font-size: 20px; }
h6 { font-size: 18px; }

p, ul, ol {
	margin: 0 0 30px;
	font-size: 18px;
	line-height: 1.5;
}

figure {
	margin: 0 0 30px;
}

img {
	margin: 0 auto 30px auto;
	border-radius: 6px;
	display: block;
	max-width: 100%;
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
	border-radius: 0;
}

.wp-block-columns {

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
	color: #999;
	padding-top: 70px;
}

p.ngl-credits a {
	color: #999;
	text-decoration: underline;
}

.wp-block-button__link {
	padding: 12px 24px;
	font-size: 16px;
	text-decoration: none;
}

.wp-block-button__link.has-vivid-cyan-blue-background-color { background-color: #0693e3; }

@media only screen and (max-width:596px) {

	body {
		line-height: 1.6;
	}

	h1, h2, h3, h4, h5, h6 {
		line-height: 1.6;
	}

	h1 { font-size: 28px; }
	h2 { font-size: 24px; }
	h3 { font-size: 22px; }
	h4 { font-size: 20px; }
	h5 { font-size: 18px; }
	h6 { font-size: 16px; }

	p, ul, ol {
		font-size: 16px;
		line-height: 1.6;
	}

}

{custom_css}

</style>

</head>
<body>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="template_wrapper">
	<tr>
		<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container">
				<tr>
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
							<tr>
								<td></td>
								<td width="600">
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

</body>
</html>
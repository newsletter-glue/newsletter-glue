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

		<?php
			// CSS added by theme designer, custom css tab.
			do_action( 'newsletterglue_email_styles' );

			// CSS added by plugin blocks.
			if ( ! get_option( 'newsletterglue_disable_plugin_css' ) ) {
				do_action( 'newsletterglue_add_block_styles' );
			}

			// CSS added by 3rd party.
			do_action( 'newsletterglue_add_custom_styles' );
		?>

	</style>

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
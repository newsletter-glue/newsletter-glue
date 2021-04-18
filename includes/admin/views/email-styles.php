<?php
/**
 * Email Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="margin:0;padding:0">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="format-detection" content="telephone=no" />

	<title>{title}</title>

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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin-top: 0; margin-right: 0; margin-bottom: 0; margin-left: 0;padding-top: 0; padding-right: 0; padding-bottom: 0; padding-left: 0;" yahoo="fix">

	<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">

		<center>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="mso-table-lspace:0;mso-table-rspace:0" id="template_wrapper">
				<tr>
					<td align="center" valign="top" style="text-align:center;">
						<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" class="main" style="text-align: left; margin-left: auto; margin-right: auto;table-layout: fixed;" id="template_container">
							<tr>
								<td>
									<table width="600" id="template_table" align="center" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td align="center" id="template_inner" style="overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">{content}</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</center>

	</div>

</body>
</html>
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

	<?php do_action( 'newsletterglue_start_head_tag' ); ?>

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<style type="text/css">

		<?php do_action( 'newsletterglue_email_styles' ); ?>

		<?php do_action( 'newsletterglue_add_custom_styles' ); ?>

	</style>

	<?php do_action( 'newsletterglue_end_head_tag' ); ?>

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
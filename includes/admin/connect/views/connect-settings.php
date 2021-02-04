<?php
/**
 * MailerLite.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include_once NGL_PLUGIN_DIR . 'includes/integrations/' . $app . '/init.php';

$class 	= "NGL_" . ucfirst( $app );
$api 	= new $class;

?>

<div class="ngl-header"><?php echo esc_html( newsletterglue_get_name( $app ) ); ?></div>

<div class="ngl-fields">

	<form action="" method="get">

		<?php $api->get_connect_settings( $integrations ); ?>

		<div class="ngl-btn">
			<button class="ui primary button" type="submit"><i class="sync alternate icon"></i><?php esc_html_e( 'Connect', 'newsletter-glue' ); ?></button>
		</div>

	</form>

</div>

<div class="ngl-card-link-end">
	<a href="https://docs.newsletterglue.com/article/2-connect" target="_blank" class="ui basic noborder button"><?php esc_html_e( 'Learn more', 'newsletter-glue' ); ?><i class="arrow right icon"></i></a>
</div>
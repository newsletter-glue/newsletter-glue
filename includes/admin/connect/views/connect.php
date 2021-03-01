<?php
/**
 * Connect UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<?php do_action( 'newsletterglue_before_admin_connect' ); ?>

<div class="ngl ngl-wrap">

	<div class="ui large header">
		<?php esc_html_e( 'Connect', 'newsletter-glue' ); ?>
		<div class="sub header"><?php esc_html_e( 'Connect via API to your email software.', 'newsletter-glue' ); ?></div>
	</div>

	<?php include_once( 'connect-card.php' ); ?>

</div>
<?php
/**
 * Settings UI.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ui large header">
	<?php esc_html_e( 'Connect', 'newsletter-glue' ); ?>
	<div class="sub header"><?php esc_html_e( 'Connect via API to your email software.', 'newsletter-glue' ); ?></div>
</div>

<?php include_once( 'settings-connect-card.php' ); ?>
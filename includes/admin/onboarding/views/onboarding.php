<?php
/**
 * Onboarding Modal.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-modal-overlay onboarding">
<div class="ngl-modal">

	<?php

		include_once( 'welcome.php' );

		include_once( 'connect.php' );

		include_once( 'connected.php' );

		include_once( 'completed.php' );

	?>

	<div class="ngl-boarding-close"><a href="<?php echo newsletterglue_skip_onboarding_url(); ?>"><span class="material-icons">close</span></a></div>
	<div class="ngl-boarding-skip">
		<a href="#"><?php _e( 'Get help', 'newsletter-glue' ); ?></a>
		<a href="<?php echo newsletterglue_skip_onboarding_url(); ?>"><?php _e( 'Skip', 'newsletter-glue' ); ?></a>
	</div>

</div>
</div>
<?php
/**
 * Newsletter Metabox.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="ngl-metabox-flex">

	<div class="ngl-metabox-flex">
		<div class="ngl-metabox-header">
			<label for="ngl_lists"><?php esc_html_e( 'Campaign (List)', 'newsletter-glue' ); ?></label>
		</div>
		<div class="ngl-field">
			<?php
				$lists = '';
				if ( isset( $settings->lists ) ) {
					$lists = $settings->lists;
				} else {
					$lists = newsletterglue_get_option( 'lists', $app );
					if ( ! $lists ) {
						if ( $defaults->lists ) {
							$keys = array_keys( $defaults->lists );
							$lists = $keys[0];
						}
					}
				}

				newsletterglue()::$the_lists = $api->get_lists();
				$the_lists = newsletterglue()::$the_lists;

				newsletterglue_select_field( array(
					'id' 			=> 'ngl_lists',
					'legacy'		=> true,
					'helper'		=> __( 'Who receives your email.', 'newsletter-glue' ),
					'class'			=> 'is-required',
					'options'		=> $the_lists,
					'default'		=> $lists,
				) );
			?>
		</div>
	</div>

	<div class="ngl-metabox-flex"></div>

</div>